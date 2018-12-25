<?php

namespace src\base\traits;
use \ErrorCode;
// 请求参数处理
trait Post {

  /**
   * @param $key
   * @param string $df
   * @param string $nullMsg  未定义时的报错
   * @return mixed
   */
  public function _param($key,$df=null,$nullMsg=null){
    return $this->checkInput(input("param.".$key),$key,$df,$nullMsg);
  }
  public function _post($key,$df=null,$nullMsg=null){
    return $this->checkInput(input("post.".$key),$key,$df,$nullMsg);
  }
  public function _get($key,$df=null,$nullMsg=null){
    return $this->checkInput(input("get.".$key),$key,$df,$nullMsg);
  }
  public function checkInput($val,$key,$df,$nul){
    $name  = preg_replace('/\/\w$/', '', $key);
    if(is_null($val)){
      if(!is_null($nul)){
        $this->err($nul===true ? Lack($name) : $null ,ErrorCode::LACK_PARA);
      }else{
        return $df;
      }
    }
    return $val;
  }
}