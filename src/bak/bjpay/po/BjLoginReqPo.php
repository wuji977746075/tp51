<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-22
 * Time: 11:48
 */

namespace app\src\bjpay\po;


class BjLoginReqPo extends BjBaseReqPo
{

    public function __construct()
    {
        parent::__construct();
        $this->setOpName('CebankUserLogonOp');
    }

    private $userId;
    private $userPwd;

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserPwd()
    {
        return $this->userPwd;
    }

    /**
     * @param mixed $userPwd
     */
    public function setUserPwd($userPwd)
    {
        $this->userPwd = $userPwd;
    }

    public function toXML(){
        $this->addParam('userID',$this->getUserId());
        $this->addParam('userPWD',$this->getUserPwd());
        return parent::toXML();
    }

}