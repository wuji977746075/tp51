<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-31
 * Time: 15:52
 */

namespace app\src\base\facade;

interface IAccount
{
    /**
     * 注销用户信息
     * @param $uid
     * @param $auto_login_code
     * @return mixed
     */
    function logout($uid,$auto_login_code);

    /**
     * @param $client_id
     * @param $mobile       string 手机号
     * @param $code         string 验证码
     * @param $country      string 国家代码
     * @param $device_token string 设备唯一码
     * @param $device_type  string 设备类型
     * @param $role         string 用户角色
     * @return mixed
     */
    function loginByCode($client_id,$mobile,$code,$country,$device_token,$device_type,$role);

    /**
     * @param $username
     * @param $password
     * @param $type
     * @param $country      string 国家代码
     * @param $device_token string 设备唯一码
     * @param $device_type  string 设备类型
     * @param $role         string 用户角色
     * @param $model        string 设备型号
     * @return mixed
     */
    function login($username, $password,$type,$country,$device_token,$device_type,$role,$model='unknown');

    function register($entity);

    function update($uid,$entity);

    function updatePwd($map,$newPwd);

    function delete($entity);

    function autoLogin($uid,$log_session_id,$device_type,$session_expire_time);

}