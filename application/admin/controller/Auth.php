<?php

namespace app\admin\controller;

class Auth extends CheckLogin{

  public function set(){
    $this->jsf = array_merge($this->jsf,[
      'name'  => '系统名',
      'title' => '节点名',
    ]);
    if(IS_GET){ // view
      $this->jsf_tpl = [
        ['title'],
        ['name'],
        ['desc|textarea','input-long']
      ];
    }else{ // save
      $this->jsf_field = ['name,title','desc'];
    }
    return parent::set();
  }
}
