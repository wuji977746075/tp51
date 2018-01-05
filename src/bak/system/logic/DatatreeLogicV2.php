<?php
/**
 * @author rainbow 2016-12-15 15:38:20
 */
namespace app\src\system\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\system\model\Datatree;

class DatatreeLogicV2 extends BaseLogicV2{

  protected function _init(){
    $this->setModel(new Datatree());
  }

  public function isParent($id=0,$parentid=0){
    $r = $this->getInfo(['id'=>$id,'parentid'=>$parentid],false,'id,name');
    return $r;
  }

  public function isExistIds($ids='',$parentid=0){
    if(!is_array($ids)) $ids = array_unique(implode(',', $ids));
    $map = ['id'=>['in',$ids]];
    if($parentid) $map['parentid'] = $parentid;

    $r = $this->queryNoPaging($map);
    return count($r) == count($ids);
  }
  /**
   * 根据数据字典id获取名字 - 可选缓存
   * @Author
   * @DateTime 2016-12-20T10:35:14+0800
   * @param    integer                  $id   [description]
   * @param    integer                  $time [description]
   * @return   [string]                       [description]
   */
  public function getNameById($id=0,$time=1800){
    //if cached
    $key = 'datatree_'.$id;
    if($time>0){
      $cache = cache($key);
      if($cache) return $cache;
    }
    //db query
    $r = $this->getInfo(['id'=>$id],false,'id,name');
    $name = $r ? $r['name'] : '';
    //need cache
    if($time>0) $cache = cache($key,$name);
    return $name;
  }
  /**
   * 根据$map获取 某个或某些datatree 并缓存
   * key 为 id
   * @return array
   */
  public function getCacheList(array $map,$field='id,name,code',$time=180,$fresh=false){
    $key = getCacheKey($map,'datatree');$cache = cache($key);
    if(false === $fresh && $cache) return $cache;

    $model = $this->getModel();
    if(empty($field)){
      $r = $model->where($map) ->select(); //return list ;key:0+
      //make object-list to array-list
      $r = Obj2Arr($r,'id');
    }else{
      $r = $model->where($map) ->column($field); //return array ;key:主键 or ?
    }
    if($time<=0) $time = 180;
    if(!$fresh) cache($key,$r,$time);
    return $r;
  }

}
