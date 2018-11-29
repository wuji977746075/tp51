<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-10-12 16:17:15
 * Description : [Description]
 */

namespace app\admin\controller;
use src\datatree\DatatreeLogic;
use src\fy\FyAccountLogic;

class WalletHis extends CheckLogin {
  protected function init() {
    $this->cfg['theme'] = 'layer';
  }
  function ajax(){
    $uid = $this->uid;
    // 非自己则检查权限
    $uid != UID && $this->checkUserRight(UID,'index');

    $paras = $this->_getPara('','kword,dt_type,start,end');
    extract($paras);
    $map = [];
    $uid && $map[] = ['uid','=',$uid];
    $kword = trim($kword);
    if($kword){
        // 查询 pid
        $kword = (new FyAccountLogic)->getField(['pid'=>$kword],'id');
    }
    $kword && $map[] = ['extra','=',$kword];
    $dt_type && $map[] = ['dt_type','=',$dt_type];
    ($start || $end) && $map[] = getWhereTime('create_time',$start,$end);
    $this->where = $map;
    // dump($map);die();
    parent::ajax();
  }

  function index(){
    $this->checkUserRight();
    // 账户变动类型
    $dtTypes = (new DatatreeLogic)->getItems('wallet_change_item');
    // dump($dtTypes);die();
    $this->assign('dtTypes',$dtTypes);

    $param = $this->_getPara('','pid,dt_type');
    // $this->assign($param);

    extract($param);
    $this->assign('kword',$pid);
    $this->assign('dt_type',$dt_type);
    return $this->show();
  }
}