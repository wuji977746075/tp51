<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-19
 * Time: 14:39
 */

namespace app\src\Skuvalue\logic;


use app\src\base\logic\BaseLogic;
use app\src\Skuvalue\model\Skuvalue;


class SkuLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new Skuvalue());
    }

}
