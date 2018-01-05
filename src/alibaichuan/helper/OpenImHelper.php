<?php
namespace src\alibaichuan\helper;

class OpenImHelper{

  public static function getOpenId($uid){ // 默认注册规则
      return 'tbimuser_'.md5($uid);
  }
  public static function getOpenPass($uid){ // 默认注册规则
      return 'itboye';
  }
  /**
   * 获取用户头像的地址
   */
  public static function getAvatarUrl($uid){
      return config('avatar_url').'?uid=' . $uid . '&size=120';
  }

  public static function suc($info='操作成功'){
    return ['status'=>true,'info'=>$info];
  }
  public static function err($info='操作失败'){
    return ['status'=>false,'info'=>$info];
  }
  public static function thr($msg='操作异常'){
    throw new \Exception($msg);
  }
}