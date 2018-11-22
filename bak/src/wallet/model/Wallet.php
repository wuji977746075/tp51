<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-06
 * Time: 10:05
 */

namespace app\src\wallet\model;


use think\Model;

class Wallet extends Model
{
  protected $table = 'itboye_wallet';
  protected $auto   = ['update_time'];
  protected $insert = ['wallet_type' => 0,'create_time'];

  protected function setUpdateTimeAttr(){
    return time();
  }

  protected function setCreateTimeAttr(){
    return time();
  }
}