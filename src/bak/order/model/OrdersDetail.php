<?php
/**
 * Created by PhpStorm.
 * User: itboye
 * Date: 2016/12/23
 * Time: 10:51
 */
namespace app\src\order\model;


use think\Model;

class OrdersDetail extends Model {
    public function profile()
    {
        return $this->hasOne('Profile');
    }
}