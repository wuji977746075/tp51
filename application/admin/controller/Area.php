<?php
namespace app\admin\controller;

class Area extends CheckLogin{

  protected $banEditFields = ['parent','id'];
  public function init(){
    $this->logic = new \src\com\AreaLogic;
  }

  // edit / add
  public function set(){

  }

  public function index(){
    $parent = $this->_param('parent/d',1);
    $up     = $this->_param('up/d',0); //  ? parent的上级
    if($parent && $up){
      $r = $this->logic->getInfo(['id'=>$parent]);
      if($r) $parent = $r['parent'];
    }
    $name   = '省市区';
    if($parent){
      $r = $this->logic->getInfo(['id'=>$parent]);
      $name = $r['city'] ? $r['city'] : $r['province'];
    }
    $this->assign('parent',$parent);
    $this->assign('name',$name);
    return $this->show();
  }


  // ajax 返回菜单单页数据
  public function ajax(){
    $parent = $this->_get('parent/d',1);
    $this->sort  = 'sort desc';
    $this->where = ['parent'=>$parent];
    return parent::ajax();
  }

}