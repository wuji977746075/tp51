<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-13
 * Time: 14:45
 */

namespace app\src\admin\helper;


use app\src\base\helper\SessionHelper;
use app\src\base\utils\DataSignUtils;

class AdminSessionHelper extends SessionHelper
{


    public static function getValue($key){
        return session($key);
    }

    public static function hasValue($key){
        return session('?'.$key);
    }
    public static function setValue($key,$value){
        return session($key,$value);
    }

    /**
     * 获取当前语言
     */
    public static function getCurrentLang(){
        $lang = self::getValue("current_lang");
        if(empty($lang)){
            self::setValue("current_lang","zh-cn");
        }

        return self::getValue("current_lang");
    }

    /**
     * 设置当前语言
     * @param $lang
     * @return mixed
     */
    public static function setCurrentLang($lang){
        return self::setValue("current_lang",$lang);
    }

    /**
     * 获取当前店铺ID
     */
    public static function getCurrentStoreId(){
        return self::getValue("current_store_id");
    }

    /**
     * 设置当前店铺id
     * @param $id
     * @return mixed
     */
    public static function setCurrentStoreId($id){
        return self::setValue("current_store_id",$id);
    }


    public static function getCurrentLeftMenuList(){
        return self::getValue('left_menu_list_'.self::getCurrentActiveTopMenuId());
    }

    public static function setCurrentLeftMenuList($list){
        self::setValue('left_menu_list_'.self::getCurrentActiveTopMenuId(),$list);
    }

    public static function getCurrentActiveLeftMenuId(){
        return self::getValue('left_menu_active_'.self::getCurrentActiveTopMenuId());
    }

    public static function setCurrentActiveLeftMenuId($menu_id){
        self::setValue('left_menu_active_'.self::getCurrentActiveTopMenuId(),$menu_id);
    }

    public static function hasCurrentActiveTopMenuId(){
        return self::hasValue('active_menu_id');
    }

    public static function getCurrentActiveTopMenuId(){
        return self::getValue('active_menu_id');
    }

    public static function setCurrentActiveTopMenuId($menu_id){
         self::setValue('active_menu_id',$menu_id);
    }

    public static function getTopMenu(){
        return self::getValue('top_menu');
    }
    public static function setTopMenu($menu){
        self::setValue('top_menu',$menu);
    }

    /**
     * 设置当前用户的菜单
     * @return mixed
     */
    public static function getCurrentUserMenu(){
        return self::getValue('current_user_menu');
    }

    public static function setCurrentUserMenu($menu){
        self::setValue('current_user_menu',$menu);
    }

    /**
     * 获取后台管理顶部菜单列表
     * @param $uid
     * @return mixed
     */
    public static function getAdminTopMenuList($uid){
        return  self::getValue("CURRENT_USER_".$uid."_TOP_MENU");
    }

    /**
     * 获取后台管理左侧菜单列表
     * @param $uid
     * @return mixed
     */
    public static function getAdminLeftMenuList($uid){
        return self::getValue("CURRENT_USER_".$uid."_LEFT_MENU");
    }

    public static function getSessionId(){
        return  session_id();
    }

    /**
     * 后台用户注销
     */
    public static function logout(){
        session(null);
        // session(null,"think");
    }

    /**
     * 获取登录后的用户会话code
     * @return int
     */
    public static function getAutoLoginCode(){
        $user = self::getUserInfo();
        if($user === false){
            return "";
        }
        return $user['auto_login_code'];
    }


    /**
     * 获取用户id
     * @return int 0 | 大于0
     */
    public static function getUserId(){
        $user = self::getUserInfo();
        if($user === false){
            return 0;
        }

        return $user['id'];
    }

    /**
     * 判断当前是否已经登录
     * 0: 未登录 大于0: 已登录
     * @return int 0 | 大于0
     */
    public static function isLogin(){
        $user = self::getUserInfo();
        if($user === false){
            return 0;
        }

        return $user['id'];
    }

    /**
     * 从session中获取登录用户的信息
     * @return bool|mixed
     */
    public static function getUserInfo(){
        $user = session(AdminSessionKeys::ADMIN_USER);
        if (empty($user)) {
            return false;
        } else {
            return session(AdminSessionKeys::ADMIN_USER_SIGN) == DataSignUtils::sign($user) ? $user : false;
        }
    }

    /**
     * admin模块登录的用户信息存入session
     * @param $userinfo
     */
    public static function setLoginUserInfo($userinfo){
        session(AdminSessionKeys::ADMIN_USER,$userinfo);
        session(AdminSessionKeys::ADMIN_USER_SIGN,DataSignUtils::sign($userinfo));
    }



}