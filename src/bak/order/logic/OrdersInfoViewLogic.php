<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-09
 * Time: 20:54
 */

namespace app\src\order\logic;

use app\src\base\logic\BaseLogic;
use app\src\order\model\OrdersInfoView;
class OrdersInfoViewLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new OrdersInfoView());
    }
}