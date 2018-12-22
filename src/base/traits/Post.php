<?php

namespace src\base\traits;
// 请求参数处理
trait Post {

  /**
   * @param $key
   * @param string $default
   * @param string $nullMsg  未定义时的报错
   * @return mixed
   */
  public function _param($key,$default=null,$nullMsg=null){
    return $this->checkParamNull(input("param.".$key),$key,$default,$nullMsg);
  }
  public function _post($key,$default=null,$nullMsg=null){
    return $this->checkParamNull(input("post.".$key),$key,$default,$nullMsg);
  }
  public function _get($key,$default=null,$nullMsg=null){
    return $this->checkParamNull(input("get.".$key),$key,$default,$nullMsg);
  }
  public function checkParamNull($val,$key,$df,$nul){
    $name  = preg_replace('/\/\w$/', '', $key);
    if(is_null($val)){
      if(!is_null($nul)){
        $this->err($nul===true ? Lack($name) : $null ,EC::LACK_PARA);
      }else{
        return $df;
      }
    }
    return $val;
  }
}