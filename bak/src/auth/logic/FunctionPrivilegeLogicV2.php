<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-01-16 13:44:56
 * Description : [Description]
 */

namespace app\src\auth\logic;
use app\src\base\logic\BaseLogicV2;
use app\src\auth\model\FunctionPrivilege;
use app\src\auth\logic\OperatorLogicV2;
use think\Db;
/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\src\logic
 * @example
 */
class FunctionPrivilegeLogicV2 extends BaseLogicV2 {
  // init
  protected function _init(){
    $this->setModel(new FunctionPrivilege());
  }

  //获取所有操作 并查询此资源是否已设置了
  public function getOptsWithRes($id=0){
    $opts = (new OperatorLogicV2())->queryNoPaging();
    if(empty($opts)) return [];
    $map  = ['resource_id'=>$id];
    $func = $this->queryNoPaging($map);
    $has = [];
    foreach ($func as $v) {
      $has[] = $v['operator_id'];
    }
    foreach ($opts as &$v) {
      $v['has_op'] = (int) in_array($v['id'],$has);
    }
    return $opts;
  }

  public function setOptsWithRes($id=0,array $op_ids){
    if($op_ids && $id){
      //get res_info
      $res = (new ResourceLogicV2())->getInfo(['id'=>$id]);
      if(empty($res)) $this->error(Linvalid('id'));
      //get all opts
      $opts = (new OperatorLogicV2())->queryNoPaging();
      if(empty($opts)) $this->error('请先定义操作');
      $opts = changeArrayKey($opts,'id');
      //get has opts
      $map  = ['resource_id'=>$id];
      $func = $this->queryNoPaging($map);
      $func = changeArrayKey($func,'operator_id');
      $old_ids = array_unique(array_keys($func));
      $new_ids = array_unique($op_ids);

      $del_ids = array_diff($old_ids, $new_ids);
      $add_ids = array_diff($new_ids, $old_ids);

      Db::startTrans();
      if($del_ids){
        $this->delete(['resource_id'=>$id,'operator_id'=>['in',$del_ids]]);
      }
      if($add_ids){
        $maps = [];
        foreach ($add_ids as $v) {
          $maps[] = [
            'resource_id'=>$id,
            'operator_id'=>$v,
            'privileges_desc'=>'auto_add',
            'operator_code'=>strtolower(ltrim($res['res_code'],'RES_')).'_'.$opts[$v]['operator_code'],
          ];
        }
        $this->addAll($maps);
      }
      Db::commit();
    }
  }
}