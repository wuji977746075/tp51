<?php
namespace src\cms;
use src\base\BaseLogic;

class CmsCateLogic extends BaseLogic{

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