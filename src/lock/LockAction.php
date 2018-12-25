<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-11-02 08:51:27
 * Description : [智能锁业务 整合]
 * V3 => 通用接口 的适配器 2018-08-02 14:33:43
 */

namespace src\lock;

use think\Db;
use src\user\member\MemberConfig;
use src\lock\lock\LockKey;
use src\lock\lock\LockIcCardLogic as LockIcCardApi;
use src\lock\lock\HouseTagLogic as HouseTagApi;
use src\lock\lock\LockHisLogic as LockHisApi;
use src\lock\lock\LockIcCard;
// use app\system\api\MessageApi;
// use app\system\model\Message;

/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\
 * @example
 */
class LockAction extends LockBase{
  /**
   * 管理员转移 - 科技侠
   * @param  array $params: uid,lock_id,to_mobile
   * @return Exception|string
   */
  function changeAdmin($params){
    $this->pre = 'sciener_';
    $this->setConfig($this->pre);
    extract($params);
    $r = $this->getLock(['lock_id'=>$lock_id,'owner_uid'=>$uid]);
    $lock_name = strPlus($r['lock_name'],$r['lock_alias']);
    // 转移给谁
    $r = $this->getInfoByMobile($to_mobile,'sciener_username as name,last_update_time,mc.uid','接收用户非法');
    $to_uid  = intval($r['uid']);
    $to_sciener_name = $r['name'];
    empty($r['name']) && $this->err('该用户未注册智能锁');
    // 下面用
    $last_update_time = intval($r['last_update_time']);
    // 不得发给自己,管理员
    if($to_uid == $uid) $this->err('不得发给自己');
    // ? to_uid是否存在这把锁的钥匙
    // $this->err('暂未实现');
    $r = $this->getMemberConfig($uid);
    $r = $this->getAccessToken($r['name'],$r['pass']);
    $url = $this->api_uri.'lock/transfer';
    $param = [
      'clientId'    => $this->config['app_id'],
      'accessToken' => $r['access_token'],
      'lockIdList'  => '['.$this->getOriId($lock_id).']',
      'receiverUsername' => $to_sciener_name,
      'date'        => $this->getMicroTime(),
    ];
    $r = $this->curl_post($url,$param);
    // $r = ['status'=>true,'info'=>$r];
    // addTestLog($url,$param,$r);
    // update lock
    $this->lock->where(['lock_id'=>$lock_id])->update(['owner_uid'=>$to_uid]);
    // delete uid key
    $this->lockKey->where(['lock_id'=>$lock_id,'uid'=>$uid])->delete();
    // sync to_uid key
    $this->syncKey($to_uid,$last_update_time,$this->pre);
    // lock_keypass uid => to_uid
    $this->lockKeyboard->where(['uid'=>$uid])->update(['uid'=>$to_uid]);
    // log
    $this->addHisLog($uid,$to_uid,$lock_id,'',LockHisApi::ACTION_CHANGE_ADMIN,1,$to_mobile);
    // push
    $this->pushLockMessage('您的智能锁'.$lock_name.'的管理员钥匙已转移',$uid);
    $this->pushLockMessage('您获得智能锁'.$lock_name.'的一把管理员钥匙,请务必手动重置密码.',$to_uid);
    return '转移成功';
  }
  /**
   * 获取用户openid - 科技侠
   * @param  int $uid
   * @return Exception|array
   */
  function getOpenId($uid){
    $this->setConfig('sciener_');
    $r = $this->getMemberConfig($uid);
    return $this->getAccessToken($r['name'],$r['pass']);
  }
  /**
   * 获取IC卡列表 - local+admin - 科技侠
   * @param array $params : uid,lock_id,page,size
   * @return Exception|array
   */
  function listIcCards($params){
    $this->setConfig('sciener_');
    extract($params);
    $size = min(max($size,20),100);
    $this->getLock(['lock_id'=>$lock_id,'owner_uid'=>$uid]);
    if($remote){
      $r = $this->getMemberConfig($uid);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      $url = $this->api_uri.'identityCard/list';
      $param = [
        'clientId'    => $this->config['app_id'],
        'accessToken' => $r['access_token'],
        'lockId'      => $this->getOriId($lock_id),
        'pageNo'      => $page,
        'pageSize'    => $size,
        'date'        => $this->getMicroTime(),
      ];
      $r = $this->curl_post($url,$param);
      $r = ['status'=>true,'info'=>$r];
      // addTestLog($url,$param,$r);
    }else{
      $r = (new LockIcCardApi)->queryWithCount(['lock_id'=>$lock_id],['curpage'=>$page,'size'=>$size],'id desc');
    }
    return $r;
  }

