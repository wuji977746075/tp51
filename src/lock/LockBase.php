<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-11-02 08:54:23
 * Description : [Sciener base]
 * 推送关闭 lock_test
 */

namespace src\lock;

use src\base\BaseAction;
use src\lock\lock\Lock;
use src\lock\lock\LockKey;
use src\lock\lock\LockKeyboard;
use src\lock\lock\LockRecord;
use src\lock\lock\LockHis;
use src\user\member\MemberConfig;

// use app\system\api\MessageApi;
// use app\system\model\Message;
// use app\system\api\UserContractApi;
// use app\system\api\UserContractContentApi;
// use app\system\api\UcenterMemberApi;
use think\Db;
use \Exception as Exc;
use \ErrorCode as Ec;

class LockBase extends BaseAction {
  const PUSH    = false; // TRUE;  // 推送总开关,用与控制本地不推送
  const SCIENER = 6323;  // 科技侠
  const SITRI   = 6587;  // 微技术 百马锁
  protected $lock         = null; //lock model
  protected $lockKey      = null; //lockKey model
  protected $lockKeyboard = null; //lockKeyBoradLog model
  protected $lockRecord   = null; //lockRecord model
  protected $lockHis      = null; //lockHis model

  protected $config       = []; //global config
  protected $api_uri      = ''; //open_api_uri
  protected $lock_type    = 0; //config : lock type
  protected $pre          = ''; //config : lock and key prefix

  public function __construct() {
    // parent::__construct();
    $this->lock         = new Lock;
    $this->lockKey      = new LockKey;
    $this->lockKeyboard = new LockKeyboard;
    $this->lockRecord   = new LockRecord;
    $this->lockHis      = new LockHis;

    $this->lock_types = [
      self::SCIENER =>'sciener_',
      self::SITRI   =>'sitri_',
    ];
    $this->lock_configs = [
      $this->lock_types[self::SCIENER] => [ // 科技侠
        "api_uri"      => "https://api.sciener.cn/v3/",
        "app_id"       => "8eb7a4d64fb8422fb23d0628a7e8cc45",
        "app_secret"   => "8b48fcd2128fc357fe036367c3f7a7a4",
        "lock_type"    => self::SCIENER,
        "redirect_uri" => "http://api.ihomebank.com/public/index.php/sciener",
        "token_url"    => "https://api.sciener.cn/oauth2/token",
      ],
      // api不给了大部分功能交sdk, 开锁记录接口需要
      // shopcloud.wanliantek.com 39.104.83.239
      $this->lock_types[self::SITRI]=>[   // 微技术
        "api_uri"      => "https://39.104.83.239/business/comlock/",
        "app_id"       => "zhujiazulinclient",
        "app_secret"   => "zhujiazulinclient",
        "lock_type"    => self::SITRI,
        "redirect_uri" => '',
        "token_url"    => "https://39.104.83.239/oauth/v2/token",
      ]
    ];
  }

  // 添加智能锁操作记录
  // uid  to_uid
  // op: ''   rs: 1/0
  // extra: str/arr
  // aid : admin uid
  // @return int
  public function addHisLog($uid,$to_uid=0,$lock_id='',$key_id='',$op='',$rs=1,$extra='',$aid=0){
    if(mb_strlen($op,'utf-8')>16) $this->err('操作类型过长:'.$op);
    $map = [
      'uid'         =>$uid,
      'to_uid'      =>$to_uid,
      'operate'     =>$op,
      'result'      =>intval($rs),
      'extra'       =>substr(json_encode($extra),0,255),
      'lock_id'     =>$lock_id,
      'key_id'      =>$key_id,
      'aid'         =>$aid,
      'create_time' =>time(),
    ];
    //bug : db('locks_his')的无法回滚
    return $this->lockHis->insertGetId($map);
  }
  public function setHisLog($id,$map=['result'=>1]){
    $this->lockHis->where('id',$id)->update($map);
  }

