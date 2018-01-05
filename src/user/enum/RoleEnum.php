<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-02
 * Time: 10:27
 */
namespace  app\src\user\enum;

class RoleEnum{
    //后台登录角色-统一
    const ROLE_Admin  = "role_admin";

    const ROLE_Driver = "role_driver";

    const ROLE_Skilled_worker = "role_skilled_worker";

    public static function getRoleIDBy($role_code){
        switch ($role_code){
            case RoleEnum::ROLE_Driver:
                return 6;
                break;
            case RoleEnum::ROLE_Skilled_worker:
                return 7;
                break;
            default:
                return -1;
                break;
        }
        
    }

}