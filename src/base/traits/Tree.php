<?php

namespace src\base\traits;
// 多茶树
trait Tree {
  use Jump;
  public  $posEach = 3; // 每位几个字符,默认3,共46655种
  function __construct(){
    parent::__construct();
    $this->posMax  = str_pad('',$this->posEach,"z",STR_PAD_RIGHT);
    $this->posMin  = str_pad('',$this->posEach,"0",STR_PAD_RIGHT);
  }

  // 补全到位数倍数长的字符串,前置补0,为后面进制转换准备
  function toStr($str) {
    $str = trim($str.'');
    $fix = 0;$lost = strlen($str)%$posEach;
    $lost && $fix = $posEach - $lost;
    return $fix ? str_pad($str,$fix,'0',STR_PAD_LEFT) : $str;
  }

  function getLevel($str){
    return strlen($this->toStr($str))/$posEach;
  }

  protected function fixPos() {
    $list = $this->logic->query(['parent'=>0],'create_time asc');
    $i = 0;
    foreach ($list as $v) {
      echo $i.'=>';
      $pos = $this->logic->getChar($i);
      echo $pos;
      $this->logic->save(['id'=>$v['id']],['pos'=>$pos]);
      $i ++ ;
    }
  }

  function isOutTree($pos,$posNew,$err=true) {
    $err = $err === true ? L('need-out-tree'): $err;
    if($pos == $posNew || $this->isRightParent($posNew,$pos)){
      $err && $this->err($err);
      return false;
    }
    return true;
  }

  function getNewChild($parentId) {
    $posNew = $this->posMin;
    $info = $this->logic->isValidInfo($parentId);
    $pos  = $info['pos'];

    $max  = $this->radix;
    $m = $this->logic->getInfo(['pos'=>['like'=>$pos."_"]],'pos desc','pos');
    if($m){
      $temp   = $m['pos'];
      $tempL  = substr($temp,0,strlen($temp)-1);
      $tempR  = substr($temp,strlen($temp)-1,1);
      $posNew = $tempL.$this->getChar($this->getCharSort($tempR) + 1);
    }
    // $i = base_convert($str,$this->radix,10);
  }
  /**
   * [getSortIndex description]
   * @param  string $posChar [description]
   * @return number|Exception
   */
  function getCharSort($posChar='') {
    $posChar = $this->toStr($posChar);
    if($posChar !== ''){
      $i = strpos($this->posArr,$posChar);
      if($i>-1) return $i;
    }
    $this->err(Linvalid('tree id =>'.$posChar));
  }
  function getChar($index) {
    if($index < $this->radix && $index>-1){
      return $this->posArr[$index];
    }
    $this->err(Linvalid('pos out of range => '.$index));
  }

  function getParent($pos=''){
    $pos = $this->toStr($pos);
    if($pos && $pos)
    return substr($pos,0,strlen($pos)-$this->posEach);
  }

  function isRightParent($posStr,$posPar) {
    $posStr = $this->toStr($posStr);
    $posPar = $this->toStr($posPar);
    if($posStr != $posPar && strpos($posStr,$posPar) == 0) return true;
    return false;
  }

  function getStrSort($posStr='') {
    $posStr = $this->toStr($posStr);
    $l = strlen($posStr);
    for ($i=0; $i < $l; $i++) {
      echo $posStr[$i];
    }
  }

}