  // 检查百马密码位
  public function checkPwdPos($pwd_list,$t=''){
    empty($pwd_list) && $this->err('需要密码pwd_list');
    if($t=='admin'){
      $startPos = 1;$endPos=50;$err = '非管理员密码位';
    }elseif($t=='rent'){
      $startPos = 51;$endPos=55;$err = '非租户密码位';
    }else{
      $this->err('非法操作:'.$t);
    }
    foreach ($pwd_list as $v) {
      (!isset($v['pwd']) || !isset($v['pwd_id'])) && $this->err('pwd_list格式错误');
      $pwd_id = intval($v['pwd_id']);
      if($pwd_id < $startPos or $pwd_id >$endPos) $this->err($err);
    }
  }

  public function isValidKey($key_id){
    $now = time();
    return $this->getLockKey(['key_id'=>$key_id,'start'=>[['lt',$now],['eq',0],'or'],'end'=>[['gt',$now],['eq',0],'or'],'status'=>['in',[LockKey::OK,LockKey::WAIT]]],'update_time desc','*',false,'非有效钥匙');
  }

  public function isAdminKey($uid,$key_id){
    $key_info = $this->getLockKey(['uid'=>$uid,'key_id'=>$key_id,'status'=>LockKey::OK]);
    return intval($key_info['type']) === LockKey::ADMIN;
  }

  // 是否为科技侠
  // @return  bool
  public function isSciener($id=''){
    $id && $this->setConfig($id);
    return $this->lock_type == self::SCIENER;
  }
  // 是否为微技术
  // @return  bool
  public function isSitri($id=''){
    $id && $this->setConfig($id);
    return $this->lock_type == self::SITRI;
  }
  // 检查类型是否合法
  // @return  lock_pre
  protected function checkLockType($lock_type=0){
    if(empty($this->lock_types[$lock_type])){
      $this->err($lock_Type,Ec::Param_E);
    }
    return $this->lock_types[$lock_type];
  }
  // 获取锁类型id
  // @param $id 锁id或钥匙id
  // @return  lock_type
  protected function getLockType($id=0){
    if(strpos($id,$this->lock_types[self::SCIENER])!== false) return self::SCIENER;
    elseif(strpos($id,$this->lock_types[self::SITRI])!== false) return self::SITRI;
    else return 0;
  }

  // 获取密码类型描述
  // @param $id 锁id或钥匙id
  // @return  pwd_type_desc
  protected function getPwdTypeDesc($id,$pwd_id=0,$type){
    $desc = '';
    if($this->getLockType($id) == self::SITRI){
      if($pwd_id < 51){
        $desc='管理员密码';
      }elseif($pwd_id < 56){
        $desc='租户密码';
      }
    }else{
      $desc = LockKey::getScienerTypeDesc($type);
    }
    return $desc;
  }
  // 检查锁类型 并返回锁ID/钥匙id 前缀
  // 每次调用远程接口需要调用
  // @return void
  protected function setConfig($id){
    $this->pre = $this->getOriPre($id);
    if(isset($this->lock_configs[$this->pre])){
      $this->config    = $this->lock_configs[$this->pre];
      $this->api_uri   = $this->config['api_uri'];
      $this->lock_type = $this->config['lock_type'];
    }else{
      $this->err($id,Ec::Param_E);
    }
  }

