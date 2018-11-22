<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-10-08 10:08:07
 * Description : [分佣系统 - 拉新数据logic]
 */

namespace src\fy\fy;
use src\base\BaseLogic;

class FyInviteLogic extends BaseLogic
{
  /**
   * @return mixed
   */
  protected function _init(){
    $this->setModel(new FyInvite);
  }

  function queryGroup($map,$page=['page'=>1,'size'=>10],$sort,$group='',$field='*') {
    $model = $this->getModel();
    $count = $model->where($map)->order($order)->group($group)->field('id')->count();
    $list = [];
    if($count){
      $start = max($page['page']*$size - $size,0);
      $list = $model->where($map)->order($order)->
      limit($start,$size)->group($group)->field($field)->select();
    }
    return ['count'=>$count,'list'=>$list];
  }
  //新人状态
  function getUserStatus($str=''){
    $ret = 0;
    $str && $str = trim($str);
    if($str == '已激活'){
      $ret = 1;
    }elseif($str == '已首购'){
      $ret = 2;
    }
    return $ret;
  }

  //订单状态
  function getOrderStatus($str=''){
    $ret = 0;
    $str && $str = trim($str);
    if($str == '未完成首购'){
      $ret = 1;
    }elseif($str == '未完成首购'){
      $ret = 2;
    }elseif($str == '非淘客订单'){
      $ret = 3;
    }
    return $ret;
  }

}