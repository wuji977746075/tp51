<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-12-07 15:32:48
 * Description : [Description]
 */

namespace app\admin\controller;
use src\base\BaseModel as Model;

class LotteryGain extends CheckLogin {
  protected $model_id = 31;
  public function init(){
    $this->cfg['theme'] = 'layer';
  }
  function index() {
    return parent::index();
  }

  function ajax() {
    return parent::ajax();
  }
}