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

/**
 * Class BjLoginRespPo
 * <opResult>
        <userName>登录用户姓名</userName>
        <corpName>所属企业名称</corpName>
        <lastLogon>上次登录时间</lastLogon>
    </opResult>
 * @package app\src\bjpay\po
 */
class BjLoginRespPo extends BjBaseRespPo
{

    /**
     * 登录用户姓名
     * @return bool|int
     */
    public function getUserName(){
        return $this->getParamResult('userName');
    }

    /**
     * 所属企业名称
     * @return bool|int
     */
    public function getCorpName(){
        return $this->getParamResult('corpName');
    }

    /**
     * 上次登录时间
     * @return bool|int
     */
    public function getLastLogon(){
        return $this->getParamResult('lastLogon');
    }
}