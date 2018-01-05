<?php
namespace app\admin\controller;
use src\datatree\DatatreeLogic;

class Banner extends CheckLogin {
  private $types     = [];
  private $url_types = [];
  private $dt_id     = 0;
  public function initialize(){
    parent::initialize();
    $dtlogic = new DatatreeLogic;
    $this->types = $dtlogic->getItems('banner_item');
    $this->url_types = $dtlogic->getItems('url_type_item');
    $this->dt_id = $this->_param('dt_id/d',0);
  }

  public function index(){
    $this->assign('dt_id',$this->dt_id);
    $this->assign('types',$this->types);
    $this->assign('url_types',$this->url_types);
    return parent::index();
  }

  public function ajax(){
    $dt_id = $this->dt_id; // 搜索banner
    $this->where = $dt_id ? ['dt_id'=>$dt_id]:[];
    return parent::ajax();
  }

  public function set(){
    $id = $this->id;
    $this->jsf = array_merge($this->jsf,[
      'dt_id'    => 'Banner',
      'title'    => '子项名',
      'url'      => '图片链接',
      'url_type' => '链接类型'
    ]);
    if(IS_GET){ // view
      $dt_id = $this->dt_id;
      if($id){ // edit
        $info = $this->logic->getInfo(['id'=>$id]);
        empty($info) && $this->error(Linvalid('id'));
        $dt_id = (int) $info['dt_id'];
      }else{ // add
      }
      $this->assign('dt_id',$dt_id);
      $this->assign('has_img',($id && $info['img']) ? 1:0);
      $this->assign('types',$this->types);
      $this->assign('url_types',$this->url_types);
    }else{ // save
      $this->jsf_field = ['title,dt_id|0|int,url,url_type|0|int','img|0|int,desc,sort|0|int'];
    }
    return parent::set();
  }
}
