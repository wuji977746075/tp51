<?php

namespace src\datatree;
use  src\base\BaseLogic;

class DatatreeLogic extends BaseLogic{

  public function getItems($name = '',$key='id',$val=null){
    $list = parent::query(['name'=>$name]);
    if($key && $list && isset($list[0][$key])){
      $list = array_column($list,$val,$key);
    }
    return $list;
  }

  public function checkItem($name = '',$id=0){
    return true;
  }
}