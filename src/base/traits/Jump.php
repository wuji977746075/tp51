<?php

namespace src\base\traits;
// 多茶树
trait Jump {
  protected static function err($msg,$code=-1,$data=[]){
    if($code === -1){ //未定义错误
      $code = self::ERROR;
    }
    // common.php
    throws($msg,$code,$data);
  }
}