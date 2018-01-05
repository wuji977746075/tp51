<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-14
 * Time: 10:43
 */

namespace app\src\securitycode\action;


use app\src\base\action\BaseAction;
use app\src\securitycode\logic\SecurityCodeLogic;

class SecurityCodeVerifyAction extends BaseAction
{
    /**
     * 验证
     * @param $acceptor
     * @param $type
     * @param $code
     * @param $client_id
     * @param bool $is_clear_code
     * @return array
     */
    public function verify($acceptor,$type,$code,$client_id,$is_clear_code = true){
        $result = (new SecurityCodeLogic())->isLegalCode($code,$acceptor,$type,$client_id,$is_clear_code);
        return $result;
    }
}