  /**
   * 添加ic卡 - remote+admin - 科技侠(蓝牙)
   * @param  array $params : uid,lock_id,card_no start,end,card_name
   * @return Exception|int
   */
  function addIcCard($params){
    $this->setConfig('sciener_');
    extract($params);
    $r = $this->getLock(['lock_id'=>$lock_id,'owner_uid'=>$uid]);
    $lock_name = strPlus($r['lock_name'],$r['lock_alias']);
    $r = $this->getMemberConfig($uid);
    $r = $this->getAccessToken($r['name'],$r['pass']);
    $url = $this->api_uri.'identityCard/add';
    $param = [
      'clientId'    => $this->config['app_id'],
      'accessToken' => $r['access_token'],
      'lockId'      => $this->getOriId($lock_id),
      'cardNumber'  => $card_no,
      // 'addType'    => 1, //1:蓝牙(d),2:网关
      'startDate'   => $start * 1000,
      'endDate'     => $end * 1000,
      'date'        => $this->getMicroTime(),
    ];
    $r = $this->curl_post($url,$param);
    // array (
    //   'cardId' => 77020,
    // )
    // addTestLog($url,$param,$r);
    $cardId = $r['cardId'];
    // 添加到数据库
    $upd = [
      'lock_id'    => $lock_id,
      'card_name'  => $card_name,
      'cardId'     => $cardId,
      'uid'        => $uid,
      'start'      => $start,
      'end'        => $end,
    ];
    $info = (new LockIcCardApi)->getInfo(['cardNumber'=>$card_no]);
    if(!$info['status']) return $info;
    if($info['info']){
      $r = (new LockIcCardApi)->save(['cardNumber'=>$card_no],$upd);
    }else{
      $upd['cardNumber'] = $card_no;
      $r = (new LockIcCardApi)->add($upd);
    }
    // log
    $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_ICCARD_ADD);
    // push
    $this->pushLockMessage('您的智能锁'.$lock_name.'的添加了一张IC卡',$uid);
    return $r;
  }
  /**
   * 删除ic卡 - remote+admin - 科技侠(蓝牙)
   * @param array $params : uid,lock_id,cardId
   * @return Exception|array
   */
  function delIcCard($params){
    $this->setConfig('sciener_');
    extract($params);
    $r = $this->getLock(['lock_id'=>$lock_id,'owner_uid'=>$uid]);
    $lock_name = strPlus($r['lock_name'],$r['lock_alias']);
    $r = $this->getMemberConfig($uid);
    $r = $this->getAccessToken($r['name'],$r['pass']);
    $url = $this->api_uri.'identityCard/delete';
    $param = [
      'clientId'    => $this->config['app_id'],
      'accessToken' => $r['access_token'],
      'lockId'      => $this->getOriId($lock_id),
      'cardId'      => $cardId,
      // 'deleteType'    => 1, //1:蓝牙(d),2:网关
      'date'        => $this->getMicroTime(),
    ];
    $r = $this->curl_post($url,$param);
    // addTestLog($url,$param,$r);
    // log
    $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_ICCARD_DEL);
    // push
    $this->pushLockMessage('您的智能锁'.$lock_name.'的失去了一张IC卡',$uid);
    // local
    (new LockIcCardApi)->delete(['cardId'=>$cardId]);
    return '操作成功';
  }
  /**
   * 清空ic卡 - remote+admin+local - 科技侠(蓝牙)
   * @param array $params : uid,lock_id,cardId
   * @return Exception|array
   */
  function clearIcCard($params){
    $this->setConfig('sciener_');
    extract($params);
    $r = $this->getLock(['lock_id'=>$lock_id,'owner_uid'=>$uid]);
    $lock_name = strPlus($r['lock_name'],$r['lock_alias']);
    $r = $this->getMemberConfig($uid);
    $r = $this->getAccessToken($r['name'],$r['pass']);
    $url = $this->api_uri.'identityCard/clear';
    $param = [
      'clientId'    => $this->config['app_id'],
      'accessToken' => $r['access_token'],
      'lockId'      => $this->getOriId($lock_id),
      'date'        => $this->getMicroTime(),
    ];
    $r = $this->curl_post($url,$param);
    // log
    $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_ICCARD_CLEAR);
    // addTestLog($url,$param,$r);
    (new LockIcCardApi)->delete(['lock_id'=>$lock_id]);
    // push
    $this->pushLockMessage('您的智能锁'.$lock_name.'的IC卡已清空',$uid);
    return '操作成功';
  }

  // 钥匙列表 - admin
  // params : '','kword,current_page|1|int,per_page|10|int,order|create_time desc,lock_id'
  public function viewKey($params){
    extract($params);
    $map = [];
    if($lock_id) $map['k.lock_id'] = $lock_id;
    $r = $this->lockKey->alias('k')
    ->join(['common_member'=>'cm'],'k.uid=cm.uid','left')
    ->join(['itboye_locks'=>'l'],'l.lock_id=k.lock_id','left')
    ->field('k.*,cm.nickname,l.owner_uid')
    ->where($map)->order('k.'.$order)->paginate($per_page,false,['page'=>$current_page]);
    // $this->checkDbErr($r,$this->lockKey);
    $r = $r->toArray();
    //钥匙状态  //接受者  昵称+类型?
    foreach ($r['data'] as &$v) {
      $v['key_status'] = getKeyStatus($v['status']);
      $v['type_desc']  = getKeyType($v['type']);
      // 兼容V3 -->
      // $v['auth_type'] = $v['type'];
      // if($v['auth_type'] ===0)     $v['user_type'] = '管理员';
      // elseif($v['auth_type'] ===1) $v['user_type'] = '租户';
      // else{
      //   $v['auth_type'] = 2;
      //   $v['user_type'] = '普通用户';
      // }
      // <-- 兼容V3
    } unset($v);
    return $r;
  }
  // 锁列表 - admin
  // params : '','kword,current_page|1|int,per_page|10|int,order|create_time desc,uid|0|int,house_no,lock_type'
  public function view($params){
    extract($params);
    $map = [];
    if($uid)       $map['l.owner_uid'] = $uid;
    if($house_no)  $map['l.house_no']  = $house_no;
    if($lock_type) $map['l.lock_type']  = $lock_type;
    if($kword)     $map['l.lock_mac|l.lock_alias'] = ['like','%'.$kword.'%'];
    $r = $this->lock->alias('l')
    ->join(['common_member'=>'m'],'l.owner_uid =m.uid','left')
    ->join(['common_datatree'=>'d'],'l.lock_type =d.id','left')
    ->where($map)->order($order)
    ->field('l.lock_id,l.lock_mac,l.lock_name,l.lock_alias,l.owner_uid as uid,l.create_time,l.push,m.nickname,l.house_no,l.lock_type,d.name as lock_type_name,l.last_adjust')
    // ->select();
    ->paginate($per_page,false,['page'=>$current_page]);
    // $this->checkDbErr($r,$this->lock);
    return $r->toArray();
  }

  // 查询科技侠键盘版本
  // uid|0|int,lock_id //lockFlagPos|-1|int
  // return int
  public function getKbVersion($lock_id,$uid){
    $r = $this->getLock(['lock_id'=>$lock_id,'owner_uid'=>$uid],false,'owner_uid,lock_flag_pos,keyboard_pwd_version');
    $v = intval($r['keyboard_pwd_version']);
    if($v) return $v;

    // 远程查询 并设置
    empty($this->config) && $this->setConfig($lock_id);
    if($this->isSciener()){ //科技侠
      $r = $this->getMemberConfig($uid);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      $url   = 'https://api.ttlock.com.cn/v3/lock/getKeyboardPwdVersion';
      $param = [
        'clientId'    => $this->config['app_id'],
        'accessToken' => $r['access_token'],
        'lockId'      => $this->getOriId($lock_id),
        'date'        => $this->getMicroTime(),
      ];
      $r = $this->curl_post($url,$param);
    }else{ // 微技术
      $this->err('未知操作');
      // $url = $this->api_uri.'device/getpwdver';
      // $param = [
      //   'lockId'       => $this->getOriId($lock_id),
      //   'access_token' => $r['access_token'],
      // ];
    }
    // addTestLog($url,$param,'before_curl');
    // addTestLog($url,$param,$r);

    //  更新本地
    $v = isset($r['keyboardPwdVersion']) ? intval($r['keyboardPwdVersion']) : 0;
    if($v) $this->lock->where('lock_id',$lock_id)->setField('keyboard_pwd_version',$v);
    return $v;
  }

  // 发送密码的记录 - 本地
  // admin : 所有获取的,rent : 自己获取的,other:to_uid
  // params : uid,lock_id current_page,per_page,all
  public function listKeyboardLog($params){
    extract($params);
    $r = $this->getLock(['lock_id'=>$lock_id]);
    $lock_owner = (int) $r['owner_uid'];

    $map = ['k.lock_id'=>$lock_id];
    $order = 'k.send_time desc';
    if($uid == $lock_owner){ // 管理员
      if(!$all){
        $map['k.to_uid'] = ['neq',0]; // 所有已分配的
        $map['k.uid'] = $uid; // 永久密码
      }
    }else{ // 非管理员和租户
      $keyInfo = $this->hasRent($lock_id,false);
      if($keyInfo && $uid==intval($keyInfo['uid'])){ // 租户
        $map['k.uid'] = $uid;
        $order = 'k.keyboard_id asc';
      }else{ // 发给自己的
        $map['k.to_uid'] = $uid;
      }
    }
    $r = $this->lockKeyboard->alias('k')->join(['itboye_locks'=>'l'],'l.lock_id=k.lock_id','left')->where($map)->field('k.lock_id,k.keyboard_id,k.keyboard_pwd,k.start,k.end,k.type,k.alias,k.uid,k.to_uid,l.lock_type')->order($order)->paginate($per_page,false,['page'=>$current_page]);
    $r = $r->toArray();
    foreach ($r['data'] as &$v) {
      $v['type_desc'] = $this->getPwdTypeDesc($v['lock_id'],$v['keyboard_id'],$v['type']);
      $v['nickname']    = get_nickname($v['uid']);
      $v['to_nickname'] = get_nickname($v['to_uid']);
    } unset($v);

    return $r;
  }

  // 重置用户密码 admin
  // 科技侠：重置键盘密后原来的用户密码都将失效。在获取键盘密码时提示“密码已经用完”或“没有键盘密码数据”时可以调用该接口生成密码数据。
  // 百马 : 重新生成50组保存
  // params : 'uid|0|int,lock_id,pwdInfo,timestamp|0|int'
  public function resetKeyboardPwd($params){
    extract($params);
    // ? lock info
    $r = $this->getLock(['lock_id'=>$lock_id]);
    $lock_owner = (int) $r['owner_uid'];
    if($uid != $lock_owner){ //非管理员
       $this->err('非管理员');
      // ? 有效租户
      // $this->isValidRent($uid,$lock_id);
    }else{ // 管理员
      // if($this->hasRent($lock_id,false)) $this->err('存在租户');
    }
    // 业务开始
    if($this->isSciener($lock_id)){
      $r = $this->getMemberConfig($lock_owner);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      $url = $this->api_uri.'lock/resetKeyboardPwd';
      $param = [
        'clientId'    => $this->config['app_id'],
        'accessToken' => $r['access_token'],
        'lockId'      => $this->getOriId($lock_id),
        'pwdInfo'     => $pwdInfo,
        'timestamp'   => $timestamp,
        'date'        => $this->getMicroTime(),
      ];
      $r = $this->curl_post($url,$param);
      // addTestLog($url,$param,$r);
      // 删除该锁键盘密码记录
      $this->lockKeyboard->where('lock_id',$lock_id)->delete();
    }else{  // 重置百马永久密码
      $this->resetSitriPwd($uid,$lock_id,$pwdInfo,false,$timestamp);
    }
    //log
    $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_PASS_RESET);

    return '操作成功';
  }
  // 发送密码 + 本地 - admin
  // params : 'uid|0|int,lock_id,pwd_type|0|int,app_time|0|int','to_phone,alias,start|0|int,end|0|int,pwd'
  public function getKeyboardPwd($params){
    extract($params);$now = time();

    if($start<0 || 0!=($start%3600)) $this->err('生效时间需要整点');
    if($end<0 || 0!=($end%3600))     $this->err('失效时间需要整点');
    //
    $r = $this->getLock(['lock_id'=>$lock_id],'owner_uid,keyboard_pwd_version');
    $lock_owner = (int) $r['owner_uid'];
    $kb_version = (int) $r['keyboard_pwd_version'];

    if($uid != $lock_owner){
      // ? 合法住户
      $this->isValidRent($uid,$lock_id);
      if($this->isSciener($lock_id)){  // 科技侠
        if(!in_array($pwd_type,[1])){
          $this->err('错误密码类型'.$pwd_type);
        }
      }else{ // 百马只能永久下面判断 ,单次APP SDK获取
        $this->err('非管理员');
        // $this->checkRentPass($uid,$lock_id,$start,$end);
      }
    }else{  // 管理员
      if($this->hasValidRent($lock_id,false)) $this->err('租户有效中');
    }

    // 为谁获取的
    // $to_uid = 0;
    if($to_phone){
      $r = $this->getInfoByMobile($to_phone,'mc.uid','接收用户非法');
      $to_uid = intval($r['uid']);
    }else{
      $to_uid = $uid;
    }

    if($this->isSciener($lock_id)){ // 科技侠
      $end = $this->checkKbAndFixTime($kb_version,$pwd_type,$start,$end);
      //业务开始
      $r = $this->getMemberConfig($lock_owner);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      $url = $this->api_uri.'keyboardPwd/get';
      $param = [
        'clientId'    => $this->config['app_id'],
        'accessToken' => $r['access_token'],
        'lockId'      => $this->getOriId($lock_id),
        'keyboardPwdVersion' => $kb_version,
        'keyboardPwdType'    => $pwd_type,
        'startDate' => $start*1000,
        'endDate'   => $end*1000,
        'date'      => $now*1000, //$now
      ];
      // addTestLog($url,$param,'before_curl');
      $r = $this->curl_post($url,$param);
      // addTestLog($url,$param,$r);
      $pwd    = $r['keyboardPwd'];
      $pwd_id = $r['keyboardPwdId'];
      // 保存到本地
      $map = [
        'lock_id'   =>$lock_id,
        'start'     =>$start,
        'end'       =>$end,
        'type'      =>$pwd_type,
        'app_time'  =>$app_time,
        'send_time' =>$now,
        'uid'       =>$uid,
        'to_uid'    =>$to_uid,
        'alias'     =>$alias,
        'keyboard_pwd'=>$pwd,
        'keyboard_id' =>$pwd_id,
      ];
      $this->lockKeyboard->insertGetId($map);
    }else{ // 微技术
      if(!in_array($pwd_type, [2])) $this->err('只能获取永久管理员密码');
      if($pwd){ // 指定获取
        $r = $this->getLockKeyboard(['lock_id'=>$lock_id,'uid'=>0,'keyboard_pwd'=>$pwd],false,'id,keyboard_id as pwd_id',false,'密码错误或已分配');
        $pwd_id = $r['pwd_id'];
        $id     = $r['id'];
      }else{ // 随机获取
        // 获取一个未使用的永久密码 - admin
        $r = $this->getLockKeyboard(['lock_id'=>$lock_id,'to_uid'=>0,'keyboard_id'=>['lt',51]],false,'id,keyboard_id as pwd_id,keyboard_pwd as pwd',true,'密码分配完了');
        // 随机取一个
        $i      = mt_rand(0,count($r)-1);
        $pwd    = $r[$i]['pwd'];
        $pwd_id = $r[$i]['pwd_id'];
        $id     = $r[$i]['id'];
      }
      // 分配到 uid 名下
      $this->lockKeyboard->where('id',$id)->update(['uid'=>$uid,'to_uid'=>$to_uid,'alias'=>$alias]);
    }
    //log
    $this->addHisLog($uid,$to_uid,$lock_id,'',LockHisApi::ACTION_PASS_GET,1,$pwd.':'.$pwd_id.':'.$alias);
    return $pwd;
  }

  //百马发送自定义密码前置 获取未分配的pwd_id
  //params : uid|0|int,lock_id
  public function preAddKeyboardPwd($params){
    extract($params);$now = time();
    // lock_info
    $r = $this->getLock(['lock_id'=>$lock_id],false,'owner_uid');
    $lock_owner = (int) $r['owner_uid'];
    if($uid == $lock_owner){ // 管理员
      // 获取未分配的管理员密码
      $r = $this->getLockKeyboard(['lock_id'=>$lock_id,'to_uid'=>0,'keyboard_id'=>['lt',51]],false,'keyboard_id',false,'未发现可分配密码');
      return $r['keyboard_id'];
    }else{ // 租户
      $this->isValidRent($uid,$lock_id);
      $r = $this->getLockKeyboard(['lock_id'=>$lock_id,'keyboard_id'=>['gt',50]],false,'keyboard_id',true,false);
      if(empty($r)){
        return 51;
      }else{
        $ids = [51,52,53,54,55];$has_ids = [];
        foreach ($r as $v) {
          $has_ids[] = intval($v['keyboard_id']);
        }
        $ids = array_diff($ids, $has_ids);
        empty($ids) && $this->err('租户密码已达上限');
        return array_values($ids)[0];
      }

    }
  }

  // *添加自定义密码 + 本地 + sdk蓝牙回调失败请重试
  // 科技侠 : 3带锁,限时,自定义租户发送个数限制
  // params :'uid|0|int,lock_id,app_time' 'start|0|int,end|0|int,pwd_list,pwd'
  public function addKeyboardPwd($params){
    extract($params);$now = time();
    // lock_info
    $r = $this->getLock(['lock_id'=>$lock_id],false,'owner_uid');
    $lock_owner = (int) $r['owner_uid'];

    // 业务开始
    if($this->isSciener($lock_id)){ //科技侠添加密码pwd
      $pwd_type = 3; // 只能限时
      empty($pwd) && $this->err('需要密码pwd');
      // 租户时 检查
      ($uid!=$lock_owner) && $this->checkRentPass($uid,$lock_id,$start,$end);
      $r = $this->getMemberConfig($uid);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      $param = [
        'lockId'      => $this->getOriId($lock_id),
        'keyboardPwd' => $pwd,
        'addType'     => 1,// 蓝牙添加,先sdk蓝牙添加
        'startDate'=> $start*1000,
        'endDate'  => $end*1000,
        'date'     => $now*1000,
      ];
      $url = $this->api_uri.'keyboardPwd/add';
      $param = array_merge($param,[
        'clientId'    => $this->config['app_id'],
        'accessToken' => $r['access_token'],
      ]);
      // addTestLog($url,$param,'before_curl');
      $r = $this->curl_post($url,$param);
      // addTestLog($url,$param,$r);
      $pwd_id = $r['keyboardPwdId'];
      // 保存到本地 + log
      $this->addOneLocalPwd($uid,$lock_id,$pwd_id,[
        'keyboard_pwd' =>$pwd,
        'type'         =>$pwd_type,
        'app_time'     =>$app_time,
      ]);
    }else{
      $pwd_type = 2;//只能永久
      $pwd_list = json_decode($pwd_list,true);

      if($uid!=$lock_owner){ // rent
        $this->isValidRent($uid,$lock_id);
        $this->checkPwdPos($pwd_list,'rent');
      }else{ // admin
        $this->checkPwdPos($pwd_list,'admin');
      }

      foreach ($pwd_list as $v) {
        // 保存到本地 + log
        $this->addOneLocalPwd($uid,$lock_id,$v['pwd_id'],[
          'keyboard_pwd' =>$v['pwd'],
          'type'         =>$pwd_type,
          'app_time'     =>$app_time,
        ]);
      }
    }
    return '添加密码成功';//$pwd;
  }

  // 无条件本地添加密码记录,有则修改 + log
  protected function addOneLocalPwd($uid,$lock_id,$pwd_id,$upd){
    $upd['uid'] = $uid;
    $pwd = $upd['keyboard_pwd'];
    $map = ['lock_id'=>$lock_id,'keyboard_id'=>$pwd_id];
    !isset($upd['send_time']) && $upd['send_time']=0;
    !isset($upd['to_uid']) && $upd['to_uid']=0;
    !isset($upd['alias']) && $upd['alias']='';
    !isset($upd['start']) && $upd['start']=0;
    !isset($upd['end']) && $upd['end']=0;
    if($this->lockKeyboard->where($map)->find()){
      $r = $this->lockKeyboard->where($map)->update($upd);
    }else{
      $upd['lock_id']     = $lock_id;
      $upd['keyboard_id'] = $pwd_id;
      $r = $this->lockKeyboard->insertGetId($upd);
    }
    // log
    $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_PASS_ADD,1,$pwd.':'.$pwd_id);
    return $r;
  }

  // *删除单个键盘密码 - 管理员/rent - 基本与用户无关不推送
  // 科技侠 : 只能删除三代锁,密码版本为4;先sdk蓝牙/网关删除
  // params : 'uid|0|int,lock_id,keyboard_id|0|int,app_time'
  public function deleteKeyboardPwd($params){
    extract($params);
    // ? lock
    $r = $this->getLock(['lock_id'=>$lock_id],false,'owner_uid',false,'未发现锁信息,请重新绑定管理员');
    $lock_owner = (int) $r['owner_uid'];
    $r = $this->getLockKeyboard(['keyboard_id'=>$keyboard_id,'lock_id'=>$lock_id],false,false,false,'未发现该密码');
    // 是否为自己发 : 租户删租户密码,admin删admin密码
    if($r['uid'] != $uid) $this->err('非法操作:not pass owner');
    $pwd = $r['keyboard_pwd'];

    // 远程作业开始
    if($this->isSciener($lock_id)){
      // 删除键盘密码
      $r = $this->getMemberConfig($lock_owner);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      $url = $this->api_uri.'keyboardPwd/delete';
      $param = [
        'lockId'      =>$this->getOriId($lock_id),
        'deleteType'  =>1, // 蓝牙,需先蓝牙删除
        'clientId'    =>$this->config['app_id'],
        'accessToken' =>$r['access_token'],
        'keyboardId'  =>$keyboard_id,
        'date'        =>$this->getMicroTime(),
      ];
      $r = $this->curl_post($url,$param);
      // addTestLog($url,$r,'before:删除钥匙');
    }

    // 删除本地键盘密码
    $this->lockKeyboard->where(['lock_id'=>$lock_id,'keyboard_id'=>$keyboard_id])->delete();
    // log
    $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_PASS_DEL,1,$keyboard_id.':'.$pwd.':'.$app_time);
    return '删除成功';
  }

  // 修改密码名字
  // params : 'uid|0|int,lock_id,keyboard_id|0|int,alias'
  public function editKeyboardPwd($params){
    extract($params);
    empty($alias) && $this->err('需要别名');
    // lock info
    $r = $this->getLock(['lock_id'=>$lock_id],false,'owner_uid',false,'未发现锁信息');
    $lock_owner = (int) $r['owner_uid'];
    // ? uid添加的密码
    $this->getLockKeyboard(['lock_id'=>$lock_id,'keyboard_id'=>$keyboard_id,'uid'=>$uid],false,'*',false,'这不是你的密码');
    // uid
    if($uid == $lock_owner){ // 管理员
      if($this->hasValidRent($lock_id,false)) $this->err('租户有效中');
    }else{ // ? 租户
      $this->isValidRent($uid,$lock_id);
    }
    $this->lockKeyboard->where(['lock_id'=>$lock_id,'keyboard_id'=>$keyboard_id])->update(['alias'=>$alias]);
    // log
    $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_PASS_RENAME,1,$keyboard_id.':'.$alias);
    return '操作成功';
  }

  // *修改单个键盘密码 - 管理员/rent
  // 科技侠 : 三代锁,密码版本为4;先sdk蓝牙/网关修改
  // params : 'uid|0|int,lock_id,keyboard_id|0|int','pwd,start,end'
  public function changeKeyboardPwd($params){
    extract($params);
    if($pwd || ($start && $end)){ // 修改密码/时限
      // lock info
      $r = $this->getLock(['lock_id'=>$lock_id],false,'owner_uid',false,'未发现锁信息');
      $lock_owner = intval($r['owner_uid']);
      // ? uid添加的密码
      $this->getLockKeyboard(['lock_id'=>$lock_id,'keyboard_id'=>$keyboard_id,'uid'=>$uid],false,'*',false,'这不是你的密码');
      if($this->isSciener($lock_id)){ // 科技侠
        $rent_uid = 0;
        if($uid != $lock_owner){ // 租户
          $rent_info = $this->isValidRent($uid,$lock_id);
          $rent_uid  = $uid;
          if($start < intval($rent_info['start'])) $this->err('不得超过租户钥匙期限');
          if($end > intval($rent_info['end'])) $this->err('不得超过租户钥匙期限');
        }else{ // 管理员
          if($this->hasRent($lock_id,false)) $this->err('存在租户');
        }
        $r = $this->getMemberConfig($lock_owner);
        $r = $this->getAccessToken($r['name'],$r['pass']);
        // 修改键盘密码
        $url = $this->api_uri.'keyboardPwd/change';
        $upd = [];
        $param = [
          'clientId'    =>$this->config['app_id'],
          'accessToken' =>$r['access_token'],
          'lockId'      =>$this->getOriId($lock_id),
          'keyboardId'  =>$keyboard_id,
          'deleteType'  =>1, // 蓝牙,需先蓝牙修改
          'date'        =>$this->getMicroTime(),
        ];
        if($pwd){
          $param['newKeyboardPwd'] = $pwd;
          $upd['keyboard_pwd'] = $pwd;
        }else{
          $param['startDate'] = $start * 1000;
          $param['endDate']   = $end * 1000;
          $upd['start'] = $start;
          $upd['end']   = $end;
        }
        $r = $this->curl_post($url,$param);
        // addTestLog($url,$r,'before:删除钥匙');
        if($upd){
          // 修改本地键盘密码
          $this->lockKeyboard->where(['lock_id'=>$lock_id,'keyboard_id'=>$keyboard_id])->update($upd);
        }
      }else{ // 百马
        //暂无判断 ? 6位数字
        $upd = $keyboard_id.':'.$pwd;
        $q = $this->lockKeyboard->where(['lock_id'=>$lock_id,'keyboard_id'=>$keyboard_id]);
        if($pwd) $q->update(['keyboard_pwd'=>$pwd]);
        else $q->delete();
      }
      // log
      $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_PASS_EDIT,1,$upd);
      return '操作成功';
    }else{
      $this->err('非法操作');
    }
  }

  // *重置普通钥匙   sdk重置后调用
  // params : uid,lock_id,lock_flag_pos
  public function resetAllKey($params){
    extract($params);
    $r = $this->getLock(['lock_id'=>$lock_id,'owner_uid'=>$uid],false,'owner_uid,lock_flag_pos');
    // $lock_flag_pos = $lock_flag_pos ? $lock_flag_pos : intval($r['lock_flag_pos']);
    // $lockFlagPos = $lock_flag_pos +1; // 科技侠
    $r = $this->getLock(['lock_id'=>$lock_id,'owner_uid'=>$uid],'owner_uid,lock_flag_pos');
    $lockFlagPos = intval($r['lock_flag_pos'])+1;

    //业务开始
    $this->setConfig($lock_id);
    $r = $this->getMemberConfig($uid);
    $r = $this->getAccessToken($r['name'],$r['pass']);
    $url   = $this->api_uri.'lock/resetKey';
    $param = [
      'clientId'    => $this->config['app_id'],
      'accessToken' => $r['access_token'],
      'lockId'      => $this->getOriId($lock_id),
      'lockFlagPos' => $lockFlagPos,
      'date'        => $this->getMicroTime(),
    ];
    $r = $this->curl_post($url,$param);
    // addTestLog($url,$param,$r);
    $this->lock->where('lock_id',$lock_id)->setField('lock_flag_pos',$lockFlagPos);
    // log
    $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_KEY_RESET);
    return '操作成功';
  }

  // 解冻钥匙 - admin/rented
  // params : 'uid|0|int,key_id','aid|0|int'
  public function unlockKey($params){
    extract($params);$now = time();
    // ? 有效的冻结钥匙
    $r = $this->getLockKey(['key_id'=>$key_id,'status'=>LockKey::FROZE],'update_time desc','*',false,'非冻结钥匙');
    $key_owner = intval($r['uid']);
    $lock_id   = $r['lock_id'];
    $key_name = strPlus($key_id,$r['mark']);
    $type     = intval($r['type']);
    // lock_info
    $r = $this->getLock(['lock_id'=>$lock_id]);
    $lock_owner = $r['owner_uid'];
    $lock_name = strPlus($r['lock_name'],$r['lock_alias']);
    // ? type
    if($type == LockKey::USER){
      if($uid != $lock_owner) $this->err('非管理员');
    }elseif($type == LockKey::RENT){
      if($uid != $lock_owner) $this->err('非管理员');
    }elseif($type == LockKey::RENT_USER){
      $this->isValidRent($uid,$lock_id); // ? 有效租户
    }else{
      $this->err('非法操作');
    }
    $this->unlockOneKey($lock_owner,$key_id);
    // log
    $this->addHisLog($uid,$key_owner,$lock_id,$key_id,LockHisApi::ACTION_KEY_UNLOCK,1,'',$aid);
    $this->pushLockMessage('您持有的智能锁'.$lock_name.'的钥匙'.$key_name.'已被解冻',$key_owner);

    // if($type == LockKey::RENT){  // 冻结租户钥匙
    //   $r = $this->lockKey(['type'=>LockKey::RENT_USER,'lock_id'=>$lock_id,'status'=>LockKey::FROZE],false,'*',true,false);
    //   foreach ($r as $v) {
    //     $key_id    = $r['key_id'];
    //     $key_name  = strPlus($key_id,$r['mark']);
    //     $key_owner = intval($r['uid']);
    //     $this->unlockOneKey($lock_owner,$key_id);
    //     // log
    //     $this->addHisLog($uid,$key_owner,$lock_id,$key_id,'解冻关联钥匙',1,'',$aid);
    //     $this->pushLockMessage('您持有的智能锁'.$lock_name.'的钥匙'.$key_name.'已被关联解冻',$key_owner);
    //   }
    // }

    return '操作成功';
  }

  // admin无条件解冻一把非管理员钥匙
  private function unlockOneKey($uid,$key_id){
    $this->setConfig($key_id);
    if($this->isSciener()){ // 科技侠
      $r = $this->getMemberConfig($uid);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      $url = $this->config['api_uri'].'key/unfreeze';
      $param = [
        'clientId'    =>$this->config['app_id'],
        'accessToken' =>$r['access_token'],
        'keyId'       =>$this->getOriId($key_id),
        'date'        =>$this->getMicroTime(),
      ];
      $this->curl_post($url,$param);
    }else{ // 微技术
    }
    // 修改本地钥匙状态
    $this->lockKey->where('key_id',$key_id)->setField('status',LockKey::OK);
    return '操作成功';
  }
  // 冻结钥匙 - admin/rented
  // params : 'uid|0|int,key_id','aid|0|int'
  public function lockKey($params){
    extract($params);$now = time();

    // ? 有效钥匙
    $r = $this->getLockKey(['key_id'=>$key_id,'status'=>['in',[LockKey::OK,LockKey::WAIT]]],'update_time desc','*',false,'非可冻结钥匙');
    $key_owner = intval($r['uid']);
    $lock_id   = $r['lock_id'];
    $key_name = strPlus($key_id,$r['mark']);
    $type     = intval($r['type']);
    // lock_info
    $r = $this->getLock(['lock_id'=>$lock_id]);
    $lock_owner = $r['owner_uid'];
    $lock_name = strPlus($r['lock_name'],$r['lock_alias']);
    // ? type
    if($type == LockKey::USER){
      if($uid != $lock_owner) $this->err('非管理员');
    }elseif($type == LockKey::RENT){
      if($uid != $lock_owner) $this->err('非管理员');
    }elseif($type == LockKey::RENT_USER){
      // ? uid 租户
      $this->isValidRent($uid,$lock_id);
    }else{
      $this->err('非法操作');
    }
    $pushs = [];
    Db::startTrans();
    $r = $this->lockOneKey($lock_owner,$key_id);
    // log
    $this->addHisLog($uid,$key_owner,$lock_id,$key_id,LockHisApi::ACTION_KEY_LOCK,1,'',$aid);
    $pushs[] = ['您持有的智能锁'.$lock_name.'的钥匙'.$key_name.'已被冻结',$key_owner];
    if($type == LockKey::RENT){ // 关联冻结租户钥匙下的租户用户钥匙
      $r = $this->getLockKey(['type'=>LockKey::RENT_USER,'lock_id'=>$lock_id],false,'*',true,false); //,'status'=>['in',[LockKey::OK,LockKey::WAIT]]
      foreach ($r as $v) {
        $key_id    = $v['key_id'];
        $key_name  = strPlus($key_id,$v['mark']);
        $key_owner = intval($v['uid']);
        $r2 = $this->lockOneKey($lock_owner,$key_id);
        // log
        $this->addHisLog($uid,$key_owner,$lock_id,$key_id,LockHisApi::ACTION_KEY_LOCK_LINK,1,'',$aid);
        $pushs[] = ['您持有的智能锁'.$lock_name.'的钥匙'.$key_name.'已被关联冻结',$key_owner];
      }
    }
    Db::commit();
    $pushs && $this->pushMulti($pushs);
    return '操作成功';
  }

  // admin无条件冻结一把非管理员钥匙
  private function lockOneKey($uid,$key_id){
    $this->setConfig($key_id);
    if($this->isSciener()){ // 科技侠
      $r = $this->getMemberConfig($uid);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      $url = $this->api_uri.'key/freeze';
      $param = [
        'clientId'    =>$this->config['app_id'],
        'accessToken' =>$r['access_token'],
        'keyId'       =>$this->getOriId($key_id),
        'date'        =>$this->getMicroTime(),
      ];
      $this->curl_post($url,$param);
    }else{ // 微技术
    }
    // 修改本地钥匙状态
    $this->lockKey->where('key_id',$key_id)->setField('status',LockKey::FROZE);
    return '操作成功';
  }
  // admin无条件删除一把钥匙
  private function deleteOneKey($uid,$key_id){
    // 远程作业开始
    if($this->isSciener($key_id)){
      //用户删除自己钥匙
      $r = $this->getMemberConfig($uid);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      $url = $this->config['api_uri'].'key/delete';
      $param = [
        'clientId'    => $this->config['app_id'],
        'accessToken' => $r['access_token'],
        'keyId'       => $this->getOriId($key_id),
        'date'        => $this->getMicroTime(),
      ];
      return $this->curl_post($url,$param);
    }else{
      return 'ok';
    }
  }

  // 解绑锁 : 科技侠为删除管理员钥匙,百马为解绑锁
  // params : lock_id aid|0|int,key_id(科技侠必须,否则报错),curl
  public function unbindLock($params){
    $curl = isset($params['curl']) ? $params['curl'] : 1;
    extract($params);
    $r = $this->getLock(['lock_id'=>$lock_id]);
    $lock_owner = (int) $r['owner_uid'];
    $lock_mac   = $r['lock_mac'];

    // 暂不允许 远程删除科技侠管理员钥匙
    if($this->isSciener($lock_id)){
      // $this->deleteOneKey($lock_owner,$key_id);
      $this->err('请APP操作');
    }
    // 远程解绑百马
    $curl && $this->unbindSitriLock($lock_owner,$lock_id,$lock_mac);
    // 本地解绑锁 + log
    $this->localUnbindLock($lock_id,$lock_owner,$key_id,$aid);
    return 'ok';
  }

  // 本地解绑锁
  protected function localUnbindLock($lock_id,$lock_owner,$key_id,$aid){
    // 修改本地锁     owner_uid manage_uid house_no 更新update_time
    $this->lock->where('lock_id',$lock_id)->update(['owner_uid'=>0]);
    // 删除本地所有钥匙 - 远方已删除 不推送了
    $this->lockKey->where('lock_id',$lock_id)->delete();
    // 删除本地键密密码记录 - 远方已删除 不推送了
    $this->lockKeyboard->where('lock_id',$lock_id)->delete();
    // log
    $this->addHisLog($lock_owner,$lock_owner,$lock_id,$key_id,LockHisApi::ACTION_LOCK_UNBIND,1,'',$aid);
  }
  // 删除钥匙 - admin/rented
  // 删除管理员钥匙会同时删除该锁的所有钥匙和重置密码
  // 科技侠 : 删除租户钥匙会同时删除租户用户钥匙,且没有非管理员发的密码(APP提示,最多5天,蓝牙删)
  // params : 'uid|0|int,key_id','aid|0|int'
  public function deleteKey($params){
    extract($params);

    Db::startTrans();
    $pushs = [];
    // ? key info
    $r = $this->getLockKey(['key_id'=>$key_id],'update_time desc','*',false,'未发现钥匙信息,请联系锁管理员');
    $type = (int) $r['type'];
    $lock_id = $r['lock_id'];
    $key_owner = (int) $r['uid'];
    $key_name  = strPlus($key_id,$r['mark']);
    // ? lock info
    $r = $this->getLock(['lock_id'=>$lock_id]);
    $lock_owner = (int) $r['owner_uid'];
    $lock_mac   = $r['lock_mac'];
    $lock_name  = strPlus($r['lock_id'],$r['lock_alias']);

    $err = '';
    if($aid){ // 后台操作
      in_array($type,[LockKey::ADMIN]) && $this->err('后台不能直接删除管理员钥匙');
      $uid = $lock_owner;
    }
    if($type == LockKey::ADMIN){
      if($uid != $lock_owner) $err = '非管理员';
    }elseif($type == LockKey::RENT){
      $aid && $uid = $lock_owner;
      if($uid != $lock_owner) $err = '非管理员';
    }elseif($type == LockKey::RENT_USER){
      // ? 有租户
      $r = $this->getLockKey(['lock_id'=>$lock_id,'type'=>LockKey::RENT],'update_time desc','*',false,false);//'数据异常:未发现租户');
      $allow_uids = $r ? [intval($r['uid']),$lock_owner] : [$lock_owner];
      if(!in_array($uid,$allow_uids)) $err = '非租户或管理员';
      // ? 需先删除已知的非管理员发的密码(sdk操作,只能独立操作)
      // 暂不管,当初非管理员只能发5天内密码
      // $r = $this->getLockKeyboard(['lock_id'=>$lock_id,'uid'=>['neq',$lock_owner]],false,false,true,false);
      // $r && $this->err('需要先删除已知的非管理员发送的密码');
    }elseif($type == LockKey::USER){
      if($uid != $lock_owner) $err = '非管理员';
    }else{
      $err = '非法操作';
    }
    if($err) $this->err($err);

    // 科技侠远程删除钥匙
    $r = $this->deleteOneKey($lock_owner,$key_id);
    // addTestLog($url,$r,'before:删除钥匙');
    // 消息推送
    $pushs[] = ['您持有的智能锁'.$lock_name.'的钥匙'.$key_name.'已被删除',$key_owner];
    if($type == LockKey::ADMIN){ // 删除adminKey
      // 远程解绑百马
      $this->unbindSitriLock($lock_owner,$lock_id,$lock_mac);
      // 本地解绑锁 + log
      $this->localUnbindLock($lock_id,$lock_owner,$key_id,$aid);
    }elseif($type == LockKey::RENT){ // 删除租户钥匙
      // 删除本地租户钥匙
      $this->lockKey->where('key_id',$key_id)->delete();
      // 关联删除租户用户钥匙
      $r = $this->getLockKey(['lock_id'=>$lock_id,'type'=>LockKey::RENT_USER],false,'*',true,false);
      foreach ($r as $v) {
        $key_owner_ = (int) $v['uid'];
        $key_id_    = $v['key_id'];
        $key_name_  = strPlus($key_id_,$v['mark']);
        $this->deleteOneKey($lock_owner,$key_id_); //
        // 删除本地
        $this->lockKey->where('key_id',$key_id_)->delete();
        // log
        $this->addHisLog($uid,$key_owner_,$lock_id,$key_id_,LockHisApi::ACTION_KEY_DEL_RENT_USER_LINK,$aid);
        // push
        $pushs[] = ['您持有的智能锁'.$lock_name.'的钥匙'.$key_name_.'已被删除',$key_owner_];
      }
      $this->addHisLog($uid,$key_owner,$lock_id,$key_id,LockHisApi::ACTION_KEY_DEL_RENT,1,'',$aid);
    }else{ //删除用户/租户用户钥匙
      $this->lockKey->where('key_id',$key_id)->delete();
      $this->addHisLog($uid,$key_owner,$lock_id,$key_id,$type == LockKey::RENT_USER ? LockHisApi::ACTION_KEY_DEL_RENT_USER : LockHisApi::ACTION_KEY_DEL_USER,1,'',$aid);
    }
    Db::commit();
    $pushs && $this->pushMulti($pushs);
    return '删除成功';
  }
  // 删除非管理员钥匙 - 推荐管理员发送租户钥匙前调用
  // params : uid,lock_id
  public function deleteAllKey($params){
    extract($params);
    $r = $this->getLock(['lock_id'=>$lock_id,'owner_uid'=>$uid],false,'owner_uid',false,'未发现锁信息,请重新绑定管理员');
    $owner_uid = (int) $r['owner_uid'];

    // 远程作业开始
    $this->setConfig($lock_id);
    // 删除钥匙
    if($this->isSciener()){
      $r = $this->getMemberConfig($uid);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      $url = $this->api_uri.'lock/deleteAllKey';
      $param = [
        'clientId'    =>$this->config['app_id'],
        'accessToken' =>$r['access_token'],
        'lockId'      =>$this->getOriId($lock_id),
        'date'        =>$this->getMicroTime(),
      ];
      $r = $this->curl_post($url,$param);
    }else{
    }
    // addTestLog($url,$r,'before:删除钥匙');
    // 删除非管理员钥匙
    $this->lockKey->where(['lock_id'=>$lock_id,'type'=>['<>',0]])->delete();
    //log
    $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_KEY_DEL_UNADMIN);
    return '删除成功';
  }
  // 修改钥匙有效期 admin/rent
  // params : uid|0|int,key_id,start|0|int,end|0|int aid|0|int
  public function changePeriod($params){
    extract($params);
    $uid<1 && $this->err('uid非法');
    $start = max($start,0);  $end = max($end,0);
    if($end && $end<=$start) $this->err('end非法');
    // key info
    $r = $this->getLockKey(['key_id'=>$key_id,'status'=>['in',[LockKey::OK,LockKey::WAIT]]],false,'*',false,'非正常钥匙');
    $key_name  = strPlus($key_id,$r['mark']);
    $key_owner = (int) $r['uid'];
    $lock_id   = $r['lock_id'];
    $key_type  = (int) $r['type'];
    $key_start = (int) $r['start'];
    $key_end   = (int) $r['end'];
    if($start == $key_start && $end == $key_end) $this->err('未做变更');
    // lock info
    $r = $this->getLock(['lock_id'=>$lock_id]);
    $lock_name  = strPlus($r['lock_name'],$r['lock_alias']);
    $lock_owner = (int) $r['owner_uid'];

    if($uid == $lock_owner){ // 管理员
      if(!in_array($key_type,[LockKey::USER,LockKey::RENT])) $this->err('只能操作租户或用户钥匙');
    }else{ //非管理员
      // ? 有效租户
      $info = $this->isValidRent($uid,$lock_id);
      if($key_type != LockKey::RENT_USER) $this->err('只能操作租户用户钥匙');
      // 期限不得超过 租户钥匙
      if($start < $info['start'] || $end > $info['end']) $this->err('期限不得超过租户钥匙');
    }
    Db::startTrans();$pushs = [];
    $this->changeOnePeriod($lock_owner,$key_id,$start,$end);
    // log
    $this->addHisLog($uid,$key_owner,$lock_id,$key_id,LockHisApi::ACTION_KEY_CHANGE,1,$start.':'.$end,$aid);
    // 消息推送
    $pushs[] = ['您持有的智能锁'.$lock_name.'的钥匙'.$key_name.'的有效期改变了,请注意查看!',$key_owner];
    // 如果缩短租户需要关联修改
    if($key_type == LockKey::RENT && ($start>$key_start || $end<$key_end)){
      // 缩短租户钥匙期限 时 关联修改租户用户钥匙
      $r = $this->getLockKey(['lock_id'=>$lock_id,'type'=>LockKey::RENT_USER],false,'*',true,false);
      foreach ($r as $v) {
        $key_id = $v['key_id'];
        $key_start = (int) $v['start'];
        $key_end   = (int) $v['end'];
        $key_owner = (int) $v['uid'];
        if($start == $key_start && $end == $key_end) continue;
        $this->changeOnePeriod($lock_owner,$key_id,max($start,$key_start),min($end,$key_end));
        // log
        $this->addHisLog($uid,$key_owner,$lock_id,$key_id,LockHisApi::ACTION_KEY_CHANGE_LINK,1,$start.':'.$end,$aid);
        // 消息推送
        $pushs[] = ['您持有的智能锁'.$lock_name.'的钥匙'.$key_name.'的有效期改变了,请注意查看!',$key_owner];
      }
    }
    Db::commit();
    $pushs && $this->pushMulti($pushs);
    return '操作成功';
  }

  //管理员修改钥匙期限
  protected function changeOnePeriod($lock_owner,$key_id,$start,$end){
    //远程作业开始
    if($this->isSciener($key_id)){
      $r = $this->getMemberConfig($lock_owner);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      $param = [
        'accessToken' => $r['access_token'],
        'keyId'       => $this->getOriId($key_id),
        'startDate'   => $start *1000,
        'endDate'     => $end *1000,
      ];
      $url = $this->api_uri.'key/changePeriod';
      $param = array_merge($param,[
        'clientId' => $this->config['app_id'],
        'date'     => $this->getMicroTime(),
      ]);
      $this->curl_post($url,$param);
    }
    // 修改本地
    $this->lockKey ->where('key_id',$key_id)->update(['start'=>$start,'end'=>$end]);
  }
  // 获取租户信息
  // params : uid,lock_id
  public function getRentInfo($params){
    extract($params);
    $r = $this->getLock(['lock_id'=>$lock_id,'owner_uid'=>$uid]);
    return $this->getRentTime($uid,$r['house_no']);
  }
  // 发送钥匙 科技侠的成功后同步钥匙
  // * 发送租户钥匙,需无密码无钥匙(科技侠)
  // 2018-01-29 11:38:54 : 发送租户钥匙时 删除普通钥匙
  // 2018-08-20 14:29:43 : 取消重置密码限制,只发单次密码了
  // params : 'uid|0|int,to_mobile,lock_id,send_type|-1|int','start|0|int,end|0|int,mark,aid|0|int'
  public function sendKey($params){
    // return $params;
    extract($params);$now = time();
    $type = $send_type;
    if($end%3600 || $start%3600) $this->err('时间需整点');
    if($end && $end<=$start) $this->err('结束时间需要大于开始时间');
    if(!$uid) $this->err('uid非法');

    // admin
    $r = $this->getLock(['lock_id'=>$lock_id]);
    $lock_owner = (int) $r['owner_uid'];
    $lock_name  = $r['lock_name'];
    $lock_alias = $r['lock_alias'];
    $house_no   = $r['house_no'];

    // ? 有租户
    $rent_info = $this->hasRent($lock_id,false);
    $rent_uid  = $rent_info ? intval($rent_info['uid']) : 0;

    // 发给谁
    $r = $this->getInfoByMobile($to_mobile,'sciener_username as name,last_update_time,mc.uid','接收用户非法');
    $to_uid  = intval($r['uid']);
    // 下面的科技侠要用
    $to_sciener_name = $r['name'];
    $last_update_time = intval($r['last_update_time']);
    // 不得发给自己,管理员
    if($to_uid == $lock_owner || $uid==$to_uid) $this->err('不得发给自己或管理员');
    $need_reset = 0; // 是否需要重置租户密码
    if($uid == $lock_owner){ // 管理员发
      if($type == LockKey::USER){ // admin send user
        $rent_uid && $this->err('请先删除租户钥匙');
        $msg = LockHisApi::ACTION_KEY_SEND_USER;
      }elseif($type == LockKey::RENT){ // APP先获取租户信息做默认值
        $need_reset = 1; // 需要重置租户密码
        //2018-01-29 11:42:43 发送租户前删除普通钥匙
        $list = $this->getLockKey(['lock_id'=>$lock_id,'type'=>LockKey::USER],false,'key_id',true,false);
        foreach ($list as $v) {
          $this->deleteKey(['uid'=>$lock_owner,'key_id'=>$v['key_id'],'aid'=>0]);
        }
        // ? 有非管理员钥匙需重置(推荐)或删光
        $this->hasUnAdminKey($lock_id,false) && $this->err('请先重置或删除非管理员钥匙');
        if($start<=0 || $end<=0 || $start>=$end) $this->err('时限错误');
        // ? 有用户秘密
        if($this->isSitri($lock_id)){ //百马的需租客APP开此门开启租客模式
        }else{  //科技侠需重置(推荐)或删光
          // 取消重置密码限制,只发单次密码了 2018-08-20 14:29:15
          // $this->getLockKeyboard(['lock_id'=>$lock_id],false,'*',false,'请先重置密码');
        }
        $msg = LockHisApi::ACTION_KEY_SEND_RENT;
      }else{
        $this->err('只能发送租户钥匙或用户钥匙');
      }
    }elseif($uid == $rent_uid){ // 租户发
      $type != LockKey::RENT_USER && $this->err('只能发送租户用户钥匙');
      $start < intval($rent_info['start']) && $this->err('不得早于租户钥匙');
      $end   > intval($rent_info['end'])   && $this->err('不得晚于租户钥匙');
      $msg = LockHisApi::ACTION_KEY_SEND_RENT_USER;
    }else{
      $this->err('当前用户无权发送钥匙');
    }
    $lock_name = strPlus($lock_name,$lock_alias);
    $this->setConfig($lock_id);
    if($this->isSciener()){ // 科技侠
      // 远程作业开始
      $r = $this->getMemberConfig($lock_owner);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      $param = [
        'lockId'=>$this->getOriId($lock_id),
        'receiverUsername' => $to_sciener_name,
        'startDate'        => $start *1000,
        'endDate'          => $end *1000,
        'remarks'          => $type.':'.$mark,
      ];
      $url = $this->api_uri.'key/send';
      $param = array_merge($param,[
        'clientId'    => $this->config['app_id'],
        'accessToken' => $r['access_token'],
        'date'        => $this->getMicroTime(),
      ]);
      $r = $this->curl_post($url,$param);
      // addTestLog($param,$r,"发送钥匙:return");
      $key_id = $this->pre.$r['keyId'];
      // 同步接受者钥匙
      $this->syncKey($to_uid,$last_update_time,$this->pre);
    }else{ // 微技术
      $now = time();
      $r = $this->getLockKey(['lock_id'=>$lock_id,'uid'=>$to_uid],'id desc','*',false,false);
      if($r){
        $key_id = $r['key_id'];
        $data = [
          'start'       =>$start,
          'end'         =>$end,
          'type'        =>$send_type,
          'status'      =>LockKey::OK,
          'uid'         =>$to_uid,
          'mark'        =>$mark,
          'update_time' =>$now,
        ];
        $this->lockKey->where('key_id',$key_id)->update($data);
      }else{ // 本地添加
        // 本地添加
        $data = [
          'lock_id'     =>$lock_id,
          'key_id'      =>$this->pre.$now,
          'start'       =>$start,
          'end'         =>$end,
          'type'        =>$send_type,
          'status'      =>LockKey::OK,
          'uid'         =>$to_uid,
          'mark'        =>$mark,
          'reset_rent_pass' =>$need_reset,
          'create_time' =>$now,
          'update_time' =>$now,
        ];
        $id = $this->lockKey->insertGetId($data);
        $key_id = $this->pre.intval($id+10000);
        // 设置key_id
        $this->lockKey->where('id',$id)->update(['key_id'=>$key_id]);
      }

    }
    // log
    $this->addHisLog($uid,$to_uid,$lock_id,$key_id,$msg,1,$start.':'.$end,$aid);
    // 消息推送
    $this->pushLockMessage('您获得智能锁'.$lock_name.'的一把钥匙'.($mark ? '备注:'.$mark : ''),$to_uid);
    return $msg.'成功';
  }
  // 用户钥匙列表 - 本地
  // params : 'uid|0|int,kword'
  public function listUserkey($params,$lock_type=0){
    extract($params);
    $lock_type = $lock_type ? $lock_type : self::SITRI;


    $r = $this->lockKey->alias('k')
    ->join(['itboye_locks'=>'l'],'l.lock_id=k.lock_id','left')
    // ->join(['common_member'=>'cm'],'cm.uid=k.uid','left')
    ->field('k.key_id,k.start,k.end,k.status,k.type,k.reset_rent_pass,k.lockFlagPos,k.adminPwd,k.noKeyPwd,k.deletePwd,k.aesKeyStr,k.electricQuantity as power,k.timezoneRawOffset,k.mark as key_alias,l.lock_id,l.lock_name,l.lock_alias,l.house_no,l.push,l.lock_version,l.lock_mac,l.lock_key,l.lock_type');
    $map = ['k.uid'=>$uid,'l.lock_type'=>$lock_type];
    if($kword) $map['l.`lock_alias`|k.`mark`'] = ['like','%'.$kword.'%'];
    $r = $r->where($map);
    // $r = $r->where('k.uid',$uid)->where('lock_type',$lock_type);
    // if($kword) $r = $r->where('l.`lock_alias`|k.`mark`','like','%'.$kword.'%');
    $r = $r->select();
    $ret = [];
    foreach ($r as $v) {
      $status = intval($v['status']);
      $v['hasValidRent'] = 0;
      if(in_array($status, [LockKey::OK,LockKey::WAIT,LockKey::FROZE])){
        // $lock_id = $v['lock_id'];
        // $get_time = (int) $v['update_time'];
        $v['keyStatus'] = getKeyStatus($status);
        $v['keyType']   = getKeyType($v['type']);
        $v['lock_version'] = json_decode($v['lock_version'],true);
        // 管理员钥匙则返回是否有合法租户
        $v['hasValidRent'] = $v['type'] ? 0 : ($this->hasValidRent($v['lock_id'],false) ? 1 : 0);
        // 兼容V3 -->
        $v['can_open']  = $v['hasValidRent'];
        $v['user_type'] = $v['type'];
        // <-- 兼容V3
        // 是否有
        $ret[] = $v;
      }
    }
    return $ret;
  }

  // 设置钥匙别名 - admin / rent
  // 暂认为自己改自己
  // params : uid|0|int,key_id,mark aid|0|int
  public function editKey($params){
    extract($params);
    // ? key_id
    $r = $this->getLockKey(['key_id'=>$key_id]);
    $lock_id   = $r['lock_id'];
    $key_owner = $r['uid'];
    $key_type  = intval($r['type']);
    // ? lock_id
    $r = $this->getLock(['lock_id'=>$lock_id]);
    $lock_owner = $r['owner_uid'];
    if($uid && $uid == $lock_owner){ // 管理员
      // if($this->hasRent($lock_id,false)) $this->err('存在租户');
    }else{  // ? 租户
      // $this->isValidRent($uid,$lock_id);
      // !in_array($key_type,[self::ADMIN,self::USER]) && $this->error('无权操作');
    }
    $this->lockKey->where(['key_id'=>$key_id])->update(['mark'=>$mark]);
    $aid && $uid = $aid; //后台操作
    $this->addHisLog($uid,$key_owner,$lock_id,$key_id,LockHisApi::ACTION_KEY_RENAME,1,$mark,$aid);
    return '操作成功';
  }
  // 同步用户的所有科技侠锁的钥匙
  // sciener : 增量/全量钥匙别名将恢复发送时的设置
  public function syncKey($uid,$last_update=0,$pre=''){
    $now = time();$ret = 0;
    if($last_update == 0){ //全量
      $this->lockKey->where(['uid'=>$uid,'lock_id'=>['like','sciener_%']])->delete(); // 删除该用户全部科技侠钥匙
      $pre = ''; // 同步所有品牌
    }
    $pre_ = 'sciener_';
    if(in_array($pre,[$pre_ , ''])){// 同步科技侠
      $this->setConfig($pre_);
      $r = $this->getMemberConfig($uid);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      $url = $this->api_uri.'key/syncData';
      $param = [
        'clientId'       => $this->config['app_id'],
        'accessToken'    => $r['access_token'],
        'lastUpdateDate' => $last_update*1000,
        'date'           => $this->getMicroTime(),
      ];
      $r = $this->curl_post($url,$param);
      // addTestLog($r,$param,$uid."同步钥匙".$last_update);
      $this_time = floor($r['lastUpdateDate']/1000);
      $ret = $this_time;

      $list = $r['keyList'];
      if($list){
        // $keyStatusArr = ['110301'=>0,'110302'=>1];
        foreach ($list as $v) {
          $key_id    = $this->pre.$v['keyId'];
          $lock_id   = $this->pre.$v['lockId'];
          $keyStatus = intval($v['keyStatus']);
          // 已删除/已重置的删除本地
          if(in_array($keyStatus,[LockKey::DELETE,LockKey::RESET])){
            $this->lockKey->where('key_id',$key_id)->delete();
            continue; //进行下一次
          }

          $temp    = $v['remarks'];
          $temp    = trim($temp) ? explode(':',$temp,2) : [0,''];
          $mark    = isset($temp[1]) ? $temp[1] : '';
          $keyType = (int)$temp[0];
          // $mark    = $v['remarks'];
          // $keyType = $keyStatusArr[$v['userType']];
          // if($keyType && is_numeric($mark)) $keyType = intval($mark);

          // 本地有则更新 无则添加
          $edit = [
            'start'             => ceil($v['startDate']/1000),
            'end'               => floor($v['endDate']/1000),
            'status'            => $v['keyStatus'],
            'uid'               => $uid,
            'lockFlagPos'       => $v['lockFlagPos'],
            'adminPwd'          => isset($v['adminPwd']) ? $v['adminPwd'] : '',
            'noKeyPwd'          => isset($v['noKeyPwd']) ? $v['noKeyPwd'] : '',
            'deletePwd'         => isset($v['deletePwd']) ? $v['deletePwd'] : '',
            'aesKeyStr'         => $v['aesKeyStr'],
            'timezoneRawOffset' => $v['timezoneRawOffset'],
            'electricQuantity'  => $v['electricQuantity'],
            'update_time'       => $now,
            'type'=>$keyType
          ];
          $r = $this->lockKey->where(['lock_id'=>$lock_id,'key_id'=>$key_id])->find();
          if($r){
            // empty($r['mark']) && $edit['mark'] = $mark;
            $this->lockKey->where('id',intval($r['id']))->update($edit);
          }else{
            $add = array_merge(['lock_id'=>$lock_id,'key_id'=>$key_id,'create_time'=>$now,'mark'=>$mark],$edit);
            $this->lockKey->insertGetId($add);
          }
        }
        // 更新 last_update_time
        (new MemberConfig)->where(['uid'=>$uid])->update(['last_update_time'=>$this_time]);
      }
    }
    // $pre_ = 'sitri_';
    // if(in_array($pre,[$pre_ ,''])){// 同步微技术
      // $this->setConfig($pre_);
      // $url   = $this->api_uri.'';
      // $param = [];
      // $this->curl_post($url,$param);
      // $this_time = 0;
      // $ret[$this->pre] = $this_time;
      // // 更新 last_update_time
      // (new MemberConfig)->where(['uid'=>$uid])->update([$this->pre.'update_time'=>$this_time]);
    // }
    return $ret;
  }
  // 锁的全部钥匙列表 - admin/rent
  // type : 0管理员;1用户钥匙;2租户钥匙;3租户用户
  // params : 'uid|0|int,lock_id'
  public function listKey($params){
    extract($params);
    // 锁信息
    $r = $this->getLock(['lock_id'=>$lock_id]);
    $lock_owner = (int) $r['owner_uid'];
    $lock_name  = $r['lock_name'];
    $lock_alias = $r['lock_alias'];
    $house_no   = $r['house_no'];
    $where = ['lock_id'=>$lock_id];
    if($lock_owner == $uid){ // 管理员
    }else{ // 租户
      // ? 有效租户
      $rent_info = $this->isValidRent($uid,$lock_id,'非管理员或租户');
      $where['type'] = ['in',[LockKey::RENT,LockKey::RENT_USER]];
    }

    $ret = [
      'lock_type'            =>$r['lock_type'],
      'lock_name'            =>$r['lock_name'],
      'lock_version'         =>$r['lock_version'] ? json_decode($r['lock_version'],true) : [],
      'lock_mac'             =>$r['lock_mac'],
      'lock_key'             =>$r['lock_key'],
      'house_no'             =>$r['house_no'],
      'lock_flag_pos'        =>$r['lock_flag_pos'],
      'aes_key_str'          =>$r['aes_key_str'],
      'lock_alias'           =>$r['lock_alias'],
      'keyboard_pwd_version' =>$r['keyboard_pwd_version'],
      'push'                 =>$r['push'],
    ];
    // 钥匙信息
    $r = $this->lockKey->where($where)->select();
    $list = $r ? obj2Arr($r) : [];

    $ret_list = [];
    foreach ($list as $v) {
    //   $status = intval($v['status']);
    //   if(in_array($status, [LockKey::OK,LockKey::WAIT,LockKey::FROZE])){
        $v['unick'] = get_nickname($v['uid']);
        $v['keyStatus'] = getKeyStatus($v['status']);
        $v['keyType']   = getKeyType($v['type']);
        $ret_list[] = $v;
    //   }
    }
    $ret['list'] = $ret_list;
    return $ret;
  }


  // 推送开关设置
  // params : 'uid|0|int,lock_id,push|0|int'
  public function ifPush($params){
    extract($params);
    // ? lock_id admin
    $r = $this->getLock(['lock_id'=>$lock_id,'owner_uid'=>$uid]);
    if(intval($r['push']) === $push) $this->err('无效操作');
    $this->lock->where('lock_id',$lock_id)->setField('push',$push);

    $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_LOCK_PUSH,1,$push);
    return '操作成功';
  }
  // 锁设置电量 - 本地和推送
  // params : 'uid|0|int,lock_id,power|100|int'
  public function setPower($params){
    extract($params);
    if($power<1 || $power>100) $this->err('电量非法');
    // ? lock_id
    $r = $this->getLock(['lock_id'=>$lock_id],false,'lock_alias,owner_uid,lock_name',false,'未发现锁信息,请重新绑定管理员');
    // 记录电量
    $this->lockKey->where('lock_id',$lock_id)->setField('electricQuantity',$power);
    //? 低电量
    if($power<LockKey::LOW_POWER){
      $owner_uid = (int) $r['owner_uid'];
      $lock_name = strPlus($r['lock_name'],$r['lock_alias']);
      //低电量推送 owner
      $this->pushLockMessage('您管理的智能锁'.$lock_name.'电量过低('.$power.'%),请及时更换电池',$owner_uid);
      return '操作成功';
    }else{
      return '操作成功,非低电量';
    }
  }
  // * 开锁回调 + 开锁记录 + 设置电量
  // 注意 : 失败要重试
  // 错误时间的记录(大于现在10分钟) 时间置0 2018-04-08 13:42:18
  // params : 'uid|0|int,lock_id' 'success|1|int,unlock_time|0|int  records,power|0|int'
  public function unlock($params){
    extract($params);
    if(!in_array($success, [0,1])) $this->err('开始结果非法');
    if($power<0 || $power>100) $this->err('电量值非法');
    // 上次校准
    if($unlock_time){
      $this->lock->where(['lock_id'=>$lock_id])->update(['last_adjust'=>$unlock_time]);
    }
    //业务开始
    if($this->isSciener($lock_id)){ // 科技侠 远程记录和查询
      $r = $this->getMemberConfig($uid);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      // 上传开锁记录
      if($records){
        $url = $this->api_uri.'lockRecord/upload';
        $param = [
          'clientId'    =>$this->config['app_id'],
          'accessToken' =>$r['access_token'],
          'lockId'      =>$this->getOriId($lock_id),
          'records'     =>$records,
          'date'        =>$this->getMicroTime(),
        ];
        $this->curl_post($url,$param);
      }
      // 上传电量
      if($power>=0){
        $url = $this->api_uri.'lock/updateElectricQuantity';
        $param = [
          'clientId'        =>$this->config['app_id'],
          'accessToken'     =>$r['access_token'],
          'lockId'          =>$this->getOriId($lock_id),
          'electricQuantity'=>$power,
          'date'            =>$this->getMicroTime(),
        ];
        $this->curl_post($url,$param);
      }
    }else{ // 微技术 - 本地记录
      // 记录开锁记录 + 去重
      $records = json_decode($records,true);
      if($records){
        // empty($records) && $this->err('records格式错误');
        $adds = [];
        $app_time = 0;
        $now = time();
        foreach ($records as $v) {
          //lockIndex time pwdPosition type userId tempPass
          $userid = isset($v['userId']) ? intval($v['userId']) : 0;
          if(strlen($userid)>10){
            continue;
          }
          $create_time = $v['time']/1000;
          $create_time = ($create_time > $now + 600) ? 0 : $create_time;
          //1永久 2IC 3蓝牙 4单次
          if($userid == $uid && $v['type']==3){
            $app_time = $unlock_time;
          }
          // 这条服务器有吗 开锁时间+type+uid+lock_id
          if($this->getLockRecord(['type'=>$v['type'],'op_time'=>$create_time,'lock_id'=>$lock_id,'uid'=>$userid],false,'id',false,false)){ // 重复了
          }else{ // 新记录
            $adds[] = [
              'lock_id'     =>$lock_id,
              'uid'         =>$userid,
              'result'      =>1,
              'type'        =>$v['type'],
              'create_time' =>$now,
              'op_time'     =>$create_time,
              'app_time'    =>$app_time,
              'tempPass'    =>$v['tempPass'],
              'pwdPosition' =>$v['pwdPosition'],
              'lockIndex'   =>$v['lockIndex'],
            ];
          }
        }
        $adds && $this->lockRecord->saveAll($adds);
      }
    }
    // 设置电量
    $power && $this->setPower(['uid'=>$uid,'lock_id'=>$lock_id,'power'=>$power]);
    if($reset_rent_pass){
      // 修改 reset_rent_pass
      $this->lockKey->where(['lock_id'=>$lock_id,'type'=>LockKey::RENT])->update(['reset_rent_pass'=>0]);
      // 清空租户密码
      $this->lockKeyboard->where(['lock_id'=>$lock_id,'keyboard_id'=>['in','51,52,53,54,55']])->delete();
      $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_PASS_RESET_RENT,1);
    }
    // addTestLog($param,$r,$url);
    $r = $this->getLock(['lock_id'=>$lock_id],false,null,false,'未发现锁信息,请重新绑定管理员');
    // 推送开锁
    $ifpush = (int) $r['push'];
    if($ifpush){
      $lock_name  = strPlus($r['lock_name'],$r['lock_alias']);
      $owner_uid  = $r['owner_uid'];
      $user_name  = strPlus($uid,get_nickname($uid));
      $msg = $user_name.'蓝牙于'.date('Y-m-d H:i:s',$unlock_time).'开启智能锁'.$lock_name.($success ? '成功' : '失败');
      $this->pushLockMessage($msg,$owner_uid);
    }
    return '操作成功';
  }

  // 锁的操作记录
  //params : lock_id,current_page,per_page,latest owner_uid,aid
  public function listHis($params){
    extract($params);
    // ? admin
    $map = ['lock_id'=>$lock_id];
    !$aid && $map['owner_uid'] = $owner_uid;
    $this->getLock($map);

    $map = ['lock_id'=>$lock_id];
    if($latest){
      // 最近的一次成功初始化 id
      $r = $this->lockHis->where(['lock_id'=>$lock_id,'operate'=>LockHisApi::ACTION_LOCK_INIT,'result'=>1])->field('id')->order('id desc')->find();
      $id = $r ? (int)$r['id'] : 0;
      $id && $map['id'] = ['egt',$id];
    }
    $r = (new LockHisApi)->query($map,['curpage'=>$current_page,'size'=>$per_page],'create_time desc');
    if($r['status']){
      foreach ($r['info']['list'] as $v) {
        $v['nickname'] = get_nickname($v['uid']);
        $v['to_nickname'] = get_nickname($v['to_uid']);
        $v['admin_nickname'] = $v['aid'] ? get_nickname($v['aid']) : '';
      } unset($v);
    }
    return $r;
  }

  // 开锁记录 - 科技侠远程/百马本地
  // params : 'uid|0|int,lock_id,current_page|1|int,per_page|20|int'
  public function listRecord($params){
    extract($params); //per_page:20-100
    // 查询远程
    if($this->isSciener($lock_id)){ // 科技侠查远程
      $temp = $this->getLock(['lock_id'=>$lock_id],false,null,false,'未发现锁信息');
      $uid = $temp['owner_uid'];
      $r = $this->getMemberConfig($uid);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      // 业务开始 科技侠
      $url = $this->api_uri.'lockRecord/list';
      $param = [
        'clientId'    =>$this->config['app_id'],
        'accessToken' =>$r['access_token'],
        'lockId'      =>$this->getOriId($lock_id),
        'pageNo'      =>$current_page,
        'pageSize'    =>$per_page,
        'date'        =>$this->getMicroTime(),
      ];
      $r = $this->curl_post($url,$param);
      if(empty($r['list'])) $this->err($r);
      $list = $r['list'];$data = [];

      foreach ($list as $v) {
        //lockId recordType success username keyboardPwd lockDate serverDate
        $uinfo = ['uid'=>0,'nickname'=>'null:'.$v['username']];
        if($v['username']){
          $tinfo = $this->getCacheUserInfo(['sciener_username'=>$v['username']],'mc.uid,nickname',1800,false,'');
          if($tinfo && $tinfo['nickname']){
            $uinfo = $tinfo;
          }
        }
        $data[] = [
          'lock_id'     =>$this->pre.$v['lockId'],
          'uid'         =>$uinfo['uid'],
          'result'      =>$v['success'],
          'type'        =>$v['recordType'],
          'create_time' =>$v['serverDate']/1000,
          'op_time'     =>$v['lockDate']/1000,
          'app_time'    =>0,
          'tempPass'    =>'',
          'pwdPosition' =>$v['keyboardPwd'],
          'lockIndex'   =>0,
          'lock_type'   =>self::SCIENER,
          'nickname'    =>$uinfo['nickname'],
        ];
      }
      return ['data'=>$data,'current_page'=>$r['pageNo'],'per_page'=>$r['pageSize'],'total'=>$r['pages']];

    }else{ // 百马
      $this->getLock(['lock_id'=>$lock_id],false,'lock_mac',false,'lock_id错误');
      // 本地查询
      $list = $this->lockRecord->where(['lock_id'=>$lock_id])->order('op_time desc')->paginate($per_page,false,['page'=>$current_page]);
      $list = $list->toArray();
      foreach ($list['data'] as &$v) {
        $v['lock_type'] = self::SITRI;
        $v['nickname']  = get_nickname($v['uid']);
      } unset($v);
      return $list;
    }
  }
  // 绑定房源
  // params : uid,lock_id,house_no
  public function bindHouse($params){
    extract($params);

    // ? admin
    $this->getLockKey(['uid'=>$uid,'lock_id'=>$lock_id,'type'=>LockKey::ADMIN,'status'=>LockKey::OK]);
    $this->lock ->where(['lock_id'=>$lock_id])->setField('house_no',$house_no);
    // 添加智能锁tag
    $r = (new HouseTagApi)->addTag(getDatatree('house_smart_lock'),$house_no);
    $this->checkErr($r);

    $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_HOUSE_BIND,1,$house_no);
    return '绑定成功';
  }
  // 解绑房源
  // 同时删除智能锁tag : 2017-10-24 16:00:32
  // params : uid,lock_id,house_no
  public function unbindHouse($params){
    extract($params);

    // ? admin
    $this->getLockKey(['uid'=>$uid,'lock_id'=>$lock_id,'type'=>LockKey::ADMIN,'status'=>LockKey::OK]);
    $this->lock ->where(['lock_id'=>$lock_id])->setField('house_no','');
    // 删除智能锁tag
    $r = (new HouseTagApi)->delTag(getDatatree('house_smart_lock'),$house_no);
    $this->checkErr($r);

    $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_HOUSE_UNBIND,1,$house_no);
    return '解绑成功';
  }

  // 检查微技术绑定 : 微技术初始化使用
  // params : uid,lockMac
  // public function checkBind($params){
  //   extract($params);
  //   !$this->config && $this->setConfig("sitri_");
  //   $url = $this->api_uri.'device/check';
  //   $r = $this->getMemberConfig($uid);
  //   $r = $this->getAccessToken($r['name'],$r['pass']);
  //   $param = [
  //     'lockMac'      => $lockMac,
  //     'access_token' => $r['access_token'],
  //   ];
  //   $this->curl_post($url,$param);
  // }
  // 是否初始化了锁
  // param : lockMac
  public function isInit($params){
    extract($params);
    $r = $this->lock->where('lock_mac',$lockMac)->field('owner_uid')->find();
    return $r ? (int)$r['owner_uid'] : 0;
  }

  // 初始化百马
  // param : uid,lockMac,lockName,lockAlias,pwdInfo,app_time
  public function initSitri($params){
    extract($params);$now = time();
    $r = $this->lock->where('lock_mac',$lockMac)->field('owner_uid,lock_id')->find();
    $owner_uid = $r ? (int)$r['owner_uid'] : 0;
    if($owner_uid) $this->err('该锁已绑定');
    $pre = 'sitri_';
    $lock_type = $this->lock_configs[$pre]['lock_type'];
    // 添加锁信息
    $data = ['owner_uid'=>$uid,'lock_name'=>$lockName,'lock_alias'=>$lockAlias,'lock_type'=>$lock_type,'update_time'=>$now];
    Db::startTrans();
    try{
      if($r){ // edit
        $lock_id = $r['lock_id'];
        $this->lock->where('lock_mac',$lockMac)->update($data);
      }else{ // add
        $data['lock_mac'] = $lockMac;
        $data['lock_id']  = $pre.time();
        $data['create_time'] = $now;
        $id = $this->lock->insertGetId($data);
        $lock_id = $pre.intval($id+10000);
        // 设置lock_id
        $this->lock->where('id',$id)->update(['lock_id'=>$lock_id]);
      }
      // 设置新密码
      $this->resetSitriPwd($uid,$lock_id,$pwdInfo,true,$app_time);
      // 删除钥匙信息
      $this->lockKey->where('lock_id',$lock_id)->delete();
      // 设置管理员钥匙
      $data = [
        'lock_id'     =>$lock_id,
        'key_id'      =>$pre.time(),
        'start'       =>0,
        'end'         =>0,
        'create_time' =>time(),
        'update_time' =>time(),
        'status'      =>LockKey::OK,
        'uid'         =>$uid,
        'type'        =>LockKey::ADMIN,
      ];
      $id = $this->lockKey->insertGetId($data);
      $key_id = $pre.intval($id+10000);
      // 设置key_id
      $this->lockKey->where('id',$id)->update(['key_id'=>$key_id]);
      $this->addHisLog($uid,0,$lock_id,$key_id,LockHisApi::ACTION_LOCK_INIT);
      Db::commit();
    }catch(\Exception $e){
      Db::rollback();
      throw $e;
    }
    return ['lock_id'=>$lock_id,'key_id'=>$key_id];
  }

  // *智能锁绑定管理员 - 电量低调用推送接口
  // lock_type : 6323=>科技侠,6587=>微技术(已转移)
  // lockVersion : json {,,,,}
  // 注意 : 失败要重试
  // params : uid|0|int,lock_type|0|int,lockName,lockAlias,lockMac,lockKey,aesKeyStr,pwdInfo,lockVersion','lockFlagPos|0|int,adminPwd,noKeyPwd,deletePwd,timestamp,specialValue|-1|int,electricQuantity|0|int,modelNum,hardwareRevision,firmwareRevision
  public function init($params){
    extract($params);
    // ? lock_type
    $pre = $this->checkLockType($lock_type);

    // 业务开始
    $this->setConfig($pre);
    $r = $this->getMemberConfig($uid);
    $last_update_time = intval($r['last_update_time']);
    $r = $this->getAccessToken($r['name'],$r['pass']);
    $token = $r['access_token'];
    $param = [
      'lockName'          =>$lockName,
      'lockAlias'         =>$lockAlias,
      'lockMac'           =>$lockMac,
      'lockKey'           =>$lockKey,
      'lockFlagPos'       =>$lockFlagPos,
      'aesKeyStr'         =>$aesKeyStr,
      'adminPwd'          =>$adminPwd,
      'noKeyPwd'          =>$noKeyPwd,
      'deletePwd'         =>$deletePwd,
      'pwdInfo'           =>$pwdInfo,
      'timestamp'         =>$timestamp,
      'specialValue'      =>$specialValue,
      'electricQuantity'  =>$electricQuantity,
      'timezoneRawOffset' =>28800000,
      'modelNum'          =>$modelNum,
      'hardwareRevision'  =>$hardwareRevision,
      'firmwareRevision'  =>$firmwareRevision,
    ];
    // 科技侠
    $isSciener = $this->isSciener();
    if($isSciener){
      $url   = $this->api_uri.'lock/init';
      $param = array_merge($param,[
        'clientId'          =>$this->config['app_id'],
        'accessToken'       =>$token,
        'lockVersion'       =>$lockVersion,
        'date'              =>$this->getMicroTime(),
      ]);
    }else{
      $this->err('请调用initSitri接口');
      // ? 检查绑定
      // $this->checkBind($uid,$lockMac);
      // // 初始化
      // $url = $this->api_uri.'device/bind';
      // $lockVersion_ = json_decode($lockVersion);
      // if(!isset($lockVersion_['protocolType']) || !isset($lockVersion_['protocolVersion']) || !isset($lockVersion_['scene']) || !!isset($lockVersion_['groupId']) || !!isset($lockVersion_['orgId'])){
      //   $this->err('lockVersion invalid');
      // }
      // $param = [
      //   'protocolType'    =>$lockVersion_['protocolType'],
      //   'protocolVersion' =>$lockVersion_['protocolVersion'],
      //   'scene'           =>$lockVersion_['scene'],
      //   'groupId'         =>$lockVersion_['groupId'],
      //   'orgId'           =>$lockVersion_['orgId'],
      //   'access_token'    =>$token,
      // ];
    }
    $r = $this->curl_post($url,$param);
    // $r = [
    // 'lockId'=>29677,
    // 'keyId' =>208237,
    // ];
    // addTestLog($param,$r,$this->client_id . ":锁绑定管理员:return");
    // empty($r) && $this->apiReturnErr('远方传来噩耗:NULL');
    if(!isset($r['lockId']) || !isset($r['keyId'])){
      $this->err($r);
    }
    $now = time();

    Db::startTrans();
    $m = $this->lock;
    //本地
    $lock_id = $pre.$r['lockId'];
    $key_id  = $pre.$r['keyId'];
    $try = [
      'lock_id'       => $lock_id,
      'lock_type'     => $lock_type,
      'lock_name'     => $lockName,
      'lock_alias'    => $lockAlias,
      'power'         => $electricQuantity,  //电量
      'owner_uid'     => $uid,
      // 'key_id'        => $key_id,
      'create_time'   => $now,
      'update_time'   => $now,
      'lock_version'  => $lockVersion,
      'lock_mac'      => $lockMac,
      'lock_key'      => $lockKey,
      'lock_flag_pos' => $lockFlagPos,
      'aes_key_str'   => $aesKeyStr,
      'house_no'      => '',
      'model_num'         => $modelNum,
      'hardware_revision' => $hardwareRevision,
      'firmware_revision' => $firmwareRevision,
    ];
    if($m->where('lock_id',$lock_id)->find()){
      $m->where('lock_id',$lock_id)->update($try);
    }else{
      $id = $m->insertGetId($try);
    }
    //删除该锁所有钥匙
    $this->lockKey->where('lock_id',$lock_id)->delete();

    // 查询设置锁键盘密码版本
    if($isSciener){ // 科技侠
      $this->getKbVersion($lock_id,$uid);
      // 清空ic卡
      (new LockIcCardApi)->delete(['lock_id'=>$lock_id]);
      //增量同步管理员钥匙
      $this_time = $this->syncKey($uid,$last_update_time,$pre);
    }
    $this->addHisLog($uid,0,$lock_id,$key_id,LockHisApi::ACTION_LOCK_INIT);
    Db::commit();
    return ['lock_id'=>$lock_id,'key_id'=>$key_id,'last_update_time'=>$this_time];
  }
  // 修改锁信息 - 当前仅别名 - admin
  // params : uid,lock_id,alias
  public function edit($params){
    extract($params);

    // ? admin
    $this->getLock(['lock_id'=>$lock_id,'owner_uid'=>$uid]);
    //业务开始
    $this->setConfig($lock_id);
    if($this->isSciener()){ // 科技侠
      //远程修改
      $r = $this->getMemberConfig($uid);
      $r = $this->getAccessToken($r['name'],$r['pass']);
      $url = $this->api_uri.'lock/rename';
      $param = [
        'clientId'    =>$this->config['app_id'],
        'accessToken' =>$r['access_token'],
        'lockId'      =>$this->getOriId($lock_id), //需去掉前缀
        'lockAlias'   =>$alias,
        'date'        =>$this->getMicroTime(),
      ];
      $this->curl_post($url,$param);
    }else{ // 微技术 sdk负责
      // $url = $this->api_uri.'device/rename';
      // $param = [
      //   'accessToken' =>$r['access_token'],
      //   'lockId'      =>$this->getOriId($lock_id), //需去掉前缀
      //   'lockAlias'   =>$alias,
      // ];
    }
    //修改本地
    $this->lock ->where('lock_id',$lock_id)->setField('lock_alias',$alias);

    $this->addHisLog($uid,0,$lock_id,'',LockHisApi::ACTION_LOCK_RENAME,1,$alias);
    return '修改成功';
  }

  // 获取名下锁
  // params : uid kword,page,size,order,house_no
  public function lock($params){
    extract($params);
    //业务开始
    $map = ['owner_uid'=>$uid];
    if($house_no) $map['l.house_no'] = $house_no;
    $r = $this->lock
    ->field('id,lock_id,lock_alias,lock_name,house_no,lock_type')
    ->where($map)->order($order)
    ->paginate($size,false,['page'=>$page]);

    return $r->toArray();
  }

  // 换绑智能锁账号
  // 将已有的智能锁账号 绑定到 未绑定的住家账号
  // 可打通APP登陆(科技侠)
  // params : uid,name,pass,lock_type
  public function bind($params){
    extract($params);

    $pre = $this->checkLockType($lock_type);
    $m = new MemberConfig;
    //? name未绑定本地
    $r = $m->field($pre.'username')->where($pre.'username',$name)->find();
    $this->checkDbErr($r,$m,false,'','此账号已被绑定'.$pre);
    //? uid未绑定相应智能锁
    $r = $m->field($pre.'username')->where('uid',$uid)->find();
    $this->checkDbErr($r,$m,false,'uid错误');
    if(!empty($r[$pre.'username'])) $this->err('请先取消绑定');

    //? 密码加密
    $pass = $this->getPass(0,$pass);

    $this->setConfig($pre);
    $this->getAccessToken($name,$pass);
    $m->where('uid',$uid)->update([$pre.'username'=>$name,$pre.'password'=>$pass]);

    $this->addHisLog($uid,0,'','',LockHisApi::ACTION_BIND,1,$pre);
    return '绑定成功';
  }

  // 取消绑定智能锁账号(单个)
  // param : uid,lock_type
  public function unbind($params){
    extract($params);
    $this->pre = $this->checkLockType($lock_type);
    $pre = $this->pre;

    // 是否已绑定
    $r = $this->getMemberConfig($uid);
    $username = $r['name'];
    $password = $r['pass'];
    if(empty($username)) $this->err('看起来您并未绑定'.$pre.'账号');
    // 业务开始
    $this->setConfig($pre);
    if($this->isSciener()){ // 科技侠
      $url = $this->api_uri.'user/delete';
      $param = [
          'clientId'     => $this->config['app_id'],
          'clientSecret' => $this->config['app_secret'],
          'username'     => $username,
          // 'username'     => substr($username,strlen($this->pre)),
          'date'         => $this->getMicroTime(),
      ];
      $this->curl_post($url,$param);
      $upd = [$pre.'username'=>'',$pre.'password'=>''];
    }else{ //微技术 sdk删除后调用
      // $url = $this->api_uri.'user/deleteUser';
      // $r = $this->getAccessToken($username,$password);
      // $param = [
      //   'username'    => $username,
      //   'access_token'=> $r['access_token']
      // ];
      $map['sitri_reg'] = 0;
    }
    // change userinfo
    (new MemberConfig)->where('uid',$uid)->update($map);

    $this->addHisLog($uid,0,'','',LockHisApi::ACTION_UNBIND,1,$pre);
    return '操作成功';
  }

  // sdk注册百马回调
  public function regSitri($uid){
    $m   = new MemberConfig;
    $m->where('uid',$uid)->update(['sitri_reg'=>1]);
    return 'ok';
  }

  // 是否注册了百马
  public function isRegSitri($uid){
    $m   = new MemberConfig;
    $r = $m->field('sitri_reg')->where('uid',$uid)->find();
    empty($r) && $this->err('非法用户');
    return $r['sitri_reg'];
  }

  // 默认注册科技侠
  // 注意:账户里没信息才绑定,强制重新绑定需置空字段,
  public function regSciener($uid){
    $uid = intval($uid);
    $m   = new MemberConfig;
    $username = '';

    // 账号绑定信息
    $pre  = 'sciener_';
    // $pre2 = 'sitri_';
    $r = $m->field($pre.'username')->where('uid',$uid)->find(); //,'.$pre2.'username
    if(!$r) $this->err('uid错误');
    $pass  = $this->getPass($uid);
    $name  = $this->getName($uid);

    // 是否已绑定科技侠
    $this->setConfig($pre);
    if(empty($r[$pre.'username'])){ //账户里没信息才绑定
      $url   = $this->api_uri.'user/register';
      $param = [
        'clientId'     => $this->config['app_id'],
        'clientSecret' => $this->config['app_secret'],
        'username'     => $name,
        'password'     => $pass,
        'date'         => $this->getMicroTime(),
      ];
      $r = $this->curl_post($url,$param);
      // addTestLog($param,$r,$url);
      //["username"=>"ihome_6512bd43d9caa6e02c990b0a82652dca"];
      if(!isset($r['username'])) $this->err('curl-return need-username');
      $username = $r['username']; //远方自动加 ihome_ 前缀了
      $r = $m->where('uid',$uid)->update([$pre.'username'=>$username,$pre.'password'=>$pass]);
      if(!$r) $this->err('uid错误');
      $this->addHisLog($uid,0,'','',LockHisApi::ACTION_REG,1,$pre);
    }

    // 是否已绑定微技术  =>  由sdk实现 2018-01-02 13:59:07
    // $this->setConfig($pre2);
    // if(empty($r[$pre2.'username'])){
    //   $url  = $this->api_uri.'user/register';
    //   $param = [
    //       'country'  => '86',
    //       'username' => $name,
    //       'password' => $pass,
    //       'nickname' => $name,
    //   ];
    //   $r = $this->curl_post($url,$param);
    //   $username = $name;
    //   $r = $m->where('uid',$uid)->update([$pre2.'username'=>$username,$pre2.'password'=>$pass]);
    //   if(!$r) $this->err('uid错误');
    //   $this->addHisLog($uid,0,'','',LockHisApi::ACTION_REG,1,$pre2);
    // }

    return $username;// '' : 皆已注册
  }
}