<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-14
 * Time: 14:29
 */

namespace app\src\admin\api\po;


class SecurityCodePo
{



    private $acceptor;
    private $codeType;
    private $codeCreateWay;
    private $codeLength;
    private $code;

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }



    /**
     * @return mixed
     */
    public function getAcceptor()
    {
        return $this->acceptor;
    }

    /**
     * @param mixed $acceptor
     */
    public function setAcceptor($acceptor)
    {
        $this->acceptor = $acceptor;
    }

    /**
     * @return mixed
     */
    public function getCodeType()
    {
        return $this->codeType;
    }

    /**
     * @param mixed $codeType
     */
    public function setCodeType($codeType)
    {
        $this->codeType = $codeType;
    }

    /**
     * @return mixed
     */
    public function getCodeCreateWay()
    {
        return $this->codeCreateWay;
    }

    /**
     * @param mixed $codeCreateWay
     */
    public function setCodeCreateWay($codeCreateWay)
    {
        $this->codeCreateWay = $codeCreateWay;
    }

    /**
     * @return mixed
     */
    public function getCodeLength()
    {
        return $this->codeLength;
    }

    /**
     * @param mixed $codeLength
     */
    public function setCodeLength($codeLength)
    {
        $this->codeLength = $codeLength;
    }


}