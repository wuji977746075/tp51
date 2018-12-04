<?php

namespace src\base\traits;
// 多茶树
trait Jump {
  function err($msg,$code=1,$data=[]){
    // common.php
    throws($msg,$code,$data);
  }
}