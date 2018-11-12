<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-10-10 17:29:44
 * Description : [Description]
 */

namespace app\admin\controller;
// use

class FyAccountUser extends CheckLogin {
  // protected $banEditFields = ['id'];
  function ajax(){
    if($this->$uid){
      $this->where = ['invite_uid'] = $uid;
    }
    parent::ajax();
  }
}