  // @return void
  public function checkErr($r,$code=Ec::Error,$nullerr=''){
    if(!$r['status']) $this->err($r['info'],$code);
    if($nullerr && empty($r['info'])) $this->err($nullerr,$code);
  }
  // @return void
  public function checkDbErr($r,$model,$trans=false,$nullerr='',$fullerr=''){
    if(false === $r){
      if($trans) Db::rollback();
      $this->err($model->getError());
    }
    if($nullerr && empty($r)){
      if($trans) Db::rollback();
      $this->err($nullerr? $nullerr : $model->getError());
    }
    if($fullerr && !empty($r)){
      if($trans) Db::rollback();
      $this->err($model->getError());
    }
  }
  // 获取租户租房信息
  // bug : fix 查询了键名key+其他 2018-01-18 14:10:50
  // @retrun array[start,end]
  protected function getRentTime($uid,$house_no=''){
    !$house_no && $this->err('未发现房源');
    // ? uid
    $r = (new UcenterMemberApi)->getInfo(['id'=>$uid]);
    $this->checkErr($r,0,'未发现管理员信息');
    // ? 租户
    $r = (new UserContractApi)->getInfo(['first_party_uid'=>$uid,'house_no'=>$house_no,'status'=>0]);
    $this->checkErr($r,0,'未发现有效合同');
    $code = $r['info']['contract_no'];
    $rent_uid = $r['info']['second_party_uid'];
    $r = (new UcenterMemberApi)->getInfo(['id'=>$rent_uid]);
    $this->checkErr($r,0,'未发现租户信息');
    $mobile = $r['info']['mobile'];
    // ? 租期
    $map = ['contract_no'=>$code,'key'=>['in',['start_date','end_date']]];
    $r = (new UserContractContentApi)->query($map);
    // $r = (new UserContractContentApi)->query($map,false,'key,value'); // bug : key 键名
    $this->checkErr($r,0,'未发现合同租期');
    $start = 0;$end = 0;
    foreach ($r['info']['list'] as $v) {
      if($v['key'] == 'start_date') $start = strtotime($v['value']);
      if($v['key'] == 'end_date')   $end   = strtotime($v['value'])+86400;
    }
    if(!$start || !$end ) $this->err('租期错误');

    return ['start'=>$start,'end'=>$end,'mobile'=>$mobile];
  }
  // 获取锁信息
  // @retrun apiReturn
  protected function getLock($map,$order=false,$field=null,$list=false,$checknull=true){
    if(true === $checknull) $checknull = 'lock_id错误或非管理员';
    return $this->getModel($this->lock,$map,$order,$field,$list,$checknull);
  }

  // 获取钥匙信息
  // @retrun apiReturn
  protected function getLockKey($map,$order=false,$field=null,$list=false,$checknull=true){
    if(true === $checknull) $checknull = 'key_id错误或uid错误';
    return $this->getModel($this->lockKey,$map,$order,$field,$list,$checknull);
  }
  // 获取密码信息
  // @retrun apiReturn
  protected function getLockKeyboard($map,$order=false,$field=null,$list=false,$checknull=true){
    if(true === $checknull) $checknull = '参数错误';
    return $this->getModel($this->lockKeyboard,$map,$order,$field,$list,$checknull);
  }

  // 获取开锁记录信息
  // @retrun apiReturn
  protected function getLockRecord($map,$order=false,$field=null,$list=false,$checknull=true){
    if(true === $checknull) $checknull = '参数错误';
    return $this->getModel($this->lockRecord,$map,$order,$field,$list,$checknull);
  }
  // 获取操作历史
  // @retrun apiReturn
  protected function getLockHis($map,$order=false,$field=null,$list=false,$checknull=true){
    if(true === $checknull) $checknull = '参数错误';
    return $this->getModel($this->lockHis,$map,$order,$field,$list,$checknull);
  }

  // 获取密码时 检查科技侠键密版本 和修正结束时间
  // @retrun int 结束时间
  protected function checkKbAndFixTime($kb_version,$pwd_type,$start,$end){
    // 键盘1,2,3不再支持
    $kb_version !=4 && $this->err('非4代键盘:'.$kb_version);
    // $pwd_type
    // 1:once(6小时内有效)
    // 2:永久(24小时内需用一次)
    // 3:限时(生效后24小时内需用一次)
    // 4:删除(24小时有效,锁上使用过的密码将失效)
    if(!in_array($pwd_type, [1,2,3,4])) $this->err('密码类型'.$pwd_type.'此锁不支持');
    switch ($pwd_type) {
      case 1:
        $end = $start + 3600*6;   break;
      case 2:
        $end = 0;                 break;
      case 4:
        $end = $start + 3600*24;  break;
      default:                    break;
    }
    return $end;
  }

