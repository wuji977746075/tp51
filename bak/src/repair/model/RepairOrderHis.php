<?php
/**
 * 维修订单 状态变更历史
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 17:10
 */

namespace app\src\repair\model;


use think\Model;

class RepairOrderHis extends Model
{
    protected $table  = "itboye_repair_order_his";
    protected $insert = ['create_time'];

    protected function setCreateTimeAttr(){
      return time();
    }
}