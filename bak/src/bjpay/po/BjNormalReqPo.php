<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-11
 * Time: 09:33
 */

namespace app\src\bjpay\po;

/**
 * 一般交易请求基类
 * Class BjNormalReqPo
 * @package app\src\bjpay\po
 */
class BjNormalReqPo extends BjBaseReqPo
{
    private $dseSessionId;//在登录时得到的，每次交易必须上传。
//    private $reqData;//交易请求XML报文

    /**
     * @return mixed
     */
    public function getDseSessionId()
    {
        return $this->dseSessionId;
    }

    /**
     * @param mixed $dseSessionId
     */
    public function setDseSessionId($dseSessionId)
    {
        $this->dseSessionId = $dseSessionId;
    }

}