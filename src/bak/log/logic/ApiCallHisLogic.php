<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 11:43
 */

namespace app\src\log\logic;


use app\src\base\logic\BaseLogic;
use app\src\log\model\ApiCallHis;

class ApiCallHisLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new ApiCallHis());
    }
}