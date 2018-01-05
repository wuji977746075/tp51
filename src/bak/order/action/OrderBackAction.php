<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-17
 * Time: 10:31
 */

namespace app\src\order\action;


use app\src\base\action\BaseAction;
use app\src\order\logic\OrdersLogic;
use app\src\order\logic\OrderStatusHistoryLogic;
use app\src\order\model\Orders;
use think\Db;

/**
 * Class OrderCancelAction
 * 订单退回操作
 * @package app\src\order\action
 */
class OrderBackAction extends BaseAction
{
    /**
     * 确认收货
     * @param Orders $orders
     * @param int $uid
     * @param int $isAuto
     * @return array
     */
    public function back(Orders $orders,$uid=0,$isAuto=0){
        Db::startTrans();
        $error = false;
        $result = (new OrdersLogic())->backOrder($orders);
        if($result['status']){
            $result = (new OrderStatusHistoryLogic())->backOrder($orders,$uid,$isAuto);
        }

        if(!$result['status']){
            $error = $result['info'];
        }

        if($error === false){
            Db::commit();
            return $this->success($result);
        }else{
            Db::rollback();
            return $this->error($result);
        }
    }
}