  // 是否有非管理员钥匙
  // @return array key_info
  protected function hasUnAdminKey($lock_id,$null_err='请先重置非管理员钥匙'){
    return $this->getLockKey(['lock_id'=>$lock_id,'type'=>['neq',LockKey::ADMIN],'status'=>['in',[LockKey::OK,LockKey::WAIT,LockKey::FROZE]]],'update_time desc','*',false,$null_err);
  }

  // 根据手机号 关联获取用户信息
  protected function getInfoByMobile($mobile='',$field='mc.uid',$null_err=''){
    $mc = new MemberConfig;
    $r = $mc->alias('mc')->field($field)
      ->join(['ucenter_member'=>'um'],'mc.uid=um.id','left')
      ->where('um.mobile',$mobile)->find();
    $this->checkDbErr($r,$mc,false,$null_err);
    return $r;
  }
  // 是否是租户
  // @return array key_info
  protected function isRent($uid,$lock_id,$null_err='非租户'){
    return $this->getLockKey(['lock_id'=>$lock_id,'type'=>LockKey::RENT,'uid'=>$uid,'status'=>['in',[LockKey::OK,LockKey::WAIT,LockKey::FROZE]]],'update_time desc','*',false,$null_err);
  }
  // 是否为有效的租户
  // @return array key_info
  protected function isValidRent($uid,$lock_id,$null_err='非有效租户'){
    $now = time();
    return $this->getLockKey(['lock_id'=>$lock_id,'type'=>LockKey::RENT,'uid'=>$uid,'status'=>['in',[LockKey::OK,LockKey::WAIT]],'start'=>[['lt',$now],['eq',0],'or'],'end'=>[['gt',$now],['eq',0],'or']],'update_time desc','*',false,$null_err);
  }

  // 是否有租户 : 同步时最好删除已删除和已重置状态的钥匙
  // @return array key_info
  protected function hasRent($lock_id,$null_err='未发现租户'){
    return $this->getLockKey(['lock_id'=>$lock_id,'type'=>LockKey::RENT,'status'=>['in',[LockKey::OK,LockKey::WAIT,LockKey::FROZE]]],'update_time desc','*',false,$null_err);
  }
  // 是否有有效租户
  // @return array key_info
  public function hasValidRent($lock_id,$null_err='未发现有效租户'){
    $now = time();
    $where = "`lock_id`='".$lock_id."'"
    ." and `type`=".LockKey::RENT
    ." and (`start`<".$now ." or `start`=0)"
    ." and (`end`>".$now ." or `end`=0)"
    ." and status in(".LockKey::OK.",".LockKey::WAIT.")";
    return $this->getLockKey($where,'update_time desc','*',false,$null_err);
  }

