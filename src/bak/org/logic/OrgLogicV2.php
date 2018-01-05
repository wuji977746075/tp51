<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-01-12 10:20:42
 * Description : [OrgLogicV2]
 */

namespace app\src\org\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\org\model\Org;
use think\Db;
/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\src\org\logic
 * @example
 */
class OrgLogicV2 extends BaseLogicV2 {

  protected function _init(){
    $this->setModel(new Org());
  }

  //获取最上级机构id
  public function getTopOid($oid=0){
    $r = $this->getInfo(['id'=>$oid]);
    if(!$r) return 0;
    $path = $r['path'];
    if($path){
      $path = explode(',', $path);
      return $path ? (int) $path[0] : 0;
    }else{
      return $oid;
    }
  }

  //获取机构编码类型 (? 层级)
  //1: by        //2: by_hz
  //3: by_hz:it  //4: by_hz:it_php
  public function getTypeByCode($code=''){
    $arr = explode(':', $code);
    $len = count($arr);
    if($len===2){
      $len += count(explode('_',$arr[1]));
    }elseif($len===1){
      $len = count(explode('_',$arr[0]));
    }else{
       throw new \Exception(Linvalid('code'));
    }
    return $len;
  }
  //获取机构类型描述
  public function getTypeMsg($type=0){
    switch ($type) {
      case 1:
        return '公司';
        break;
      case 2:
        return '子公司';
        break;
      case 3:
        return '部门';
        break;
      case 4:
        return '子部门';
        break;

      default:
        return '未知';
        break;
    }
  }
  //删除机构 - 有下级时不能删
  public function del($id=0){
    //? has chilren
    $r = $this ->getInfo(['parent'=>$id]);
    if($r) return returnErr('需要先删除下级机构:'.$r['name']);
    //? id
    $r = $this ->getInfo(['id'=>$id]);
    if(empty($r)) $this->error(Linvalid('id'));

    Db::startTrans();
    //delete
    $this->delete(['id'=>$id]);
    //更新父级 children
    if($r['path']) $this->updateParent($r['path'],$id,false);
    Db::commit();
    return returnSuc(L('success'));
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
  // check name
  public function isExistName($name=''){
    if(empty($name)) throw new \Exception(Llack('name'));
    return $this->getInfo(['name'=>$name]);
  }

  //机构编码: 最多4部分(2机构+2部分)
  //形如:机构_下级机构:部门_下级部门
  //eg:
  //a   a:b   a_b
  //a_b:c     a_b:c_d
  public function isLegalCode($code=''){
    if(empty($code)) throw new \Exception(Llack('code'));
    return preg_match('/^[a-z0-9]+(_[a-z0-9]+)?(:[a-z0-9]+(_[a-z0-9]+)?)?$/i',$code);
  }
  //check code
  public function isExistCode($code=''){
    if(!$this->isLegalCode($code)) return false;
    return $this->getInfo(['code'=>$code]);
  }
}