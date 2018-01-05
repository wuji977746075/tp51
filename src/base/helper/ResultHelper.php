<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-14
 * Time: 10:28
 */

namespace app\src\base\helper;


class ResultHelper
{
    public static function success($info){
        return ['status'=>true,'info'=>$info];
    }

    public static function error($info){
        return ['status'=>false,'info'=>$info];
    }
    public static function result($result){
        return ['status'=>false,'info'=>$info];
    }
}