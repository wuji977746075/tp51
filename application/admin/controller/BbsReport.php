<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-12-07 15:32:48
 * Description : [Description]
 */

namespace app\admin\controller;
// use

class BbsReport extends CheckLogin {
  protected $model_id = 28;
  public function init(){
    $this->cfg['theme'] = 'layer';
  }
  function index() {
    return parent::index();
  }
}