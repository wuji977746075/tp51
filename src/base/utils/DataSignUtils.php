<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-13
 * Time: 14:43
 */

namespace app\src\base\utils;


/**
 * Class DataSignUtils
 * 对数据进行签名
 * @package app\src\base\utils
 */
class DataSignUtils
{
    /**
     * 签名
     * @param $data
     * @return string
     */
    public static function sign($data){
        //数据类型检测
        if (!is_array($data)) {
            $data = (array)$data;
        }
        ksort($data);
        //排序
        $code = http_build_query($data);
        //url编码并生成query字符串
        $sign = sha1($code);
        //生成签名
        return $sign;
    }
}