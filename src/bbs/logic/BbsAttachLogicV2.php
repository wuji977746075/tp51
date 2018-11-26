<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-06-30 15:10:22
 * Description : [bbs Logic V2]
 */

namespace app\src\bbs\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\bbs\model\BbsAttach;

class BbsAttachLogicV2 extends BaseLogicV2{

  protected function _init(){
    $this->setModel(new BbsAttach());
  }

  // 更新图片
  public function setImgs($pid,$imgs,$rid=0){
    // 查询现有图片
    $r = $this->queryNoPaging(['pid'=>$pid,'rid'=>$rid],false,'img');

    $imgs_src = changeArrayKey($r,'img',true);
    if($imgs_src){
      $adds_imgs = array_diff($imgs,$imgs_src);
      $dels_imgs = array_diff($imgs_src,$imgs);
    }else{
      $adds_imgs = $imgs;
      $dels_imgs = [];
    }
    if($adds_imgs){
      $adds = [];
      foreach ($adds_imgs as $v) {
        $adds[] = [
          'pid'=>$pid,
          'img'=>$v,
          'rid'=>$rid,
        ];
      }
      $adds && $this->addAll($adds);
    }
    $dels_imgs && $this->delete(['pid'=>$pid,'img'=>['in',$dels_imgs],'rid'=>$rid]);
    return returnSuc('更新成功');
  }
}