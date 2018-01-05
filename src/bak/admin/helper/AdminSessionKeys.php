<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-10
 * Time: 16:42
 */

namespace app\src\admin\helper;

use app\src\base\helper\SessionKeys;

/**
 * 定义session的键
 * Class SessionKeys
 * @package app\src\config
 */
class AdminSessionKeys extends SessionKeys {

    //admin模块

    const ADMIN_USER = "admin_user";

    const ADMIN_USER_SIGN = "admin_user_sign";
}


