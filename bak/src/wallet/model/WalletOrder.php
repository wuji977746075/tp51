<?php
/**
 * 余额充值订单 model
 * User: 1
 * Date: 2016-12-29 10:37:35
 */

namespace app\src\wallet\model;


use think\Model;

class WalletOrder extends Model
{
  protected $table = 'itboye_wallet_order';
  protected $auto   = ['update_time'];
  protected $insert = ['create_time'];

  protected function setUpdateTimeAttr(){
    return time();
  }

  protected function setCreateTimeAttr(){
    return time();
  }
}