<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 15:05
 */

namespace app\src\store\logic;


use app\src\base\logic\BaseLogic;
use app\src\store\model\Store;

class StoreLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new Store());
    }
}