<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 11:40
 */

namespace app\src\log\logic;


use app\src\base\logic\BaseLogic;
use app\src\log\model\ApiHisView;

class ApiHisViewLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new ApiHisView());
    }
}