<?php

namespace src\menu;
use  src\base\BaseLogic;
use src\user\UserLogic;

class MenuLogic extends BaseLogic{

  public function getUserMenu($mid=0,$uid=0,$cache=true){
    $menus = $this->getModuleMenu($mid,$cache);
    $isSuper = true;
    $userMenuIds = []; // todo : array
    if($isSuper){ // 超管
      $ret = $menus;
    }else{
      $ret = [];
      foreach ($menus as $v) {
        $id = $v['id'];
        if(!$v['hide'] && in_array($id,$userMenuIds)){
          $ret[$id] = $v;
        }
      }
    }
    //去掉数组 key
    foreach ($ret as &$v) {
      $v['child'] = array_values($v['child']);
    } unset($v);
    $ret = array_values($ret);
    return $ret;
  }

  public function getModuleMenu($mid=0,$cache=true){
    $menus = $this->getAllMenu($cache);
    if($mid){
      $ret = [];
      foreach ($menus as $v) {
        if(intval($v['module_id']) == $mid){
          $ret[$v['id']] = $v;
        }
      }
      return $ret;
    }else{
      return $menus;
    }
  }
  // 获取模块菜单 / 全部
  public function getAllMenu($cache=true,$level=3){
    $cacheKey = 'sys_menus_all';
    if($cache && cache($cacheKey)) {
      return cache($cacheKey);
    }else{
      $all = $this->query([['level','<=',$level]],'sort asc','id,name,parent,url,icon,params,module_id,hide');
      $ret = [];$top_arr = [];
      foreach ($all as $k=>&$v) { // 一级菜单
        $v['link'] = $v['url'];
        if($v['parent'] ==0){
          $v['child'] = [];
          $ret[$v['id']] = $v;unset($all[$k]);
        }
      } unset($v);
      foreach ($all as $k=>$v) { // 二级菜单
        $parent = intval($v['parent']);
        if(isset($ret[$parent])){
          $v['child'] = [];$top_arr[$v['id']] = $parent;
          $ret[$parent]['child'][$v['id']] = $v;unset($all[$k]);
        }
      }
      foreach ($all as $k=>$v) { // 三级菜单
        $parent = intval($v['parent']);
        $top = $top_arr[$parent];

        if(isset($ret[$top]) && isset($ret[$top]['child'][$parent])){
          $v['url'] = url($v['url'],[],'').($v['params'] ? '?'.$v['params'] : '');
          $ret[$top]['child'][$parent]['child'][] = $v;
        }
      }
      unset($all);unset($top_arr);

      if($cache) cache($cacheKey,$ret);
      return $ret;
    };
  }
}