<?php
namespace app\admin\controller;

class Test extends CheckLogin{

  public function map(){

    $this->assign('desc', 'desc');
    $this->assign('name', 'name');
    $this->assign('logo', 1);
    $this->assign('lat', 30);
    $this->assign('lng', 120);
    return $this->show();
  }

  // todo : add modal map
  public function add(){
    return $this->show();
  }
}