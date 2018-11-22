<?php

namespace src\sys\core;
use  src\base\BaseLogic;

class ModuleLogic extends BaseLogic{
  const CACHE_KEY  = 'cache_module';

  function getItems($name = '',$key='id',$val=null){
    $list = parent::query(['name'=>$name],'sort desc');
    if($key && $list && isset($list[0][$key])){
      $list = array_column($list,$val,$key);
    }
    return $list;
  }
  function getModal($name,$key='id',$val=null){
    $list = $this->getItems($name,$key,$val);
    if($name != rtrim($name,'_item')){ // 查询 list
      return $list;
    }else{ // 查询单个
      return $list ? $list[0] : [];
    }
  }
  //
  function clearCache() {
    $this->queryCache(false);
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
    $list = parent::query();
    cache($key,$list,$time);
    return $list;
  }
}