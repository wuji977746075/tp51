<?php
namespace app\admin\controller;

use src\com\AreaLogic;
use think\Db;

class Common extends CheckLogin{

  public function area(){
    $code = input('code','');
    $r  = (new AreaLogic)->getChildArea($code);
    return json($r);
  }
}