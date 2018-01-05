<?php
/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/2/14
 * Time: 上午11:21
 */

namespace app\src\bjpay\po;


class BjSinglePaymentTradeRespPo extends BjBaseRespPo
{
    /**
     * 企业端发过来的交易序列号
     * @return bool|int
     */
    public function getOrderSerialNo()
    {
        return $this->getParamResult('orderSerialNo');
    }

    /**
     * 指令处理状态
     * 2：正在处理
     * 3：处理失败
     * 4：处理成功
     * @return bool|int
     */
    public function getResult()
    {
        return $this->getParamResult('result');
    }
}