<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-12
 * Time: 9:54
 */

namespace app\src\base\facade;


/**
 * 用户信息相关统一调用
 * Class AccountFacade
 * @package app\src\base\interfaces
 */
class AccountFacade Implements IAccount{

    private  $IAccount;

    function __construct(IAccount $account){
        $this->IAccount = $account;
    }

    function logout($uid,$auto_login_code) {
        return $this->IAccount->logout($uid,$auto_login_code);
    }
    /**
     * @param $uid
     * @param $auth_code
     * @return mixed
     */
    function autoLogin($uid, $auth_code,$device_type,$session_expire_time)
    {
        return $this->IAccount->autoLogin($uid,$auth_code,$device_type,$session_expire_time);
    }


    /**
     * 删除
     * @param $entity
     * @return mixed
     */
    function delete($entity)
    {
        return $this->IAccount->delete($entity);
    }

    /**
     * @param $client_id
     * @param string $mobile
     * @param string $code
     * @param string $country
     * @param string $device_token
     * @param string $device_type
     * @param string $role
     * @return mixed
     */
    function loginByCode($client_id,$mobile, $code, $country,$device_token,$device_type,$role)
    {
        return $this->IAccount->loginByCode($client_id,$mobile,$code,$country,$device_token,$device_type,$role);
    }


    /**
     * 登录
     * @param $username
     * @param $password
     * @param $type
     * @param string $country
     * @param string $device_token
     * @param string $device_type
     * @param string $role
     * @return mixed
     */
    function login($username, $password, $type ,$country,$device_token,$device_type,$role,$model='unknown')
    {
        return $this->IAccount->login($username,$password,$type,$country,$device_token,$device_type,$role,$model);
    }

    /**
     * 注册
     * @param $entity
     * @return mixed
     */
    function register($entity)
    {
        return $this->IAccount->register($entity);
    }

    /**
     * 获取用户信息
     * @param $id
     * @return mixed
     */
    function getInfo($id)
    {
        return $this->IAccount->getInfo($id);
    }

    /**
     * 更新
     * @param $uid
     * @param $entity
     * @return mixed
     */
    function update($uid, $entity)
    {
        return $this->IAccount->update($uid,$entity);
    }

    /**
     * 更新密码
     * @param $map
     * @param $newPwd
     * @return mixed
     * @internal param $uid
     * @internal param $oldPwd
     */
    function updatePwd($map, $newPwd)
    {
        return $this->IAccount->updatePwd($map,$newPwd);
    }


}