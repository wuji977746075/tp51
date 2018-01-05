<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-10
 * Time: 17:20
 */

namespace app\src\base\helper;


use app\src\session\action\LoginSessionCheckAction;
use app\src\session\action\LoginSessionLoginAction;
use app\src\session\logic\LoginSessionLogic;

class SessionHelper
{


    const EXPIRE_LOGIN_TIME = 1296000;// 3600 * 24 * 15;

    /**
     * 检查登录状态
     * @param $uid   integer 用户id
     * @param $log_session_id string 用户登录后的授权id
     * @return array
     */
    public static function checkLoginSession($uid,$log_session_id,$device_type,$session_expire_time){
        return (new LoginSessionCheckAction())->check($uid,$log_session_id,$device_type,$session_expire_time);
    }

    /**
     * 记录 Session 信息
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @param $device_token
     * @param $device_type
     * @param $login_info
     * @param $session_expire_time
     * @return string
     */
    public static function addLoginSession($uid,$device_token,$device_type,$login_info,$session_expire_time){

        return (new LoginSessionLoginAction())->login($uid,$device_token,$device_type,$login_info,$session_expire_time);
    }

}