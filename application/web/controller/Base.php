<?php
namespace app\web\controller;
use think\Controller;

class Base extends Controller{

  public function index() {
    // dump(is_object($this));
    // $id = input('id/d',5);// ? : 5;
    // halt($id);
    // $newBase = new get_class($this);
    // dump(get_class($this));
    // dump(get_parent_class($this));
    // dump(get_declared_classes());
    // echo Base::class;
    //
    // error_report(0);
    // print_r(error_get_last());die();
    //
    $map = ['id'=>['numberBetween',[1,99]],'name'=>['firstName',[]]];
    $data = getFaker($map,rand(1,5));
    $this->assign('data',$data);
    return $this -> fetch();
  }
}