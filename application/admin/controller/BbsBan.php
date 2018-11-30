<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-28 17:02:32
 * Description : [Description]
 */

namespace app\admin\controller;
// use

class BbsBan extends CheckLogin{
  protected $model_id = 26;
  protected $banEditFields = ['uid','id'];
  function init(){
    $this->cfg['theme'] = 'layer';
  }


  function index() {
    return parent::index();
  }


  function ajax() {
    $this->checkLogic();
    // $kword = $this->_get('kword',''); // 搜索关键词
    $r = $this->logic->queryCountWithUser($this->where,$this->page,$this->sort);
    $this->checkOp($r);
  }
}