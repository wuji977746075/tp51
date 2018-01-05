<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-11
 * Time: 09:37
 */

namespace app\src\bjpay\po;


class BjQueryBalanceRespPo extends BjBaseRespPo
{
    public function getAcctName(){
        return $this->getParamResult("acctName");
    }

    /**
     * 币种
     * @return bool|int
     */
    public function getAcctCur(){
        return $this->getParamResult("acctCur");
    }

    public function getBalance(){
        return $this->getParamResult("balance");
    }

    /**
     * 当日余额
     * @return bool|int
     */
    public function getCurBal(){
        return $this->getParamResult("curBal");
    }

    /**
     * 昨日金额
     * @return bool|int
     */
    public function getHisBal(){
        return $this->getParamResult("hisBal");
    }

    public function getAvailableBalance(){
        return $this->getParamResult("AvailableBalance");
    }
}