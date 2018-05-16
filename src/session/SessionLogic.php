<?php

namespace src\session;
use src\base\BaseLogic;
use src\user\UserLogic;

class SessionLogic extends BaseLogic{

  const LIFE_TIME = 86400; // 1天
  const ADMIN_KEY = 'admin_user';
  // return
  public static function getSessionId(){
    return  session_id();
  }
  /**
   * 后台用户注销
   * 后台登陆 session , 记录sid , check session
   * api登陆  sid , check sid
  */
  public static function adminLogout(){
    session(null);
    session("[destroy]");
  }
  // return
  public static function isAdminLogin(){
    return intval(session(self::ADMIN_KEY));
  }
  // 添加 session ,登一次记一次
  // return
  // throws
  public function add($uid,$device_token='',$device_type='',$login_info=[],$life_time=self::LIFE_TIME){
    $now   = time();
    // 至少5分钟
    $life_time = min(intval($life_time),300);
    $sid = md5($device_token.rand(100000,999999).$now).get_36HEX($uid);
    $add = [
        'uid'         => $uid,
        'session_id'  => $sid,
        'login_info'  => json_encode($login_info),
        'device_token'=> $device_token,
        'device_type' => $device_type,
        // 'update_time'=> $now,
        // 'create_time'=> $now,
        'expire_time' => $now + $life_time,
    ];
    if(parent::add($add)) return $sid;
    throws('add session err');
  }

  // api验证用 , 未过期则顺延 , 所有接口带sid(? token)
  // throws
  public function check($uid,$sid='',$life_time=self::LIFE_TIME){
    $now = time();
    // 至少5分钟
    $life_time = max(intval($life_time),300);

    $map = ['uid'=>$uid,'session_id'=>$sid];
    $info = $this->getInfo($map);
    empty($info) && throws('会话错误,请重新登陆');

    $id = $info['id'];
    $expire_time = intval($info['expire_time']);
    ($now > $expire_time) && throws('会话已过期,请重新登陆');
    // 会话顺延
    !($this->saveByID($id,['expire_time'=>$now + $life_time])) && throws('会话错误');
  }
}