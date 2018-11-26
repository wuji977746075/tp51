<?php
 /**
  * Author      : rainbow <hzboye010@163>
  * DateTime    : 2017-03-17 16:48:46
  * Description : [书籍单元 logic - V2]
  */
namespace app\src\ewt\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\ewt\model\Bookunit;

class BookunitLogicV2 extends BaseLogicV2{

  // 查询书籍的题目
  // 同时返回3级单元的audio_id : 2017-08-12 17:32:14
  // 性能优化去掉order : 2017-12-14 17:21:51
  public function getAudioIdsByBookId($book_id=0){
    // 查询书籍下的所有3级单元下的所有题目(含小题)的音频id
    $model = $this->getModel();
    $map = ['b.level'=>3,'b.book_id'=>$book_id,'q.audio_id'=>['neq',0]];
    $r = $model->alias('b')
    ->join(['itboye_ewe_bookunit_question'=>'bq'],'b.id = bq.unit_id','left')
    ->join(['itboye_ewe_question'=>'q'],'bq.question_id = q.id or bq.question_id=q.parent_id','left')
    ->where($map)->field('distinct(q.audio_id)')->select();
    //->order('q.audio_id asc')
    $ids = [];
    foreach ($r as $v) {
      $ids[] = $v['audio_id'];
    }
    // 查询书籍下3级单元自带的音频文件
    $map = ['level'=>3,'book_id'=>$book_id,'audio_id'=>['neq',0]];
    $r = $model->where($map)->field('distinct(audio_id)')->order('audio_id asc')->select();
    foreach ($r as $v) {
      $ids[] = $v['audio_id'];
    }
    return array_unique($ids);
  }

  public function getAllByBookId($book_id=0){
    $r = $this->queryNoPaging(['book_id'=>$book_id],'sort desc,id asc','id,unit_name,parent_unit,is_free,price,is_tip,is_rand');
    return $this->sortByTree($r);
  }

  //获取某些书籍的1,2级单元
  public function queryLevel12(array $map,$order=false,$field='id,level,parent_unit,unit_name'){
    $ret = [];
    $r = $this->queryNoPaging($map,$order,$field);
    foreach ($r as $v) {//一级
      $parent = (int) $v['parent_unit'];
      $id = (int) $v['id'];
      if($parent == 0){
        $ret[$id] = [
          'unit_id'   =>$v['id'],
          'unit_name' =>$v['unit_name'],
          'child'     =>[],
        ];
      }
    }
    foreach ($r as $v) {//二级
      $parent = (int) $v['parent_unit'];
      $id = (int) $v['id'];
      if(isset($ret[$parent])){
        $ret[$parent]['child'][] = [
          'unit_id'   =>$id,
          'unit_name' =>$v['unit_name'],
        ];
      }
    }
    return array_values($ret);
  }

  //从1级或2级单元查询所属的3级单元 ids
  public function getLevel3Children(array $unit_id){
    $ret = [];$q_ids = [];
    $r = $this->queryNoPaging(['parent_unit'=>['in',$unit_id]],false,'level,id');
    if($r){
      // $ids = getArrColumn($r,'id');
      foreach ($r as $v) {
        $level = (int) $v['level'];
        $id = (int) $v['id'];
        if($level == 3){ //从2级查
          $ret[] = $id;
        }elseif($level == 2){ //从1级查
          $q_ids[] = $id;
        }
      }
      if($q_ids){ //从1级查
        $r = $this->queryNoPaging(['parent_unit'=>['in',$q_ids]],false,'id');
        if($r){
          $ret = array_merge($ret,getArrColumn($r,'id'));
        }
      }
    }
    return $ret;
  }

  private function sortByTree(array $r,$by='parent_unit'){
    $u = [];$send = [];
    foreach ($r as $k => $v) {
      $v['units'] = [];
      $parent = intval($v[$by]);
      if($parent === 0){
        $u[$v['id']] = $v;
        unset($r[$k]);
      }
    }
    foreach ($r as $k=>$v) {
      $v['units'] = [];
      $parent = intval($v[$by]);
      if(isset($u[$parent])){
        $u[$parent]['units'][$v['id']] = $v;
        $send[$v['id']] = $v;
        unset($r['$k']);
      }
    }
    foreach ($r as $v) {
      $parent = intval($v[$by]);
      if(isset($send[$parent])){
        $top = $send[$parent][$by];
        if(isset($u[$top]) && isset($u[$top]['units'][$parent])){
          $u[$top]['units'][$parent]['units'][] = $v;
        }
      }
    }
    foreach ($u as &$v) {
      $v['units'] = array_values($v['units']);
    }
    return array_values($u);
  }
}