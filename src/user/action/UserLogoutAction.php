<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-14
 * Time: 16:30
 */

namespace app\src\user\action;


use app\src\base\action\BaseAction;
use app\src\base\facade\AccountFacade;
use app\src\user\facade\DefaultUserFacade;

class UserLogoutAction extends BaseAction
{

    protected  $facade;

    public function getUserFacade(){
        $this->facade = new AccountFacade(new DefaultUserFacade());
        return $this->facade;
    }

    public function logout($uid,$auto_login_code){
        $result = $this->getUserFacade()->logout($uid,$auto_login_code);
        return $this->result($result);
    }
}