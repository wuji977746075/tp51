<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-10
 * Time: 21:31
 */

namespace app\src\order\enum;


class PayType
{
    /**
     * 支付宝
     * @author hebidu <email:346551990@qq.com>
     */
    const ALIPAY = 1;

    /**
     * paypal
     * @author hebidu <email:346551990@qq.com>
     */
    const PAYPAL = 2;

    /**
     * 微信
     * @author rainbow
     */
    const WXPAY = 3;

    /**
     * 余额
     * @author rainbow
     */
    const WALLET = 4;

    /**
     * 银联
     */
    const UPACP = 5;

    /**
     * 微信公众号
     */
    const WXPAY_MP = 6;

    /**
     * 微信-司机端
     */
    const WXPAY_DRIVER = 7;

    /**
     * 微信-技工端
     */
    const WXPAY_WORKER = 8;

    /**
     * 积分支付
     */
    const SCORE = 9;
}