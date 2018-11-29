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
}