  // 重置百马密码
  // todo : 可优化删除不同的,修改共同的
  protected function resetSitriPwd($uid,$lock_id,$pwdInfo='',$delAll=false,$app_time){
    // addTestLog('resetSitriPwd','pwdInfo',$pwdInfo);
    !$this->isSitri($lock_id) && $this->error('非百马锁');

    $pwdInfo = is_array($pwdInfo) ? $pwdInfo : json_decode($pwdInfo,true);
     empty($pwdInfo) && $this->err('pwdInfo格式错误');
    // 删除现有密码
    $map = ['lock_id'=>$lock_id];
    !$delAll && $map['keyboard_id'] = ['lt',51]; //保留租户密码
    $this->lockKeyboard->where($map)->delete();

    $adds = [];
    foreach ($pwdInfo as $v) {
      !is_array($v) && $this->err('pwdInfo格式错误');
      //id,lockMac,lockPwd,pwdPosition,status,type,remark,
      //startDate,endDate,createtime,updatetime
      $adds[] = [
        'lock_id'        =>$lock_id,
        'keyboard_id'    =>$v['pwdPosition'],
        'keyboard_pwd'   =>$v['lockPwd'],
        'type'           =>2, //永久
        'send_time'      =>0,
        'start'          =>0,
        'end'            =>0,
        'uid'            =>$uid,
        'to_uid'         =>0,
        'app_time'       =>$app_time,
        'alias'          =>'',
      ];
    }
    $adds && $this->lockKeyboard->saveAll($adds);
  }
  // 租户用户是否能获取密码
  protected function checkRentPass($uid,$lock_id,$start,$end){
    if($start<1 || $end<1) $this->err('只能发送限时密码');
    if($start>=$end) $this->err('时限错误');
    // 不得超过租户钥匙期限
    $r = $this->isValidRent($uid,$lock_id);
    if($start<intval($r['start']) || $end>intval($r['end'])) $this->err('不得超过租户钥匙期限');
    $limit = LockKey::RENT_PASS_DAY_LIMIT;
    if($end - $start>$limit*86400) $this->err('租户密码不得超过'.$limit.'天');
    // 发送限制
    $limit =  LockKey::RENT_PASS_LIMIT;
    $r = $this->lockKeyboard->where(['uid'=>$uid,'lock_id'=>$lock_id])->count();
    if(!$r || intval($r)>=$limit) $this->err('您最多只能发送'.$limit.'个');
  }
  // find/select 查询语句封装
  // @retrun array
  protected function getModel($model,$map,$order=false,$field=null,$list=false,$checknull=''){
    $r = $model->where($map)->field($field)->order($order);//->fetchSql(true);
    $r = $list ? $r->select() : $r->find();
    if(false === $r)  $this->err($model->getError());
    if($checknull && empty($r)) $this->err($checknull);
    return $r;
  }

  protected function err($msg,$code=0,$log=false){
    $code = $code ? $code : EC::LOCK_ERROR;
    // if($log) addTestLog($code,$msg,'');
    parent::err('Lock:'.$msg,$code,[]);
  }

  protected function pushMulti($data=[]){
    foreach ($data as $v) {
      if(is_array($v) && count($v)>1){
        $v[2] = isset($v[2]) ? $v[2] : '';
        $this->pushLockMessage($v[0],$v[1],$v[2]);
      }
    }
  }
  // push msg
  // todo : 考虑加到redis消息队列
  // @return void
  protected function pushLockMessage($msg,$to_uid,$summary=''){
    if(!self::PUSH) return ; // local not push
    if($to_uid){
      $msg = trim($msg);
      $entity = [
       'from_id' =>0,
       'title'   =>Message::MESSAGE_LOCK,
       'content' =>$msg,
       'summary' =>$summary ? $summary : $msg,
       'extra'   =>'', //消息记录中的
      ];
      $after_open = ['type'=>'go_activity','param'=>Message::MESSAGE_LOCK_ACTIVITY,'extra'=>[]];
      $r = (new MessageApi)->pushMessageAndRecordWithType(Message::MESSAGE_LOCK,$entity,$to_uid,false,$after_open);
      // $this->checkErr($r); //发送失败 不报错
    }
  }

  // rebuild page date
  // @return array
  protected function parsePager($r){
    $l = [];
    $l['total'] = $r->paginator->options['list_rows'];
    $l['data'] = Obj2Arr($r->items);
    return $l;
  }

