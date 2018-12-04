<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-26 17:54:14
 * Description : [Description]
 */

namespace app\admin\controller;

class Bbs extends CheckLogin {
  protected $model_id = 22;
  protected function init() {
    $this->cfg['theme'] = 'layer';
  }

  function index() {
    $parent = $this->_param('parent/d',0);
    $up     = $this->_param('up/d',0); //  ? parent的上级
    if($parent && $up){
      $r = $this->logic->getInfo(['id'=>$parent]);
      if($r) $parent = $r['parent'];
    }
    $this->assign('parent',$parent);
    return $this->show();
  }

  function ajax() {
    $parent = $this->_param('parent/d',0);
    $this->where = ['parent'=>$parent];
    return parent::ajax();
  }

  function set() {
    $id = $this->id;
    $this->jsf = array_merge($this->jsf,[
      'name'   =>'标题',
      'parent' =>'父级',
      'desc'   =>'描述',
      'auth'   =>'审核',
    ]);
    $super  = $this->_param('super/d',0); // 是否为高级模式
    $parent = $this->_param('parent/d',0);
    if(IS_GET){ // view
      $this->assign('super',$super);
      if($id){ // edit
        $info = $this->logic->getInfo(['id'=>$id]);
        empty($info) && $this->error(Linvalid('id'));
        $parent = $info['parent'];
      }else{ // add
        if($parent){
          $info = $this->logic->getInfo(['id'=>$parent]);
          empty($info) && $this->error(Linvalid('parent'));
        }
      }
      $this->assign('parent',$parent);
      // 查询按tree板块
      $bbs = $this->logic->queryTree(false);
      array_unshift($bbs, ['id'=>0,'name'=>'* 顶级 *','child'=>[]]);
      $this->assign('bbs',$bbs);
      $this->jsf_tpl = [
        ['*name'],
        ['*parent|selects|'.$parent,'',$bbs],
        ['icon|btimg','',1],
        ['status|radio','','',3],
        ['auth|radio','','',3],
        ['desc|textarea','input-long'],
        ['sort|number'],
      ];
      return parent::set();
    }else{ //save
      $paras = $this->_getPara('name','icon,status|0|int,desc,auth|0|int,sort|0|int,parent|0|int');
      if($id){ // edit

        $info = $this->logic->getInfo(['id'=>$id]);
        empty($info) && $this->err(Linvalid('id'));
        // ? parent以改变
        if($parent != intval($info['parent'])){
          // 排除子类
          $info = $this->logic->getInfo(['parent'=>$id]);
          $info && $this->err(L('need-del-down'));
          // 排除自身
          $parent == $id && $this->err(Linvalid('op'));
          $r = $this->logic->save(['id'=>$id],$paras);
        }
      }else{ // add
        if($parent){
          $r = $this->logic->getInfo(['id'=>$parent]);
          empty($r) && $this->err(Linvalid('parent'));
        }
        $id = $this->logic->add($paras);
      }
      if($super){
        $this->suc_url= url(CONTROLLER_NAME.'/drag');
      }else{
        $this->suc_url= url(CONTROLLER_NAME.'/index',['parent'=>$parent]);
      }
      $this->opSuc();
    }
  }
}