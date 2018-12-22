<?php
namespace src\base\traits;
// 多茶树
// require : cosnt ERROR
trait Jump {
  protected static function err($sMsg=false,$iCode=-1,$data = []){
    if($iCode === -1){ //未定义错误
      // $sClass = get_called_class();
      // if(defined($sClass::ERROR)){
      //   $iCode = $sClass::ERROR;
      // }else{
      //   $iCode = \ErrorCode::ERROR;
      // }
      $iCode = self::ERROR;
    }
    if($iCode === 0){
      $sMsg = $sMsg!==false ? $sMsg : 'success';
    }else{
      $sMsg = $sMsg!==false ? $sMsg : 'error';
    }
    // common.php
    throws($sMsg,$iCode,$data);
  }

  protected static function suc($data=[],$sMsg=false) {
    self::err($sMsg,0,$data);
  }
}