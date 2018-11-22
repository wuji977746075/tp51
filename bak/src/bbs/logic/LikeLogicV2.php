<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-07-07 17:45:32
 * Description : [Description]
 */

namespace app\src\bbs\logic;
use app\src\base\logic\BaseLogicV2;
use app\src\bbs\model\Like;

class LikeLogicV2 extends BaseLogicV2 {
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