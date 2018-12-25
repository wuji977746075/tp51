<?php
/**
 * @Author   : rainbow
 * @Email    : hzboye010@163.com
 * @DateTime : 2016-10-26 18:27:42
 * @Description : [科技侠智能锁V3版接口 待废弃]
 * 科技侠原V3 => 通用接口 的适配器,只用于科技侠,原备份在bak/ScienerV3Domain 2018-08-02 14:35:05
 */
namespace app\index\domain;

// use app\system\api\AllianceHouseModeApi;
// use app\system\api\MessageApi;
// use app\system\model\Message;
// use app\system\model\MemberConfig;
// use app\house\model\Lock;
// use app\house\model\LockKey;
// use app\house\model\LockKeyboard;
// use app\house\api\HouseTagApi;
// use think\Db;
use src\lock\LockAction as Logic;

class ScienerV3Domain extends BaseDomain {
  // public
  protected $business_code = 'scienerV3';
  // public function __construct() {
  //   // $this->config = $this->getConfig();
  //   parent::__construct();
  // }
  // 获取IC卡列表 - remote
  function listIcCards() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,"应用" . $this->client_id . "调用IC卡列表接口");
    $params = $this->parsePost('uid|0|int,lock_id','page|1|int,size|20|int,remote'); // 20-100

    $r = (new Logic)->listIcCards($params);
    $this->checkApiReturn($r);
    $this->suc($r['info']);
  }
  // 添加IC卡 - remote
  function addIcCard() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,"应用" . $this->client_id . "调用添加 IC卡接口");
    $params = $this->parsePost('uid|0|int,lock_id,card_no','start|0|int,end|0|int,card_name');

    $r = (new Logic)->addIcCard($params);
    $this->checkApiReturn($r);
    $this->suc($r['info']);
  }
  // 删除IC卡 - remote
  function delIcCard() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,"应用" . $this->client_id . "调用删除 IC卡接口");
    $params = $this->parsePost('uid|0|int,lock_id,cardId');
    $this->suc((new Logic)->delIcCard($params));
  }
  // 清空IC卡 - remote
  function clearIcCard() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,"应用" . $this->client_id . "调用清空 IC卡接口");
    $params = $this->parsePost('uid|0|int,lock_id');
    $this->suc((new Logic)->clearIcCard($params));
  }

  // 获取用户科技侠 openid
  public function getOpenId() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . "获取用户openid");
    $params = $this->parsePost('uid|0|int');
    extract($params);
    $this->suc((new Logic)->getOpenId($uid));
  }

  // 获取本地/远程键盘密码版本
  public function getKeyboardPwdVersion() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST, $this->client_id . "获取键盘密码版本");
    $params = $this->parsePost('uid|0|int,lock_id');//lockFlagPos|-1|int
    extract($params);
    $this->suc((new Logic)->getKbVersion($lock_id,$uid));
  }
  // 发送密码的记录 - 本地
  public function listKeyboardLog() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,"应用" . $this->client_id . "获取发送密码的记录");
    $params = $this->parsePost('uid|0|int,lock_id','current_page|1|int,per_page|20|int');
    // $params = $this->parsePost('uid|0|int,lock_id','current_page|1|int,per_page|20|int,all|0|int');
    $params['all'] = 0;
    $this->suc((new Logic)->listKeyboardLog($params));
  }
  // 重置用户密码
  // 注意：重置键盘密后原来的用户密码都将失效。在获取键盘密码时提示“密码已经用完”或“没有键盘密码数据”时可以调用该接口生成密码数据。
  public function resetKeyboardPwd() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . ":上传或重置键盘密码");
    $params = $this->parsePost('uid|0|int,lock_id,pwd_info,timestamp|0|int');
    // $params = $this->parsePost('uid|0|int,lock_id,pwdInfo,timestamp|0|int');
    $params['pwdInfo'] = $params['pwd_info'];
    unset($params['pwd_info']);
    $this->suc((new Logic)->resetKeyboardPwd($params));
  }
  // 发送密码 + 本地 - 管理员 或 授权用户
  public function getKeyboardPwd() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . ":获取键盘密码");
    $params = $this->parsePost('uid|0|int,lock_id,pwd_type|0|int,date|0|int,app_time|0|int|需要app时间戳','to_phone,start|0|int,end|0|int'); // req+ app_time
    // $params = $this->parsePost('uid|0|int,lock_id,pwd_type|0|int,app_time|0|int','pwd,to_phone,start|0|int,end|0|int,alias');
    $params['alias'] = '';
    $this->suc((new Logic)->getKeyboardPwd($params));
  }
  // *发送自定义密码 + 本地
  // 3带锁,限时,需先蓝牙添加,失败请重试
  public function addKeyboardPwd() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . ":发送自定义密码");
    $params = $this->parsePost('uid|0|int,lock_id,pwd,start|0|int,end|0|int,app_time|0|int|需要APP时间戳','to_phone'); // req+app_time
    // $params = $this->parsePost('uid|0|int,lock_id,app_time','start|0|int,end|0|int,pwd,pwd_list');
    $this->suc((new Logic)->addKeyboardPwd($params));
  }
  // *删除单个键盘密码 - 管理员
  // 只能删除三代锁,密码版本为4的密码;先sdk蓝牙/网关删除
  public function deleteKeyboardPwd() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . ":删除钥匙");
    $params = $this->parsePost('uid|0|int,lock_id,keyboard_id|0|int|,app_time|0|int|需要APP时间戳',''); // req+app_time
    // $params = $this->parsePost('uid|0|int,lock_id,keyboard_id|0|int,app_time|0|int','');
    $this->suc((new Logic)->deleteKeyboardPwd($params));
  }
  // *修改单个键盘密码 - 管理员
  // 只能修改三代锁,密码版本为4的密码;先sdk蓝牙/网关修改
  public function changeKeyboardPwd() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . ":删除钥匙");
    $params = $this->parsePost('uid|0|int,lock_id,keyboard_id|0|int','pwd,start,end');
    // $params = $this->parsePost('uid|0|int,lock_id,keyboard_id|0|int','pwd,start,end');
    $this->suc((new Logic)->changeKeyboardPwd($params));
  }

  // *重置普通钥匙
  // sdk重置后调用 lockFlagPos +=1
  public function resetAllKey() {

    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,"应用" . $this->client_id . "调用重置普通钥匙接口");
    $params = $this->parsePost('uid|0|int,lock_id');
    // $params = $this->parsePost('uid|0|int,lock_id','lock_flag_pos|0|int');
    $params['lock_flag_pos'] = 0;
    $this->suc((new Logic)->resetAllKey($params));
  }
  // 解冻钥匙 - admin/rented
  public function unlockKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . ":解冻钥匙");
    $params = $this->parsePost('uid|0|int,key_id');
    // $params = $this->parsePost('uid|0|int,key_id','aid|0|int');
    $params['aid'] = 0;
    $this->suc((new Logic)->unlockKey($params));
  }
  // 冻结钥匙 - admin/rented
  public function lockKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . ":冻结钥匙");
    $params = $this->parsePost('uid|0|int,key_id');
    // $params = $this->parsePost('uid|0|int,key_id','aid|0|int');
    $params['aid'] = 0;
    $this->suc((new Logic)->lockKey($params));
  }
  // 删除钥匙 - admin/rented
  // 删除管理员钥匙会同时删除该锁的所有普通钥匙和密码
  public function deleteKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . ":删除钥匙");
    $params = $this->parsePost('uid|0|int,lock_id,key_id','auth_out|0|int');
    // $params = $this->parsePost('uid|0|int,key_id','aid|0|int');
    $params['aid'] = 0;
    unset($params['lock_id']);
    $this->suc((new Logic)->deleteKey($params));
  }
  // 删除所有普通钥匙 - admin
  public function deleteAllKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . ":删除钥匙");
    $params = $this->parsePost('uid|0|int,lock_id','auth_out|0|int');
    // $params = $this->parsePost('uid|0|int,lock_id','aid|0|int');
    $params['aid'] = 0;
    $this->suc((new Logic)->deleteAllKey($params));
  }
  // 修改钥匙有效期
  public function changePeriod() {
    $this->checkVersion($this->api_ver);
    // addHisLog($_GET,$_POST,$this->client_id . "修改钥匙有效期");
    $params = $this->parsePost('uid|0|int,lock_id,key_id,start|0|int,end|0|int');
    // $params = $this->parsePost('uid|0|int,lock_id,key_id,start|0|int,end|0|int','aid|0|int');
    $params['aid'] = 0;
    $this->suc((new Logic)->changePeriod($params));
    // 发送钥匙 科技侠成功后同步钥匙
  }
  // 发送钥匙 成功后同步钥匙 =>V3 发送普通钥匙
  public function sendKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . ":发送钥匙");
    $params = $this->parsePost('uid|0|int,to_mobile,lock_id,send_type|-1|int|发送类型','start|0|int,end|0|int,mark');
    // $params = $this->parsePost('uid|0|int|发送者,to_mobile|||接收者手机号,lock_id|||锁id,send_type|-1|int|发送类型','start|0|int,end|0|int,mark,alias,aid|0|int');
    // $params['send_type'] = LockKey::USER;
    // $params['alias']     = '';
    $params['aid']       = 0;
    // try{
      $r = (new Logic)->sendKey($params);
    // }catch(\Exception $e){
    //   dump($e->getTrace());die();
    // }
    $this->suc($r);
  }
  // 用户钥匙列表 - 本地
  // 只查科技侠 2018-01-04 17:26:09
  public function listUserkey() {
    $this->checkVersion($this->api_ver);
    // addHisLog($_GET,$_POST,"应用" . $this->client_id . "调用用户钥匙列表");
    $params = $this->parsePost('uid|0|int','kword');
    $this->suc((new Logic)->listUserKey($params,6323));
  }
  // 根据时间同步用户钥匙
  public function syncUserKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . ":同步用户钥匙");
    $params = $this->parsePost('uid|0|int','last_update_time|0|int');
    // extract($this->parsePost('uid|0|int','last_update_time|0|int'));
    extract($params);
    $this->suc((new Logic)->syncKey($uid,$last_update_time,'sciener_'));
  }
  // 锁的普通钥匙列表 - local
  public function listKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . ":锁钥匙列表");
    $params = $this->parsePost('uid|0|int,lock_id');
    $this->suc((new Logic)->listKey($params));
  }
  // 推送开关设置
  public function ifPush() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . "调用开锁推送开关接口");
    $params = $this->parsePost('uid|0|int,lock_id,push|0|int');
    $this->suc((new Logic)->ifPush($params));
  }
  // 锁 - 低电量
  public function lowPower() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . "锁低电量");
    $params = $this->parsePost('uid|0|int,lock_id,power|100|int');
    $this->suc((new Logic)->setPower($params));
  }
  // * 开锁回调 + 上传开锁记录
  // 注意 : 失败要重试
  public function unlock() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . "调用开锁上传接口");
    $params = $this->parsePost('uid|0|int,lock_id,records,success|-1|int,power|0|int');
    // $params = $this->parsePost('uid|0|int,lock_id','records,power|0|int,reset_rent_pass|0|int,success|1|int,unlock_time|0|int');
    // $params['power'] = 0;
    $params['reset_rent_pass'] = 0;
    $params['unlock_time'] = time();
    $this->suc((new Logic)->unlock($params));
  }
  // 锁授权 与 取消 => 发送租户钥匙 + 删除钥匙
  public function auth() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . ":锁授权与取消");
    $params = $this->parsePost('uid|0|int,to_uid|0|int,lock_id','auth|-1|int,start|-1|int,end|-1|int');
    $auth = $params['auth'];
    if(!in_array($auth,[0,1])) $this->apiReturnErr('auth非法');
    $to_uid    = $params['to_uid'];
    if($auth){
      // 发送租户钥匙
      // $params = $this->parsePost('uid|0|int|发送者,to_mobile|||接收者手机号,lock_id|||锁id,send_type|-1|int|发送类型','start|0|int,end|0|int,mark,alias,aid|0|int');
      $to_uinfo  = checkUID($to_uid);
      $params['to_mobile'] = $to_uinfo['mobile'];
      $params['send_type'] = LockKey::RENT;
      $params['mark']  = '';
      $params['alias'] = '';
      $params['aid']   = 0;
      unset($params['auth']);
      $this->suc((new Logic)->sendKey($params));
    }else{ // 删除租户钥匙
      $r = $this->getLockKey(['lock_id'=>$lock_id,'uid'=>$to_uid]);
      // $params = $this->parsePost('uid|0|int,key_id','aid|0|int');
      $this->suc((new Logic)->deleteKey(['uid'=>$uid,'key_id'=>$r['key_id'],'aid'=>0]));
    }
  }
  // 开锁记录 - 远程
  public function listRecord() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,$this->client_id . ":锁开锁记录");

    $params = $this->parsePost('uid|0|int,lock_id,current_page|1|int,per_page|20|int');
    // $params = $this->parsePost('uid|0|int,lock_id,current_page|1|int,per_page|20|int','latest|0|int');
    $params['latest'] = 0;
    $this->suc((new Logic)->listRecord($params));
  }
  // 绑定房源
  // public function bindHouse() {
  //   $this->checkVersion($this->api_ver);
  //   // addTestLog($_GET,$_POST$this->client_id . ":锁绑定房源");
  //   $params = $this->parsePost('uid|0|int,lock_id,house_no');
  //   try {
  //     //更新房源模式信息
  //     $house_no = $params['house_no'];
  //     (new AllianceHouseModeApi)->updateHouseInfo($house_no);
  //   } catch (\Exception $e) {

  //   }
  //   $this->suc((new Logic)->bindHouse($params));
  // }
  // 解绑房源
  // public function unbindHouse() {
  //  $this->checkVersion($this->api_ver);
  //   // addTestLog($_GET,$_POST,$this->client_id . ":锁解绑房源");
  //   $params = $this->parsePost('uid|0|int,lock_id,house_no');
  //   $this->suc((new Logic)->unbindHouse($params));
  // }
  // *智能锁绑定管理员 - 电量低调用推送接口
  // 注意 : 失败要重试
  public function init() {
    $this->checkVersion($this->api_ver);
    addTestLog($_GET,$_POST,$this->client_id . ":锁初始化");
    $params = $this->parsePost('uid|0|int,lock_type|0|int,lockName,lockAlias,lockMac,lockKey,aesKeyStr,pwdInfo,specialValue|-1|int,lockVersion,electricQuantity|0|int','lockFlagPos|0|int,adminPwd,noKeyPwd,deletePwd,timestamp|0|int');
    // $params = $this->parsePost('uid|0|int,lockName,lockAlias,lockMac,lockKey,aesKeyStr,pwdInfo,lockVersion','lockFlagPos|0|int,adminPwd,noKeyPwd,deletePwd,timestamp,specialValue|-1|int,electricQuantity|100|int,modelNum,hardwareRevision,firmwareRevision');
    $params['lock_type'] = Logic::SCIENER;
    // $params['electricQuantity'] = 100;
    $params['modelNum'] = '';
    $params['hardwareRevision'] = '';
    $params['firmwareRevision'] = '';
    $this->suc((new Logic)->init($params));
  }
  // 修改锁信息 - 仅别名
  public function edit() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,"应用" . $this->client_id . "调用智能锁修改别名接口");
    $params = $this->parsePost('uid|0|int,lock_id,alias');
    $this->suc((new Logic)->edit($params));
  }
  // 换绑科技侠账号
  // 将已有的未绑定住家账号的科技侠账号 绑定到 住家账号,可打通锁
  public function bind() {
    $this->checkVersion($this->api_ver);
    $params = $this->parsePost('uid|0|int,name,pass','lock_type|6323|int');
    // $params = $this->parsePost('uid|0|int,name,pass,lock_type|0|int');
    $this->suc((new Logic)->bind($params));
  }
  // 删除科技侠
  public function unbind() {
    $this->checkVersion($this->api_ver);
    $params = $this->parsePost('uid|0|int','lock_type|6323|int');
    // $params = $this->parsePost('uid|0|int,lock_type|0|int');
    $this->suc((new Logic)->unbind($params));
  }
  // 注册科技侠账号并保存 - ok
  public function reg() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,"应用" . $this->client_id . "调用智能锁注册接口");
    $uid = (int) $this->_post('uid','','缺失uid');
    $this->suc((new Logic)->regSciener($uid));
  }

  // reg's reply - ok
  // 注册+登陆 依赖 请不要丢弃
  public function regSciener($uid) {
    return returnSuc( (new Logic)->regSciener( intval($uid) ) );
  }
}