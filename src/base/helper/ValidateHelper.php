<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-17
 * Time: 11:09
 */

namespace app\src\base\helper;


class ValidateHelper
{
    /**
     * 判断是否为数字
     * @param $str
     * @return bool
     */
    public static function isNumberStr($str){
        return  !is_resource($str) && !is_array($str) && !is_object($str) && (is_int($str) || is_numeric($str) || preg_match('/^\d*$/',$str));
    }

    /**
     * 是否为[几位]纯数字[字符串]
     * @Author
     * @DateTime 2017-03-11T16:14:53+0800
     * @param    string                   $str [description]
     * @param    integer                  $len [description]
     * @return   boolean                       [description]
     */
    public static function isNumber($str='',$len=0){
        if($len) return intval($str) == $str && strlen($str)==$len;
        else return intval($str) == $str;
    }
    /**
     * 是否为邮箱字符串
     * @Author
     * @DateTime 2017-03-11T16:09:19+0800
     * @param    string                   $str [description]
     * @return   boolean                       [description]
     */
    public static function isEmail($str=''){
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        return preg_match($pattern, $str);
    }
    /**
     * 判断是否为合法的密码
     * @author hebidu <email:346551990@qq.com>
     * @param $password
     * @return array
     */
    public static function legalPwd($password){
        if(strlen($password) < 6 || strlen($password) > 64){
            return array('status'=>false,'info'=>lang('tip_password_length'));
        }
        if(!preg_match("/^[0-9a-zA-Z\\\\,.?><;:!@#$%^&*()_+-=\[\]\{\}\|]{6,64}$/",$password)){
            return ['status'=>false,'info'=>lang('tip_password')];
        }
        return ['status'=>true,'info'=>lang('success')];
    }

    /**
     * 判定是否为一个合法的11位字符串
     * @author hebidu <email:346551990@qq.com>
     * @param $str
     * @return bool
     */
    public static function isMobile($str){
        if(is_string($str) && preg_match("/^1\d{10}$/",$str)){
            return true;
        }

        return false;
    }

    /**
     * 验证是否合法的结果,含数组
     * @author hebidu <email:346551990@qq.com>
     * @param $result array
     * @return bool
     */
    public static function legalArrayResult($result){

        if(isset($result['info']) && isset($result['status']) && $result['status'] && is_array($result['info']) && count($result['info']) > 0){
            return true;
        }

        return false;
    }
}