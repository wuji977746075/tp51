<?php
namespace app\admin\controller;

class Picture extends CheckLogin{
  protected $model_id = 20;
  public function test(){

    //Editor + upload + Select Pic
    $this->assign('api_url',config('api_url'));
    return $this->show();
  }

  public function test2(){

    //Editor + upload + Select Pic
    // $this->assign('api_url',config('api_url'));
    return $this->show();
  }
  public function test3(){

    //Editor + upload + Select Pic
    // $this->assign('api_url',config('api_url'));
    return $this->show();
  }

  public function save(){
    dump(input('param.'));
  }
}