<?php

namespace src\config;
use  src\base\BaseLogic;

class ConfigLogic extends BaseLogic{

  const SYSTEM = 1;

  public function queryGroup($gid=0,$time=600){
    $cacheKey = 'config_query_group';
    $list = cache($cacheKey,'');
    if($list && $time>0) {
    }else{
      $r = parent::query([],false,'value,name,type,group');
      $list = [];
      foreach ($r as $v) {
        $v['value'] = self::_parse($v['value'],$v['type']);
        $list[$v['name']] = [$v['value'],$v['group']];
      } unset($v);
      // $list = array_column($list,false,'name'); // php5.5+
      $time>0 && cache($cacheKey,$list,$time);
    }
    $ret = [];
    foreach ($list as $k=>$v) {
      if(isset($v[1])){
        if($gid>0){
          $v[1] == $gid && $ret[$k] = $v[0];
        }else{
          $ret[$k] = $v[0];
        }
      }
    }
    return $ret;
  }

  public function getConfig($key=''){
    if(config($key)){
      return config($key);
    }else{
      $r = $this->getInfo(['name'=>$key]);
      return $r ? self::_parse($r['value'],$r['type']) : '';
    }
  }

  /**
   * 根据配置类型解析配置
   * @param  string $value 配置值
   * @param  integer $type 配置类型
   * @return array|string
   */
  private static function _parse($value,$type) {

    switch (intval($type)) {
      case 3 :
        $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
        if (strpos($value, ':')) {
          $value = [];
          foreach ($array as $val) {
            list($k, $v) = explode(':', $val,2);
            $value[$k] = $v;
          }
        } else {
          $value = $array;
        }
        break;
    }
    return $value;
  }

  /**
   * old - 设置
   * @return true 设置成功 false 参数不正确
   */
  public function set($config) {
    $effects = 0;
    if ($config && is_array($config)) {
      foreach ($config as $name => $value) {
        $map = ['name' => $name];
        $result = $this -> where($map) -> setField('value', $value);
        if(false !== $result) $effects = $effects + $result;
      }
      if(0 === $effects) return false;
      return $effects;
    }
    return false;
  }
}