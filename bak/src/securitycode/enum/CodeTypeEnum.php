<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-14
 * Time: 10:23
 */

namespace app\src\securitycode\enum;

/**
 * Class CodeTypeEnum
 * 验证码处理种类
 * @package app\src\securitycode\enum
 */
class CodeTypeEnum
{
    /**
     *  只是返回
     */
    const ALERT = "alert";

    /**
     * 短信息
     */
    const Sms = "sms";
}