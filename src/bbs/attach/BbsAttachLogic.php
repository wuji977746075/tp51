<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-26 18:03:05
 * Description : [Description]
 */

namespace src\bbs\attach;
use src\base\BaseLogic;

/**
 * 论坛板块业务逻辑
 */

class BbsAttachLogic extends BaseLogic {

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