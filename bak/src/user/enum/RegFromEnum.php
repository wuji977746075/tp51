<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-17
 * Time: 9:18
 */

namespace app\src\user\enum;


class RegFromEnum
{
    const UNKNOWN = "999";

    const ADMIN_USER_ADD = "2";//后台管理人员添加

    const SYSTEM = "0";

    public static function getInstance($reg_from){
        if($reg_from == RegFromEnum::SYSTEM){
            return RegFromEnum::SYSTEM;
        }

        if($reg_from == RegFromEnum::ADMIN_USER_ADD){
            return RegFromEnum::ADMIN_USER_ADD;
        }
        return RegFromEnum::UNKNOWN;
    }
}