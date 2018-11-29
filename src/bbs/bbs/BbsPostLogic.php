<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-26 18:03:05
 * Description : [Description]
 */

namespace src\bbs\bbs;
use src\base\BaseLogic;

/**
 * 论坛帖子业务逻辑
 */

class BbsPostLogic extends BaseLogic {
  // 过滤 str
  function filter($s,$replace='*'){
    $ws = explode(' ',getConfig('filter_words'));
    $s = str_replace_all($ws,$replace,$s);
    return $s;
  }
  // check title
  function checkTitle($t,$id=0){
    $t = $this->filter($t);
    $map = ['title'=>$t,'id'=>['neq',$id]];
    $this->getField($map,'id') && throws('title exist');
    return  $t;
  }

  public static function subPureContent($str='',$len=200){
    $str = str_replace(['&nbsp;',PHP_EOL], '', strip_tags($str));
    $str = mb_substr($str,0,$len).(mb_strlen($str)>$len ? '...' : '');
    return $str;
  }

  // 检查title
  // title: id:排除的帖子id
  // public function checkTitle($title='',$id=0){
  //   if($title){
  //     $map = ['title'=>$title,'status'=>['in',[0,1]]];
  //     $id && $map['id'] = ['neq',$id];
  //     $info = $this->getInfo($map);
  //     if($info) return returnErr('标题已被使用了');
  //   }
  //   return returnSuc('pass');
  // }
}
