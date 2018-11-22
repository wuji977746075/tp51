<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-01-16 13:44:56
 * Description : [Description]
 */

namespace app\src\auth\logic;
use app\src\base\logic\BaseLogicV2;
use app\src\auth\model\RolePrivilege;
use app\src\auth\model\FunctionPrivilege;
use app\src\auth\logic\FunctionPrivilegeLogicV2;
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
class RolePrivilegeLogicV2 extends BaseLogicV2 {
  // init
  protected function _init(){
    $this->setModel(new RolePrivilege());
  }

  //获取定义的全部权限 和该角色是否拥有
  public function getRoleRightTree($role_id=0){
    $func_right = $this->getAllRight();

    $r = (new RolePrivilege())->where('role_id',$role_id)
    ->field('func_privileges_id as id')
    // ->where('uid',$to_uid)
    -> select();
    $role_right = Obj2Arr($r,'id');
    // 添加是否有权限 按资源分类
    $list = [];
    foreach ($func_right as $v) {
      $v['has_right'] = isset($role_right[$v['id']]) ? 1 : 0;
      $res_id  = $v['res_id'];
      $type_id = $v['type_id'];
      if(!isset($list[$type_id])){
        $list[$type_id] = [
          'id'  =>$type_id,
          'name'=>$v['type_name'],
          'res' =>[],
          'tree'=>[],
        ];
      }
      if(!isset($list[$type_id]['res'][$res_id])){
        $list[$type_id]['res'][$res_id] = [
          'id'    =>$res_id,
          'name'  =>$v['res_name'],
          'parent'=>$v['res_parent_id'],
          'sort'   =>$v['sort'],
          'parents'=>$v['path'],
          'ops'    =>[],
          'tree'   =>[],
        ];
      }
      $list[$type_id]['res'][$res_id]['ops'][] = [
        'id'       =>$v['op_id'],
        'name'     =>$v['op_name'],
        'func_id'  =>$v['id'],
        'has_right'=>$v['has_right'],
      ];
    }
    unset($func_right);unset($role_right);
// halt($list);
// array(3) {
//   [1] => array(3) {
//     ["id"] => int(1)
//     ["name"] => string(5) "house"
//     ["res"] => array(1) {
//       [1] => array(6) {
//         ["id"] => int(1)
//         ["name"] => string(6) "房源"
//         ["parent"] => int(0)
//         ["sort"] => int(0)
//         ["parents"] => string(0) ""
//         ["ops"] => array(7) {
//           [0] => array(4) {
//             ["id"] => int(1)
//             ["name"] => string(6) "添加"
//             ["func_id"] => int(1)
//             ["has_right"] => int(1)
//           }
//         }... ops
//         ["tree"]=> array(0){
//         }...tree
//       }
//     }... res
//   }
// }... type
    foreach ($list as &$v) {
      $v['res'] = $this->getRightTree($v['res']);
    }
    //格式化 tree 最多3级
    // $list = $this->getRightTree($list);
    return $list;
  }

  /**
   * 设置角色权限
   * @return apiReturn
   */
  public function setRoleRight($role_id=0,array $func_ids){
    //get role rights
    if(empty($func_ids)){
      // 去掉删除全部权限
      $this ->delete('role_id',$role_id);
      return returnSuc('权限已请空');
    }else{
      $new_right = array_unique($func_ids);
      //检查权限是否匹配
      $r = (new FunctionPrivilegeLogicV2())->queryNoPaging(['id'=>['in',$func_ids]],false,'id,operator_id,resource_id');
      if(count($new_right) !== count($r)){
        return returnErr('func_uids中存在非法数据');
      }
      $func_rights = changeArrayKey($r,'id');
      //查询角色现有权限
      $r = $this->queryNoPaging(['role_id'=>$role_id],false,'func_privileges_id as func_id');
      $old_right = array_keys(changeArrayKey($r,'func_id'));
      //需要删除的权限
      $del_right = array_diff($old_right,$new_right);
      if($del_right){
        $this ->delete(['role_id'=>$role_id,'func_privileges_id'=>['in',$del_right]]);
      }
      //需要添加的权限
      $add_right = array_diff($new_right,$old_right);
      $map = [];
      foreach ($add_right as $v) {
        $map[] = [
         'role_id'=>$role_id,
         'func_privileges_id'=>$v,
         'operator_id'=>$func_rights[$v]['operator_id'],
         'resource_id'=>$func_rights[$v]['resource_id'],
        ];
      }
      if($map) $this ->addAll($map);
      return returnSuc(L('success'));
    }
  }

  public function getAllRight(){
    $model = new FunctionPrivilege();
    $r = $model->alias('p')
      ->join(['auth_operator op',''],'op.id = p.operator_id','left')
      ->join(['auth_resources r',''],'r.id = p.resource_id','left')
      ->join(['auth_data_object_type t',''],'t.id = r.data_object_type_id','left')
      ->field('p.id,op.id as op_id,op.operator_name as op_name,r.id as res_id,r.res_title as res_name,r.res_parent_id,t.type_name as type_name,r.data_object_type_id as type_id,r.sort,r.path')
      // ->where()
      ->select();
    return Obj2Arr($r);
  }



  //权限重组 为资源树 最多三级 抛弃其他
  protected function getRightTree($r,$top_parent=0){
    $l = [];
    foreach ($r as $k=>$v) {
      $v['tree'] = [];
      //挂载$top_parent下一级菜单
      $parent = (int) $v['parent'];
      if( $parent === $top_parent){
        $l[$v['id']] = $v;
        unset($r[$k]);
      }
    }
    foreach ($r as $k=>$v) {
      //挂载$top_parent下二级菜单
      $parent = (int) $v['parent'];
      if($parent && isset($l[$parent])){
        $l[$parent]['tree'][$v['id']] = $v;
        unset($r[$k]);
      }
    }
    foreach ($r as $k=>$v) {
      //挂载$top_parent下三级菜单
      $path = $v['parents'];
      if($path){
        $path = explode(',', $path);
        isset($l[$path[0]]['tree'][$path[1]]) && $l[$path[0]]['tree'][$path[1]]['tree'][] = $v;
      }
    }
    // $sort = SORT_DESC;
    // 1级排序
    // $this->my_sort($l,'sort',$sort);
    // foreach ($l as &$v) {
    //  //2级排序
    //  if(!empty($v['tree'])){
    //    $v['tree'] = $this->my_sort($v['tree'],'sort',$sort);
    //    foreach ($v['tree'] as &$vv) {
    //      //3级排序
    //      if(!empty($vv['tree'])) $vv['tree'] = $this->my_sort($vv['tree'],'sort',$sort);
    //    }
    //  }
    // }
    return $l;
  }
  //2维数组排序
  protected function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){
    if(is_array($arrays) && !empty($arrays)){
      foreach ($arrays as $array){
        if(is_array($array) && !empty($arrays)){
          $key_arrays[] = $array[$sort_key];
        }else{
          return [];
        }
      }
    }else{
      return [];
    }
    array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
    return $arrays;
  }
}