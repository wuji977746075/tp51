<?php
namespace src\role;

use src\base\BaseLogic;

class UserRoleLogic extends BaseLogic{

  const SUPER_ADMINS = [1];
  const ADMIN        = 1;
  const DEFAULT_ROLE = 2;

  // return int
  public function getRoleId($client_id,$uid){
    $r = $this->getInfo(['uid'=>$uid]);
    return $r ? $r['role_id'] : 0;
  }


  // return void
  public function setRole($uid=0,$role_id=0){
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
