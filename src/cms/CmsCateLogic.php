<?php
namespace src\cms;
use src\base\BaseLogic;

class CmsCateLogic extends BaseLogic{

  public function getUserMenu($mid=0,$uid=0,$cache=true){
    $menus = $this->getAllMenu($cache);
    //去掉数组 key
    foreach ($menu as &$v) {
      $v['child'] = array_values($v['child']);
    } unset($v);
    $ret = array_values($ret);
    return $ret;
  }

  // 获取模块菜单 / 全部
  public function getAllMenu($cache=true,$level=3){
    $cacheKey = 'cms_cates_all';
    if($cache && cache($cacheKey)) {
      return cache($cacheKey);
    }else{
      $all = $this->query([['level','<=',$level]],'sort asc','id,name,parent,url,icon,show');
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
          $v['url'] = url($v['url'],[],'');
          $ret[$top]['child'][$parent]['child'][] = $v;
        }
      }
      unset($all);unset($top_arr);

      if($cache) cache($cacheKey,$ret);
      return $ret;
    };
  }
}