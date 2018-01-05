<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-11
 * Time: 09:28
 */

namespace app\src\bjpay\po;


class BjLogoutReqPo extends BjBaseReqPo
{
    private $dseSessionId;

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