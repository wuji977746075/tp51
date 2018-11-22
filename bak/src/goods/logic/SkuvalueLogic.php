<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-16
 * Time: 17:16
 */

namespace app\src\goods\logic;


use app\src\base\logic\BaseLogic;
use app\src\goods\model\Skuvalue;

class SkuvalueLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new Skuvalue());
    }
}