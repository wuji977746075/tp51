<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-06-30 15:10:22
 * Description : [bbs Logic V2]
 */

namespace app\src\bbs\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\bbs\model\BbsPost;

class BbsPostLogicV2 extends BaseLogicV2{

  protected function _init(){
    $this->setModel(new BbsPost());
  }

  public static function subPureContent($str='',$len=200){
    $str = str_replace(['&nbsp;',PHP_EOL], '', strip_tags($str));
    $str = mb_substr($str,0,$len).(mb_strlen($str)>$len ? '...' : '');
    return $str;
  }

  // 检查title
  // title: id:排除的帖子id
  public function checkTitle($title='',$id=0){
    if($title){
      $map = ['title'=>$title,'status'=>['in',[0,1]]];
      $id && $map['id'] = ['neq',$id];
      $info = $this->getInfo($map);
      if($info) return returnErr('标题已被使用了');
    }
    return returnSuc('pass');
  }
}