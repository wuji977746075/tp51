<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-13
 * Time: 15:08
 */

namespace app\src\admin\helper;


class AdminFunctionHelper
{

    public static function yesorno($param) {
        if (is_null($param) || $param === false || $param == 0 || $param == "0") {
            return L("NO");
        } else {
            return L('YES');
        }
    }


    /**
     * 分析枚举类型配置值 格式 a:名称1,b:名称2
     * @param $string
     * @return array
     */
    public static function parse_config_attr($string) {
        $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
        if (strpos($string, ':')) {
            $value = array();
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k] = $v;
            }
        } else {
            $value = $array;
        }
        return $value;
    }

    /**
     * 获取链接
     * 传入U方法可接受的参数或以http开头的完整链接地址
     * @param $str
     * @param string $param
     * @return string 链接地址
     */
    public static function getURL($str, $param = '') {

        if (strpos($str, '?') === false) {
            $str = $str . '?' . $param;
        } else {
            $str = $str . '&' . $param;
        }
        if (strpos($str, "http") === 0) {
            return $str;
        }

        return url($str);
    }

    public static function isActiveLeftMenu($id){
        $active_menu_id = AdminSessionHelper::getCurrentActiveLeftMenuId();
        if ($active_menu_id === $id) {
            return 'active';
        }
        return '';
    }

    public static function isActiveTopMenu($id){
        $active_menu_id = AdminSessionHelper::getCurrentActiveTopMenuId();
        if ($active_menu_id === $id) {
            return 'active';
        }
        return '';
    }

    /**
     * 判断当前登录用户是否为根管理员
     * @param null $uid
     * @return bool
     */
    public static function isRoot($uid = null){
        $uid = is_null($uid) ? AdminSessionHelper::getUserId() : $uid;
        return $uid && (intval($uid) === 1); //AdminConfigHelper::getValue('USER_ADMINISTRATOR')
    }

    public static function getAvatarUrl($uid){
        return AdminConfigHelper::getValue('avatar_url').'?uid='.$uid.'&size=120';
    }


    /**
     * @param $skin_code
     * @return string
     */
    public static function getBySkin($skin_code){

        switch($skin_code) {
            case 0 :
            $desc = "skin-blue";
            break;
            case 1 :
                $desc = "skin-blue-light";
                break;
            case 2 :
                $desc = "skin-black";
                break;
            case 3 :
                $desc = "skin-black-light";
                break;
            case 4 :
                $desc = "skin-green";
                break;
            case 5 :
                $desc = "skin-green-light";
                break;
            case 6 :
                $desc = "skin-red";
                break;
            case 7 :
                $desc = "skin-red-light";
                break;
            case 8 :
                $desc = "skin-yellow";
                break;
            case 9 :
                $desc = "skin-yellow-light";
                break;
            case 10 :
                $desc = "skin-purple";
                break;
            case 11 :
                $desc = "skin-purple-light";
                break;
            default :
                $desc = "skin-blue";
                break;
        }
        return $desc;
    }

    public static function getSkin($skin) {

        $desc = '';

        switch($skin) {
            case 0 :
                $desc = "simplex";
                break;
            case 1 :
                $desc = "flatly";
                break;
            case 2 :
                $desc = "darkly";
                break;
            case 3 :
                $desc = "cosmo";
                break;
            case 4 :
                $desc = "paper";
                break;
            case 5 :
                $desc = "slate";
                break;
            case 6 :
                $desc = "superhero";
                break;
            case 7 :
                $desc = "united";
                break;
            case 8 :
                $desc = "yeti";
                break;
            case 9 :
                $desc = "spruce";
                break;
            case 10 :
                $desc = "readable";
                break;
            case 11 :
                $desc = "cyborg";
                break;
            case 12 :
                $desc = "cerulean";
                break;
            default :
                $desc = "simplex";
                break;
        }
        return $desc;
    }
}
