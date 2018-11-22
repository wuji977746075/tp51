<?php

namespace src\sys\auth;
use src\base\BaseLogic;
use ErrorCode as EC;

class AuthLogic extends BaseLogic{

  public function getIdByName($name=''){
    $r = $this->getInfo(['name'=>$name]);
    empty($r) && $this->throws('无此权限:'.$name,EC::Business_Error);
    return intval($r['id']);
  }

  function queryByGroup($f=''){
    $r = $this->query();
    if($f){
      $nodes = [];
      foreach ($r as $v) {
        $t = $v[$f]; // 按模型分组
        if(isset($nodes[$t])){
          $nodes[$t][] = $v;
        }else{
          $nodes[$t] = [$v];
        }
      }
    }else{
      $nodes = $r;
    }
    return $nodes;
  }
}