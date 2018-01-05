<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 17:05
 */

namespace app\src\user\action;


use app\src\base\action\BaseAction;
use app\src\base\facade\AccountFacade;
use app\src\user\facade\DefaultUserFacade;

class LoginAction extends BaseAction
{
    protected  $facade;

    public function getUserFacade(){
        $this->facade = new AccountFacade(new DefaultUserFacade());
        return $this->facade;
    }

    public function autoLogin($uid,$auto_login_code){
        $result = $this->getUserFacade()->autoLogin($uid,$auto_login_code);
        if($result['status']){
            return $result['info'];
        }else{
            return $result;
        }
    }

    public function loginByCode($client_id,$mobile,$code,$country,$device_token,$device_type,$role){
        $result = $this->getUserFacade()->loginByCode($client_id,$mobile,$code,$country,$device_token,$device_type,$role);
        return $this->result($result);
    }
    
    public function login($username,$password,$country,$device_token,$device_type,$role){
        $result = $this->getUserFacade()->login($username,$password,"",$country,$device_token,$device_type,$role);
        return $this->result($result);
    }
}