  // 用户锁权限 钥匙状态有限期由APP控制
  // lockOpen
  // locklogList
  // keyReset keySend keyList keyDel keyFroze keyRename
  // passSend passList
  // protected function getRightByKey($key_id){
  //   $ret = [];
  //   $r = $this->getLockKey(['key_id'=>$key_id,'status'=>LockKey::OK]);
  //   if($r){
  //     $type = (int) $r['type'];
  //     $isAdminKey   = $type == LockKey::ADMIN;
  //     $isRentKey    = $type == LockKey::REND;
  //     $hasValidRent = true; // 是否有有效期内的租户钥匙
  //     if($isAdminKey){ // 管理员钥匙
  //       $ret = ['keyList','keyDel','keyFroze','keyRename','locklogList','keyReset','passReset'];
  //       if($hasValidRent){
  //       }else{
  //         $ret = array_merge($ret,['lockOpen','keySend','passSend','passList']);
  //       }
  //     }elseif($isRentKey){ // 租户钥匙
  //       $ret = ['lockOpen','passSend','passList','keyList','keyDel','keyFroze','keyRename','locklogList'];
  //     }else{ // 时限钥匙 普通钥匙
  //       $ret = ['lockOpen','locklogList'];
  //     }
  //   }
  //   return $ret;
  // }

  // 获取13位时间戳
  // @return long
  protected function getMicroTime($time=0){
    return intval(1000*microtime(true));
  }

  // 智能锁账号注册,注意，因为有原账号绑定
  // @return string
  protected function getName($uid){
    return md5($uid);
  }

  // 智能锁账号密码加密
  // @return string
  protected function getPass($uid=0,$pass='itboye'){
    return md5($pass);
  }

  // $id : 锁或钥匙ID,带品牌前缀
  // @return string 原始或原ID
  protected function getOriId($id){
    $r = strpos($id,'_');
    return (false === $r)? $id: substr($id,(int)$r+1);
  }
  // $id : 锁或钥匙ID,带品牌前缀
  // @return string 原始或品牌前缀
  protected function getOriPre($id){
    $r = strpos($id,'_');
    return (false === $r)? $id: substr($id,0,(int)$r+1);
  }

  // 获取智能锁账号信息
  // @return array
  public function getMemberConfig($uid){
    $pre = 'sciener_';
    if(empty($pre) || !in_array($pre,array_values($this->lock_types))) $this->err('pre 非法 或 未配置');

    //TODO 缓存
    $mc = new MemberConfig;
    $r = $mc->field($pre.'username as name,'.$pre.'password as pass,last_update_time')->where('uid',$uid)->find();
    if(false === $r) $this->err($mc->getError());
    if(empty($r))    $this->err('uid错误');
    if(empty($r['pass']) || empty($r['name']))  $this->err('请先绑定'.$pre.'账号');
    return $r;
  }

  // 使用前需要 setCongfig
  // @return apiReturn
  public function getAccessToken($name,$pass,$cache = true){
    if(empty($this->config) || empty($this->lock_type)) $this->err('未设置 config');

    $cache_key = serialize('lock'.$this->lock_type.'_token_'.$name);
    if($cache){
      $cache_value = cache($cache_key);
      if($cache_value) return $cache_value;
    }
    //TODO 保存到数据库 过期再调用refreshToken更新 ?
    //TODO 保存open_id sciener_openid  ?
    //only test
    // if($name=='18267152148'){
    //   return [
    //     "access_token"  => "e5ad0af6de13a14f713fe1daaa79f177",
    //     "refresh_token" => "b580d25d06ea379130b89fcea9e91ae6",
    //     "openid"        => 1229304871,
    //     "scope"         => "user,key,room",
    //     "expires_in"    => 7776000,
    //   ];
    //   [
    //    "code"=>'',
    //    "message"=>'',
    //    "data"=>[
    //      'token_type'=>'Bearer',
    //      'expires_in'=>180*3600*24,
    //      'refresh_token'=>'',
    //      'access_token' =>'',
    //    ]
    //   ]
    // }
    $url = $this->config['token_url'];
    $param = [
      'client_id'     => $this->config['app_id'],
      'client_secret' => $this->config['app_secret'],
      'grant_type'    => 'password',
      'username'      => $name,
      'password'      => $pass,
      'redirect_uri'  => $this->config['redirect_uri'], // 科技侠
      'scope'         => 'read', // 微技术
    ];
    // addTestLog($url,$param,'-- token --');
    $r = $this->curl_post($url,$param);
    // addTestLog($url,$param,$r);
    isset($r['data']) && $r = $r['data']; // 兼容微技术
    if(!isset($r['expires_in'])) $this->err('需要 expires_in');
    $expires_in = min($r['expires_in'],3600);
    if($cache) cache($cache_key,$r,$expires_in);
    return $r;
  }
  //ucenter_member
  protected function getUserInfo($map,$field=false){
    $r = sdb('itboye_ucenter_member','')
    ->where($map)->find();
    if(empty($r)) $this->err('获取用户信息出错');
    return $r;//->getData();
  }

