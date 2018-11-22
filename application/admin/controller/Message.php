<?php
namespace app\admin\controller;

class Message extends CheckLogin {
  protected $model_id = 11;
  public function init(){
    $this->cfg['theme'] = 'layer';
  }
}