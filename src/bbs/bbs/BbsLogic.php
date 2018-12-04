<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-26 18:03:05
 * Description : [Description]
 */

namespace src\bbs\bbs;
use src\base\BaseLogic;
// use src\base\traits\Tree;
/**
 * 论坛板块业务逻辑
 */

class BbsLogic extends BaseLogic {
  // use Tree;

  const DEFAULT_BLOCK   = 1;
  const BANNER_POSITION = 6220;
  const MAX_POST_IMG    = 9;
  const MAX_REPLY_IMG   = 3;

  protected $filter_words;
  protected function init(){
    $this->filter_words = getConfig('filter_words');
  }

  // function isValidParent($pos,$parPos){
  //   $info  = $this->isValidInfo($pos,'pos');
  //   $pinfo = $this->isValidInfo($parPos,'pos');
  //   if(!$this->isParent($pos,$parPos)){
  //     $this->err(Linvalid('parent relation'));
  //   }
  // }

  // function hasChildren($id) {
  //   return $this->isValidInfo($id,'parent',L('need-del-down')) ? true : false;
  // }

  public function checkBlock($tid=0){
    $r = $this->getInfo(['id'=>$tid]);
    if(!$r) throws('论坛配置有误');
    if($r['status'] == 1) throws('论坛锁定中,请稍后再试');
    if($r['status'] ==-1) throws('论坛关闭中,请稍后再试');
    return $r;
  }

  // 关键词过滤
  public function filter($str='',$rep='***'){
    return str_replace($this->filter_words, $rep, $str);
  }

  public static function apps($app=''){
    $apps = ['admin','android_driver','android_worker','pc','ios_driver','ios_worker','test','other'];
    if($app) return in_array($app,$apps);
    return $apps;
  }

  //板块的正常下级ids - tid=>直属的/0=>全部
  public function childIds($tid=0,$field='',$time=120){
    $key = 'bbs_child_'.$field.'_'.$tid;
    if($time>0){
      $val = cache($key);
      if($val) return $val;
    }
    $val = $this->queryNoPaging($tid ? ['parent'=>$tid] : []);
    $field && $val = changeArrayKey($val,$field,true);
    if($time>0) cache($key,$val,$time);
    return $val;
  }
  //某板块直属正常帖子数
  public function countPost($tid=0,$time=120){
    $key = 'bbs_post_num_'.$tid;
    if($time>0){
      $val = cache($key);
      if($val) return $val;
    }
    $val = (new BbsPostLogic)->count(['status'=>1,'tid'=>$tid]);
    if($time>0) cache($key,$val,$time);
    return $val;
  }

  //某板块直属正常回复数
  public function countReply($tid=0,$time=120){
    $key = 'bbs_reply_num_'.$tid;
    if($time>0){
      $num = cache($key);
      if($num) return $num;
    }
    $num = (new BbsReplyLogic)->count(['tid'=>$tid]);
    if($time>0) cache($key,$num,$time);
    return $num;
  }

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
