<?php

namespace src\sys\core;
use  src\base\BaseLogic;

class SysModelLogic extends BaseLogic{
  const CACHE_KEY  = 'cache_modal';

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
  function queryCache($cache=false) {
    $key  = self::CACHE_KEY;
    $time = self::CACHE_TIME;
    if($cache){
      $list = cache($key);
      if($list){
        return $list;
      }
    }
    $list = $this->getModel()->alias('ml')
    ->join(['f_sys_module'=>'me'],'ml.module_id = me.id','left')
    ->field('ml.*,me.name as module_name,me.title as module_title')
    ->select();
    //think\model\Collection
    $list =  $list ? obj2Arr($list) : [];
    cache($key,$list,$time);
    return $list;
  }
}