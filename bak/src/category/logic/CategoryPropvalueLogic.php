<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 15:54
 */

namespace app\src\category\logic;


use app\src\base\logic\BaseLogic;
use app\src\category\model\CategoryPropvalue;

class CategoryPropvalueLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new CategoryPropvalue());
    }
}