<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-26 18:03:05
 * Description : [Description]
 */

namespace src\bbs\like;
use src\base\BaseLogic;

/**
 * 论坛帖子业务逻辑
 */

class BbsLikeLogic extends BaseLogic {
  const BBS_POST  = 1;
  const BBS_REPLY = 2;

  protected function _init(){
    $this->setModel(new Like());
  }


  public function hasLikePost($uid,$pid){
    if($uid<1) return 0;
    return $this->getInfo(['like_id'=>$pid,'uid'=>$uid,'type_id'=>1]) ? 1:0;
  }

  public function countPost($pid){
    return $this->count(['like_id'=>$pid,'type_id'=>1]);
  }
}