  // 解绑百马锁 否则无操作
  protected function unbindSitriLock($uid,$lock_id,$lock_mac){

    if($this->isSitri($lock_id)){
      // $r = $this->getAccessToken($uid,'itboye');
      $url = $this->api_uri.'device/unbind';
      $param = [
        'lockMac' =>$lock_mac,
        // 'access_token' =>$r['access_token'],
      ];
      addTestLog($url,$param,'--unbind--');
      try{
        $r = $this->curl_post($url,$param);
      }catch(Exc $e){
        if(substr($e->getMessage(),0,4) == '-45:'){
        }else{
          throw $e;
        }
        // addTestLog($url,$param,$e);
      }
      // addTestLog($url,$param,$r);
    }
  }

  // $throw : curl请求成功业务失败不保存
  protected function curl_post($url,$param,$timeout=5,$throw=true){
    $timeout = max(5,(int)$timeout);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);            //定义超时5秒钟
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
    // curl_getinfo($ch);
    $r = curl_exec($ch);
    curl_close($ch);
    $r = json_decode($r,true);
    if(empty($r)) $this->err($url.':curl-return-null');
    $this->parseErr($r,$throw);
    if(isset($r['code']) && isset($r['message']) && isset($r['data'])){
      // 微技术
      $r = $r['data'];
    }
    return $r;
  }

  // 解析已知格式的返回错误
  protected function parseErr($r,$throw=true){
    //, 科技侠报错 {不定}
    if(isset($r['errcode']) && $r['errcode']){
      $str = $r['errcode'];
      if(isset($r['errmsg']))      $str .= ':'.$r['errmsg'];
      if(isset($r['description'])) $str .= ':'.$r['description'];
      addTestLog('curl_post',$r,'return错误');
      $throw && $this->err($str);
    }
    //? 微技术报错 {code,message,data}
    if(isset($r['code']) && $r['code']){
      // $r['data'] = isset($r['data']) ? $r['data'] : 'datanull';
      // if(!isset($r['data'])){
      //   $this->err('需要data');
      // }
      $str = $r['code'];
      if(isset($r['message'])) $str .= ':'.$r['message'];
      addTestLog('curl_post',$r,'return错误');
      $throw &&  $this->err($str);
    }
  }
  //memberconfig common_member
  protected function getCacheUserInfo($map,$field='mc.uid,nickname',$time=1800,$fresh=false,$errmsg='获取用户信息出错'){
    $key = getCacheKey($map,'lock_uinfo');
    if(false === $fresh){
        $cache = cache($key);
        if($cache) return $cache;
    }
    $m = new MemberConfig;
    $r = $m->alias('mc')->field($field)->where($map)->join(['common_member'=>'cm'],'cm.uid=mc.uid','left')->find();
    // addTestLog('getCacheUserInfo',$map,$r);
    if(false === $r) $this->err($m->getError());
    if(empty($r)){
      if($errmsg) $this->err($errmsg);
    }else{
      $r = $r->getData();
    }
    $r = $r ? $r : ['uid'=>0,'nickname'=>''];
    if($cache) cache($key,$r,$time);
    return $r;
  }
}