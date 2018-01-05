<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-14
 * Time: 14:05
 */

namespace app\src\securitycode\enum;


class CodeCreateWayEnum
{
    /*
     * 纯数字验证码
     */
    const ONLY_NUMBER = "only_number";

    /*
     * 数字+验证码
     */
    const ALPHA_AND_NUMBER = "alpha_and_number";

}