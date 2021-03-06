<?php

namespace app\admin\controller;

class SysAuth extends CheckLogin{
  protected $model_id = 9;
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
      // $this->suc_url   = url(CONTROLLER_NAME.'/index');
    }
    return parent::set();
  }
}
