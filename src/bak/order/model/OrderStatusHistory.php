<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-09
 * Time: 20:52
 */

namespace app\src\order\model;


use think\Model;

class OrderStatusHistory extends Model
{
    protected $auto = ['create_time'];

    public function setCreateTimeAttr($name, $value, $data = [])
    {
        return time();
    }
}