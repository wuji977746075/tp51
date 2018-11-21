<?php

namespace app\admin\controller;

// use

class Message extends CheckLogin {
  protected $business = '消息';
  protected $banEditFields = ['id'];
  public function init(){
    $this->cfg['theme'] = 'layer';
    $this->logic = new \src\com\MessageLogic;
  }



}