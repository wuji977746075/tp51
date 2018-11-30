<?php

namespace app\admin\controller;

class SysConfig extends CheckLogin{
  protected $model_id = 5;
  protected $banDelIds = [7,8];
  public function index(){
    $group = $this->_get('group/d',-1);
    $this->assign('group',$group);
    $kword = $this->_get('kword/d','');
    $this->assign('kword',$kword);

    $this->assign('group_list',config('config_group_list'));
    return parent::index();
  }

  public function ajax(){
    $group = $this->_get('group/d',-1);
    $kword = $this->_get('kword',''); // 搜索关键词

    $map = [];
    if($group >=0) $map[] = ['group','=',$group];
    if($kword) $map[] = ['name|title','like',$kword.'%'];
    $this->where = $map;
    return parent::ajax();
  }

  public function set(){
    $this->jsf = array_merge($this->jsf,[
      'name'  =>'系统名',
      'title' =>'配置名',
      'value' =>'配置值',
      'type'  =>'类型',
      'group' =>'分组',
    ]);
    if(IS_GET){ // view
      $this->jsf_tpl = [
        ['title'],
        ['name'],
        ['type|select','',config('config_type_list')],
        ['group|select','',config('config_group_list')],
        ['value|textarea','input-long'],
        ['desc|textarea','input-long'],
        ['sort|number'],
      ];
    }else{      // save
      $this->jsf_field = ['name,title,value','type|0|int,group|0|int,desc,sort|0|int'];
    }
    return parent::set();
  }

}
