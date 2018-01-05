<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-13
 * Time: 16:11
 */

namespace app\src\admin\api;


use app\src\admin\helper\ByApiHelper;

class BaseApi
{
    protected static function getNotifyId(){
        return time();
    }

    protected function callRemote($data){
        return ByApiHelper::getInstance()->callRemote($data);
    }
}