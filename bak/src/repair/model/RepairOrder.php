<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 17:10
 */

namespace app\src\repair\model;


use think\Model;

class RepairOrder extends Model
{
    protected $table  = "itboye_repair_order";
    protected $auto   = ['update_time'];
    protected $insert = ['create_time'];

    protected function setUpdateTimeAttr(){
      return time();
    }

    protected function setCreateTimeAttr(){
      return time();
    }
}