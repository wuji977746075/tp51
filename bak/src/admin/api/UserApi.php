<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

namespace  app\src\admin\api;

use app\src\admin\helper\ByApiHelper;
use app\src\user\enum\RegFromEnum;
use app\src\user\enum\RoleEnum;

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-13
 * Time: 15:45
 */
class UserApi extends BaseApi
{
    /**
     * 暂时只支持手机号注册
     * @param $nickname
     * @param string $username 必须是手机号
     * @param string $password 密码
     * @param string $country 国家代码
     * @param string $code
     * @return array
     */
    public static function register($nickname,$username,$password,$country,$code='itboye'){
        $data = [
            'type'=>'By_User_register',
            'api_ver'=>'102',
            'notify_id'=>self::getNotifyId(),
            'nickname'=>$nickname,
            'username'=>$username,
            'password'=>$password,
            'country'=>$country,
            'code'=>$code,
            'reg_from'=>RegFromEnum::ADMIN_USER_ADD
        ];

        return ByApiHelper::getInstance()->callRemote($data);
    }

    /**
     * 登录接口调用
     * @param $username
     * @param $password
     * @param $device_token
     * @param $device_type
     * @param string $country
     * @return array
     */
    public static function login($username,$password,$device_token,$device_type,$country='86'){

        $data = [
            'type'=>'By_User_login',
            'api_ver'=>'104',
            'notify_id'=>self::getNotifyId(),
            'username'=>$username,
            'password'=>$password,
            'country'=>$country,
            'device_token'=>$device_token,
            'device_type'=>$device_type,
            'role'=>RoleEnum::ROLE_Admin
        ];

        return ByApiHelper::getInstance()->callRemote($data);
    }

    /**
     * 用户信息更新
     * 支持如下信息更新: 1. email,sign,nickname,sex,loc_country,loc_area,job_title,company,weixin
     * @param array $entity
     * @return array
     */
    public function update($entity=[]){

        $data = [
            'type'=>'By_User_update',
            'api_ver'=>'100',
            'notify_id'=>self::getNotifyId(),
        ];

        return ByApiHelper::getInstance()->callRemote(array_merge($data,$entity));
    }
}