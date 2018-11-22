<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-29
 * Time: 13:52
 */

namespace app\src\message\action;


use app\src\base\action\BaseAction;
use app\src\message\push\UMPushMessage;

/**
 * Class RepairOrderMsgAction
 * 维修订单消息相关代码
 * @package app\src\message\action
 */
class RepairOrderMsgAction extends BaseAction
{

    /*
     * 1、已接单
     */
    public function orderTaking(){
        $msgTool = new UMPushMessage();
    }

    /**
     * 无人接单被分配
     */
    public function orderManualDistribute(){

    }

    /**
     * 价格修改提示（这一步是双方沟通后才会）
     */
    public function orderPriceChange(){

    }

    /**
     * 已修好
     */
    public function orderCompleted(){

    }

}