<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-29
 * Time: 14:12
 */

namespace app\src\hook;
use app\src\message\action\RepairOrderMsgAction;

/**
 * Class RepairOrderMsgHook
 * 维修订单消息相关钩子
 * @package app\src\hook
 */
class RepairOrderMsgHook
{
    /**
     * 订单已接单
     */
    const OrderTaking = "orderTaking";

    /**
     * 人工分配
     */
    const OrderManualDistribute = "orderManualDistribute";

    /**
     * 价格修改提示（这一步是双方沟通后才会）
     */
    const OrderPriceChange = "orderPriceChange";

    /**
     * 已修好
     */
    const OrderCompleted = "orderCompleted";

    /**
     *
     * @param  string $type   类型
     * @param  array  $params 参数
     * @return bool
     */
    public function hook($type,$params=[]){
        $action = new RepairOrderMsgAction();
        if(method_exists($action,$type)){
            $action->$type($params);
            return true;
        }

        return false;
    }
}