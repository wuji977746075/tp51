<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2019-01-02 13:41:33
 * Description : [Description]
 */

namespace app\admin\controller;
use src\sys\area\SysAreaLogic as Area;

class House extends HouseBase {

  function index() {
    // echo CODE.'--'.CITY;
    return parent::index();
  }
}