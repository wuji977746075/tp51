<?php

namespace src\sys\core;
use  src\base\BaseLogic;

class SysDatatreeLogic extends BaseLogic{
  const CACHE_KEY  = 'cache_datatree';

  function getItems($name = '',$key='id',$val=null){
    $list = parent::query(['name'=>$name],'sort desc');
    if($key && $list && isset($list[0][$key])){
      $list = array_column($list,$val,$key);
    }
    return $list;
  }
  function getDatatree($name,$key='id',$val=null){
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
    $list = parent::query([],'sort desc');
    cache($key,$list,$time);
    return $list;
  }


  // 使用datatree : 注意改动需要更新缓存
  public function queryTree2($name='',$cache=true) {
    $ret  = $this->queryCache($cache);
    $list = [];
    // 一级
    foreach ($ret as $k=>$v) {
      if($v['parent'] == 0){
        $list[$v['id']] = $v;
        unset($ret[$k]);
      }
    }
    // 二级
    foreach ($ret as $v) {
      $parent = $v['parent'];
      if(isset($list[$parent])){
        if(isset($list[$parent]['child'])){
          $list[$parent]['child'][] = $v;
        }else{
          $list[$parent]['child'] = [$v];
        }
      }
    }
    return array_values($list);
  }

  public function checkItem($name = '',$id=0){
    return true;
  }
}