<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-10-12 16:13:37
 * Description : [Description]
 */

namespace app\admin\controller;
// use

class FyInvite extends CheckLogin {
  protected $model_id = 19;

  function ajax() {
    $uid = $this->uid;
    // 非自己则检查权限
    $uid != UID && $this->checkUserRight(UID,'index');

    $paras = $this->_getPara('','kword,start,end');
    extract($paras);
    $map = [];
    $uid && $map[] = ['invite_uid','=',$uid];
    ($start || $end) && $map[] = getWhereTime('create_time',$start,$end);
    $this->where = $map;
    // dump($map);die();
    parent::ajax();
  }
}