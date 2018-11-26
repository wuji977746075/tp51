<?php

namespace src\sys\area;
use  src\base\BaseLogic;

class AreaLogic extends BaseLogic{


  public function getChildArea($code = '',$field='code,province,city,district'){
    $id = 1; // 默认中国
    // getIDByCode
    if($code){
      $r  = $this->getInfo(['code'=>$code]);
      $id = $r ? $r['id'] : -1;
    }
    $r = $this->query(['parent'=>$id],false,$field);
    return $r;
  }

  // tocombine
  public function isExistCode($code='',$field='id'){
    $r = $this->getInfo(['areaID'=>$code],false,$field);
    return $r;
  }
}