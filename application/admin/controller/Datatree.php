<?php

namespace  app\admin\controller;

class Datatree extends CheckLogin{

  public function index(){

    $parent = $this->_param('parent/d',0);
    $up     = $this->_param('up/d',0); //  ? parent的上级
    if($parent && $up){
      $r = $this->logic->getInfo(['id'=>$parent]);
      if($r) $parent = $r['parent'];
    }
    $this->assign('parent',$parent);
    return $this->show();
  }

  // ajax 返回菜单单页数据
  public function ajax(){
    $parent = $this->_get('parent/d',0);
    $this->where = ['parent'=>$parent];
    return parent::ajax();
  }


  public function set(){
    $id = $this->id;
    $this->jsf = array_merge($this->jsf,[
      'name'   =>'系统名',
      'title'  =>'字典名',
      'icon'   =>'图标',
      'is_sys' =>'系统',
    ]);
    if(IS_GET){ // view
      if($id){ // edit
        $info = $this->logic->getInfo(['id'=>$id]);
        empty($info) && $this->error(Linvalid('id'));
        $parent = $info['parent'];
      }else{ // add
        $parent = $this->_param('parent/d',0);
        if($parent){
          $info = $this->logic->getInfo(['id'=>$parent]);
          empty($info) && $this->error(Linvalid('parent'));
        }
      }
      $this->assign('parent',$parent);
      return parent::set();
    }else{ //save
      $paras = $this->_getPara('name,title','icon,is_sys|0|int,desc,sort|0|int,parent|0|int');
      $parent = $paras['parent'];

      if($id){ // edit
        $this->logic->save(['id'=>$id],$paras);
      }else{ // add
        if($parent){
          $r = $this->logic->getInfo(['id'=>$parent]);
          empty($r) && $this->err(Linvalid('parent'));
        }
        $id = $this->logic->add($paras);
      }
      $this->suc('',url(CONTROLLER_NAME.'/index',['parent'=>$parent]));
    }

  }
}