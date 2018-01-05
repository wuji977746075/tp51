<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2016-12-23 15:05:48
 * Description : [Description]
 */

namespace app\src\wxpay\logic;
use app\src\base\logic\BaseLogicV2;
use app\src\wxpay\model\WxpayNotify;

/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\src\wxpay\logic
 * @example
 */
class WxpayNotifyLogicV2 extends BaseLogicV2{

    //初始化
    protected function _init(){
        $this->setModel(new WxpayNotify());
    }
}