<?php
/**
 * @Author   : rainbow
 * @Email    : hzboye010@163.com
 * @DateTime : 2016-10-26 18:27:42
 * @Description : [智能锁通用接口 api入口]
 * 记录操作日志(查询外) itboye_locks_his : 2017-10-25 14:36:46
 */
namespace app\index\domain;

use src\user\member\MemberConfig;
use src\lock\lock\Lock;
use src\lock\lock\LockKey;
use src\lock\lock\LockKeyboard;
use src\lock\LockAction as Logic;

// use app\house\api\HouseTagApi;
// use think\Db;
// use app\system\api\AllianceHouseModeApi;
// use app\system\api\MessageApi;
// use app\system\model\Message;

final class Sciener2Domain extends BaseDomain {
  protected $business = 'sciener2';

  //////////////////////////////////////////////////////////////////////
  //------------------ sciener -----------------------------------------
  //////////////////////////////////////////////////////////////////////
  // 获取用户科技侠 openid
  public function getOpenId() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . "获取用户openid");
    $params = $this->parsePost('uid|0|int');
    extract($params);
    $this->suc((new Logic)->getOpenId($uid));
  }

  // 管理员转移
  public function changeAdmin() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . "获取用户openid");
    $params = $this->parsePost('uid|0|int,lock_id,to_mobile');
    $this->suc((new Logic)->changeAdmin($params));
  }

  // *科技侠绑定管理员
  // lock_type : 6323=>科技侠,6587=>微技术(已转移到initSitri)
  // lockVersion : json {,,,,}
  // 注意 : 失败要重试
  public function init() {
    $this->checkVersion($this->api_ver);
    addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":锁初始化");
    $params = $this->parsePost('uid|0|int,lockName,lockAlias,lockMac,lockKey,aesKeyStr,pwdInfo,lockVersion','lockFlagPos|0|int,adminPwd,noKeyPwd,deletePwd,timestamp,specialValue|-1|int,electricQuantity|100|int,modelNum,hardwareRevision,firmwareRevision');
    $params['lock_type'] = Logic::SCIENER;
    $this->suc((new Logic)->init($params));
  }

  // 获取科技侠键盘密码版本
  public function getKeyboardPwdVersion() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST, CLIENT_ID_REQ . "获取键盘密码版本");
    $params = $this->parsePost('uid|0|int,lock_id');//lockFlagPos|-1|int
    extract($params);
    $this->suc((new Logic)->getKbVersion($lock_id,$uid));
  }
  // 同步用户的所有科技侠锁钥匙
  public function syncUserKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":同步用户钥匙");
    extract($this->parsePost('uid|0|int','last_update_time|0|int'));
    $this->suc((new Logic)->syncKey($uid,$last_update_time));
  }
  // 换绑智能锁账号
  // 将已有的智能锁账号 绑定到 未绑定的住家账号
  // 可打通APP登陆(科技侠)
  public function bind() {
    $this->checkVersion($this->api_ver);
    $params = $this->parsePost('uid|0|int,name,pass,lock_type|0|int');
    $this->suc((new Logic)->bind($params));
  }
  // 取消绑定智能锁账号(单个)
  public function unbind() {
    $this->checkVersion($this->api_ver);
    $params = $this->parsePost('uid|0|int,lock_type|0|int');
    $this->suc((new Logic)->unbind($params));
  }
  // 注册智能锁账号并保存
  public function reg() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,"应用" . CLIENT_ID_REQ . "调用智能锁注册接口");
    $uid = (int) $this->_post('uid','','缺失uid');
    $this->suc((new Logic)->regSciener($uid));
  }




  //////////////////////////////////////////////////////////////////////////
  //------------------ sitri   ------------------------------------------ //
  //////////////////////////////////////////////////////////////////////////
  // 百马锁获取自定义密码前置 + 百马sdk蓝牙添加密码前置操作
  public function preAddKeyboardPwd() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":百马发送自定义密码前置");
    $params = $this->parsePost('uid|0|int,lock_id');
    $this->suc((new Logic)->preAddKeyboardPwd($params));
  }
  // 注册百马成功回调
  public function regSitri() {
    $this->checkVersion($this->api_ver);
    $uid = (int) $this->_post('uid','','缺失uid');
    $this->suc((new Logic)->regSitri($uid));
  }
  // 是否注册了百马
  public function isRegSitri() {
    $this->checkVersion($this->api_ver);
    $uid = (int) $this->_post('uid','','缺失uid');
    $this->suc((new Logic)->isRegSitri($uid));
  }
  // 检查绑定 : 门锁 : 微技术
  // public function checkBind() {
  //   $this->checkVersion($this->api_ver);
  //   // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":检查锁绑定");
  //   $params = $this->parsePost("uid|0|int,lockMac");
  //   $this->suc((new Logic)->checkBind($params));
  // }
  // 锁是否初始化了
  public function isInit() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":检查锁绑定");
    $params = $this->parsePost("lockMac");
    $this->suc((new Logic)->isInit($params));
  }
  // 百马锁初始化回调
  public function initSitri() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":检查锁绑定");
    $params = $this->parsePost("uid|0|int,lockMac,lockName,lockAlias,pwdInfo,app_time");
    $this->suc((new Logic)->initSitri($params));
  }



  /////////////////////////////////////////////////////////////////////////
  //------------------ public ------------------------------------------ //
  /////////////////////////////////////////////////////////////////////////
  // 钥匙列表 - admin
  public function viewKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . "查询智能锁钥匙列表");
    $params = $this->parsePost('','kword,current_page|1|int,per_page|10|int,order|create_time desc,lock_id');
    $this->suc((new Logic)->viewKey($params));
  }
  // 钥匙列表-管理员在前 - admin
  public function viewKeyOrderByType() {
      $this->checkVersion($this->api_ver);
      // addTestLog($_GET,$_POST,CLIENT_ID_REQ . "查询智能锁钥匙列表");
      $params = $this->parsePost('','kword,current_page|1|int,per_page|10|int,order|type asc,lock_id');
      $this->suc((new Logic)->viewKey($params));
  }
  // 锁列表 - admin
  public function view() {
    $this->checkVersion($this->api_ver);
    addTestLog($_GET,$_POST,CLIENT_ID_REQ . "查询锁列表");
    $params = $this->parsePost('','kword,current_page|1|int,per_page|10|int,order|create_time desc,uid|0|int,house_no,lock_type');
    $this->suc((new Logic)->view($params));
  }
  // 发送密码的记录 - 本地
  public function listKeyboardLog() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,"应用" . CLIENT_ID_REQ . "获取发送密码的记录");
    $params = $this->parsePost('uid|0|int,lock_id','current_page|1|int,per_page|20|int,all|0|int');
    $this->suc((new Logic)->listKeyboardLog($params));
  }
  // 重置用户密码
  public function resetKeyboardPwd() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":上传或重置键盘密码");
    $params = $this->parsePost('uid|0|int,lock_id,pwdInfo,timestamp|0|int');
    $this->suc((new Logic)->resetKeyboardPwd($params));
  }
  // 发送密码 + 本地 - admin
  public function getKeyboardPwd() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":获取键盘密码");
    $params = $this->parsePost('uid|0|int,lock_id,pwd_type|0|int,app_time|0|int','pwd,to_phone,start|0|int,end|0|int,alias');
    $this->suc((new Logic)->getKeyboardPwd($params));
  }
  // *发送自定义密码 + 本地 + sdk蓝牙操作回调
  // 科技侠 : 3代锁,限时,需先蓝牙添加,失败请重试
  public function addKeyboardPwd() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":发送自定义密码");
    $params = $this->parsePost('uid|0|int,lock_id,app_time','start|0|int,end|0|int,pwd,pwd_list');
    $this->suc((new Logic)->addKeyboardPwd($params));
  }
  // *删除单个键盘密码 - 管理员
  // 科技侠 : 三代锁,密码版本为4的密码;先sdk蓝牙/网关删除
  public function deleteKeyboardPwd() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":删除钥匙");
    $params = $this->parsePost('uid|0|int,lock_id,keyboard_id|0|int,app_time');
    $this->suc((new Logic)->deleteKeyboardPwd($params));
  }
  // *修改单个键盘密码 - 管理员
  // 只能修改三代锁,密码版本为4的密码;先sdk蓝牙/网关修改
  public function changeKeyboardPwd() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":删除钥匙");
    $params = $this->parsePost('uid|0|int,lock_id,keyboard_id|0|int','pwd,start,end');
    $this->suc((new Logic)->changeKeyboardPwd($params));
  }
  // 密码别名 - 本地 - 管理员或租户
  public function editKeyboardPwd() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":获取键盘密码");
    $params = $this->parsePost('uid|0|int,lock_id,keyboard_id|0|int,alias');
    $this->suc((new Logic)->editKeyboardPwd($params));
  }
  // *重置普通钥匙  sdk重置后调用
  public function resetAllKey() {
    $this->err('已废弃');
  //   $this->checkVersion($this->api_ver);
  //   // addTestLog($_GET,$_POST,"应用" . CLIENT_ID_REQ . "调用重置普通钥匙接口");
  //   $params = $this->parsePost('uid|0|int,lock_id','lock_flag_pos|0|int');
  //   $this->suc((new Logic)->resetAllKey($params));
  }
  // 解冻钥匙 - admin/rented
  // todo
  public function unlockKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":解冻钥匙");
    $params = $this->parsePost('uid|0|int,key_id','aid|0|int');
    $this->suc((new Logic)->unlockKey($params));
  }
  // 冻结钥匙 - admin/rented
  public function lockKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":冻结钥匙");
    $params = $this->parsePost('uid|0|int,key_id','aid|0|int');
    $this->suc((new Logic)->lockKey($params));
  }
  // 删除钥匙 - admin/rented
  // 删除管理员钥匙会同时删除该锁的所有普通钥匙和密码
  public function deleteKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":删除钥匙");
    $params = $this->parsePost('uid|0|int,key_id','aid|0|int');
    $this->suc((new Logic)->deleteKey($params));
  }
  // 删除所有普通钥匙 - 管理员
  public function deleteAllKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":删除钥匙");
    $params = $this->parsePost('uid|0|int,lock_id','aid|0|int');
    $this->suc((new Logic)->deleteAllKey($params));
  }
  // 修改钥匙有效期
  public function changePeriod() {
    $this->checkVersion($this->api_ver);
    // addHisLog($_GET,$_POST,CLIENT_ID_REQ . "修改钥匙有效期");
    $params = $this->parsePost('uid|0|int,lock_id,key_id,start|0|int,end|0|int','aid|0|int');
    $this->suc((new Logic)->changePeriod($params));
  // 发送钥匙 科技侠成功后同步钥匙
  }
  // 设置钥匙别名 - local
  public function editKey() {
    $this->checkVersion($this->api_ver);
    // addHisLog($_GET,$_POST,CLIENT_ID_REQ . "设置钥匙别名");
    $params = $this->parsePost('uid|0|int,key_id,mark','aid|0|int');
    $this->suc((new Logic)->editKey($params));
  }
  // 解绑锁
  // public function unbindLock() {
  //   $this->checkVersion($this->api_ver);
  //   // addHisLog($_GET,$_POST,CLIENT_ID_REQ . "解绑锁");
  //   $params = $this->parsePost('lock_id','aid|0|int,key_id,curl|1|int');
  //   try {
  //     $lock_id = $params['lock_id'];
  //     $Lock = Lock::get(['lock_id' => $lock_id]);
  //   } catch (\Exception $e) {

  //   }
  //   $result = (new Logic)->unbindLock($params);
  //   try {
  //     //更新房源模式信息
  //     if (!is_null($Lock)) (new AllianceHouseModeApi)->updateHouseInfo($Lock['house_no']);
  //   } catch (\Exception $e) {

  //   }
  //   $this->suc($result);
  // }
  // 发送钥匙
  public function sendKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":发送钥匙");
    $params = $this->parsePost('uid|0|int|发送者,to_mobile|||接收者手机号,lock_id|||锁id,send_type|-1|int|发送类型','start|0|int,end|0|int,mark,alias,aid|0|int');
    $this->suc((new Logic)->sendKey($params));
  }
  // 用户钥匙列表 - 本地
  public function listUserkey() {
    $this->checkVersion($this->api_ver);
    // addHisLog($_GET,$_POST,"应用" . CLIENT_ID_REQ . "调用用户钥匙列表");
    $params = $this->parsePost('uid|0|int','kword');
    $this->suc((new Logic)->listUserKey($params));
  }
  // 锁的操作记录 - admin
  public function listHis() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":同步用户钥匙");
    $params = $this->parsePost('lock_id','current_page|1|int,per_page|10|int,latest|1|int,owner_uid|0|int,aid|0|int');
    $r = (new Logic)->listHis($params);
    $this->checkApiReturn($r);
    $this->suc($r['info']);
  }
  // 锁的普通钥匙列表 - admin/rent
  public function listKey() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":锁钥匙列表");
    $params = $this->parsePost('uid|0|int,lock_id');
    $this->suc((new Logic)->listKey($params));
  }
  // 推送开关设置
  public function ifPush() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . "调用开锁推送开关接口");
    $params = $this->parsePost('uid|0|int,lock_id,push|0|int');
    $this->suc((new Logic)->ifPush($params));
  }
  // 设置锁电量
  public function setPower() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . "锁低电量");
    $params = $this->parsePost('uid|0|int,lock_id,power|100|int');
    $this->suc((new Logic)->setPower($params));
  }
  // * 开锁回调
  // 上传电量 : 2017-11-07 16:44:59
  // 注意 : 失败要重试
  public function unlock() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . "调用开锁上传接口");
    $params = $this->parsePost('uid|0|int,lock_id','records,power|0|int,reset_rent_pass|0|int,success|1|int,unlock_time|0|int');
    $this->suc((new Logic)->unlock($params));
  }
  // 租户信息 (发送租户钥匙时)
  public function getRentInfo() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . "调用开锁上传接口");
    $params = $this->parsePost('uid|0|int,lock_id');
    $this->suc((new Logic)->getRentInfo($params));
  }
  // 开锁记录 - 远程
  public function listRecord() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":锁开锁记录");

    $params = $this->parsePost('uid|0|int,lock_id,current_page|1|int,per_page|20|int','latest|0|int');
    $this->suc((new Logic)->listRecord($params));
  }
  // 绑定房源
  // public function bindHouse() {
  //   $this->checkVersion($this->api_ver);
  //   // addTestLog($_GET,$_POSTCLIENT_ID_REQ . ":锁绑定房源");
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
  // 同时删除智能锁tag : 2017-10-24 16:00:32
  // public function unbindHouse() {
  //   $this->checkVersion($this->api_ver);
  //   // addTestLog($_GET,$_POST,CLIENT_ID_REQ . ":锁解绑房源");
  //   $params = $this->parsePost('uid|0|int,lock_id,house_no');
  //   $this->suc((new Logic)->unbindHouse($params));
  // }
  //修改锁信息 - 当前仅别名
  public function edit() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,"应用" . CLIENT_ID_REQ . "调用智能锁修改别名接口");
    $params = $this->parsePost('uid|0|int,lock_id,alias');
    $this->suc((new Logic)->edit($params));
  }

  // 获取名下的锁
  // local : 2017-10-24 15:15:37
  public function lock() {
    $this->checkVersion($this->api_ver);
    // addTestLog($_GET,$_POST,"应用" . CLIENT_ID_REQ . "调用查询名下锁接口");
    $params = $this->parsePost('uid|0|int','kword,page|1|int,size|10|int,order|create_time desc,house_no,remote|0|int');
    $this->suc((new Logic)->lock($params));
  }
}