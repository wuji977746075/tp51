<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-22
 * Time: 11:11
 */

namespace app\src\bjpay\utils;


class SerialNoUtils
{
    public static function generate($uid){
        $rand = mt_rand(1000000, 9999999);
        $time_str = date("yzHis",time());
        return "BJHTB".$time_str.$rand.get_36HEX($uid);
    }
}