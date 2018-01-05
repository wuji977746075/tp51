<?php

namespace src\auth;
use src\base\BaseLogic;
use ErrorCode as EC;

class AuthLogic extends BaseLogic{

  public function getIdByName($name=''){
    $r = $this->getInfo(['name'=>$name]);
    empty($r) && $this->throws('无此权限:'.$name,EC::Business_Error);
    return intval($r['id']);
  }
}