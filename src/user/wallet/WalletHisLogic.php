<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-10-08 11:23:04
 * Description : [分佣系统 - 余额变动logic]
 */

namespace src\user\wallet;
use src\base\BaseLogic;

class WalletHisLogic extends BaseLogic {

  // todo : cache
  function fyCount($uid,$start='',$end=''){
    $map = [];
    ($start || $end ) && $map[] = getWhereTime('create_time',$start,$end);
    return $this->sum(['uid'=>$uid,'dt_type'=>WalletLogic::PLUS_FY],'plus');
  }
}