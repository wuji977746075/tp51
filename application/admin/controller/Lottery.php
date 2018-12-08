<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-12-07 15:32:48
 * Description : [Description]
 */

namespace app\admin\controller;
// use

class Lottery extends CheckLogin {
  protected $model_id = 29;
  public function init(){
    $this->cfg['theme'] = 'layer';
  }
  function index() {
    return parent::index();
  }

  function set(){
    $this->jsf = array_merge($this->jsf,[
      'name'       => '活动名',
      'icon'       => '图标',
      'start_time' => '开始时间',
      'end_time'   => '截止时间',
      'views'      => '浏览量',
      'playtimes'  => '游戏次数',
    ]);
    if(IS_GET){ // view
      $this->jsf_tpl = [
        ['*name'],
        ['icon|btimg','',1],
        ['desc|textarea','input-ueditor'],
        ['start_time|time','','Y-m-d H:i:s'],
        ['end_time|time','','Y-m-d H:i:s'],
        ['views|number*|0'],
        ['playtimes|number*|0'],
      ];
      return parent::set();
    }else{ // save
      $paras = $this->_getPara('name','icon|0|int,desc,start_time,end_time,views|0|int,playtimes|0|int');
      $id = $this->id;
      // todo : name 检查
      // $paras['title'] = $this->logic->checkTitle($paras['title'],$id);
      // $paras['content'] = $this->logic->filter($paras['content']);
      // $paras['publish_time'] = strtotime($paras['publish_time']);
      $this->trans();
      $add = $paras;
      if($id){  // edit
        $this->logic->save(['id'=>$id],$add);
      }else{    // add
        $add['uid'] = UID;
        $id = $this->logic->add($add);
      }
      $this->opSuc();
    }
  }
}