<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-01-16 13:44:56
 * Description : [Description]
 */

namespace app\src\auth\logic;
use app\src\base\logic\BaseLogicV2;
use app\src\auth\model\Resource;

/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\src\logic
 * @example
 */
class ResourceLogicV2 extends BaseLogicV2 {
  // init
  protected function _init(){
    $this->setModel(new Resource());
  }

  //更新机构父类的 children字段
  public function updateParent($path='',$id=0,$plus=true){
    if($path && $id){
      $p_infos = $this->queryNoPaging(['id'=>['in',$path]]);
      foreach ($p_infos as $v) {
        $parent = $v['id'];
        $children = $v['children'];
        if($plus){ //add
          $children .= $children ? ','.$id : $id;
        }else{  //delete
          $temp  = array_unique(explode(',', $children));
          $tempa = [];
          foreach ($temp as $v1) {
            if($v1==$id) continue;
            if(empty($v1)) continue;
            $tempa[] = $v1;
          }
          $children = implode(',', $tempa);
        }
        $this->save(['id'=>$parent],['children'=>$children]);
      }
    }else{
      throw new \Exception(Linvalid('path or id'));
    }
  }

}