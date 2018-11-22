<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-14
 * Time: 11:22
 */

namespace app\src\productquantityhis\logic;


use app\src\base\logic\BaseLogic;
use app\src\productquantityhis\model\ProductQuantityHis;

class ProductQuantityHisLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new ProductQuantityHis());
    }
}