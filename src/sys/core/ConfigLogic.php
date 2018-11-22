<?php

namespace src\sys\core;
use  src\base\BaseLogic;

class ConfigLogic extends BaseLogic{
  const CACHE_KEY = 'cache_config';
  const SYSTEM    = 1;

  // 每个访问再 base那已经clear了
  // rebuild config cache
  function clearCache() {
    $sysConfig_new = $this->queryGroup(self::SYSTEM,false);
    \Config::set(['app'=>array_merge($sysConfig_new,config('app.'))]);
  }

  function queryCache($cache=true) {
    $key  = self::CACHE_KEY;
    $time = self::CACHE_TIME;
    if($cache){
      $list = cache($key);
      if($list){
        return $list;
      }
    }
    $list = parent::query([],false,'value,name,type,group');
    cache($key,$list,$time);
    return $list;
  }
  // 启动时已经 无缓存全查
  // 后面有需要的地方尽可能缓存取(不带参数time)
  function queryGroup($gid=0,$cache=true){
    $cacheKey  = self::CACHE_KEY.'_group';
    $cacheTime = self::CACHE_TIME;
    $ret  = [];
    $list = [];
    if($cache){
      $list = cache($cacheKey);
    }else{
      $r = $this->queryCache($cache);
      foreach ($r as $v) {
        $v['value'] = self::_parse($v['value'],$v['type']);
        $ret[$v['name']] = [$v['value'],$v['group']];
      } unset($v);
      cache($cacheKey,$ret,$cacheTime);
      foreach ($ret as $k=>$v) {
        if(isset($v[1])){
          $list[$v[1]][$k] = $v[0];
        }else{
          $list[$v[1]] = [$k=>$v[0]];
        }
      }
    }
    return $gid ? $list[$gid] : $list;
  }

  // todo : time
  public function getConfig($name='',$time=0) {
    if(config($name)) {
      return config($name);
    }else{
      $r = $this->getInfo(['name'=>$name],'id asc','value,type');
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