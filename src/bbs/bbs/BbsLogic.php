<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-26 18:03:05
 * Description : [Description]
 */

namespace src\bbs\bbs;
use src\base\BaseLogic;

/**
 * 论坛板块业务逻辑
 */

class BbsLogic extends BaseLogic {


  // 获取论坛板块tree / 全部
  public function queryTree($cache=true) {
    $cacheKey = 'sys_bbs_all';
    if($cache && cache($cacheKey)) {
      return cache($cacheKey);
    }else{
      $all = $this->query([],'sort asc','id,name,parent');
      $ret = [];
      foreach ($all as $k=>&$v) { // 一级菜单,key:id
        if($v['parent'] ==0){
          $v['child'] = [];
          $ret[$v['id']] = $v;unset($all[$k]);
        }
      } unset($v);
      foreach ($all as $k=>$v) { // 二级菜单,key:id
        $parent = intval($v['parent']);
        if(isset($ret[$parent])){
          $ret[$parent]['child'][$v['id']] = $v;unset($all[$k]);
        }
      }
      unset($all);

      if($cache) cache($cacheKey,$ret);
      return $ret;
    };
  }
}
