<?php
namespace src\role;

use src\base\BaseLogic;
use src\user\UserLogic;
use src\role\RoleLogic;

class UserRoleLogic extends BaseLogic{
  // return int
  function getRoleId($uid,$client_id){
    !$uid && throws('uid非法:'.$uid);
    $r = $this->getInfo(['uid'=>$uid]);
    if(UserLogic::isSuper($uid)){
      return -1;
    }else{
      !$r && throws('用户角色非法');
      return $r ? $r['role_id'] : 0;
    }
  }

  // return void
  function setRole($uid=0,$role_id=0){
    $uid     = intval($uid);
    $role_id = intval($role_id);

    // ? uid

    // ? role_id

    $info = $this->getInfo(['uid'=>$uid]);
    if($info){
      $this->save(['uid'=>$uid],['role_id'=>$role_id]);
    }else{
      $this->add(['uid'=>$uid,'role_id'=>$role_id]);
    }
  }
}
