<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-10
 * Time: 17:17
 */

namespace app\src\base\helper;


use app\src\base\utils\CacheUtils;
use think\Config;

class ConfigHelper
{


    /**
     * 获取头像地址
     * @author hebidu <email:346551990@qq.com>
     * @param $id
     * @param int $size
     * @return string
     */
    public static function getPictureUrl($id,$size=0){
        if(empty($size)){
            return config('picture_url').$id;
        }
        return config('picture_url').$id.'&size='.$size;
    }
    /**
     * 获取头像地址
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @param int $size
     * @return string
     */
    public static function getAvatarUrl($uid,$size=180){
        return config('avatar_url').'?uid='.$uid.'&size='.$size;
    }

    public static function __callStatic($name,$arguments){
        return config($name);
    }

    /**
     * 获取语言列表
     * @author hebidu <email:346551990@qq.com>
     */
    public static function getLangSupport(){
        return config("lang_support");
    }

    /**
     * 获取图片上传缩略图路径
     * @author hebidu <email:346551990@qq.com>
     * @return mixed
     */
    public static function getFileThumbnailPath(){
        return config("app.file_cfg.thumbnail_path");
    }

    /**
     * 获取图片支持裁剪大小
     * @return mixed
     */
    public static function getFilePictureCropSize(){
        return config("app.file_cfg.picture_crop_size");
    }

    public static function isDebug(){
        return config('app_debug');
    }

    public static function getPasswordSalt(){
        return config("app.security_salt.password");
    }

    public static function getSecuritySalt(){
        return config("security_salt");
    }

    /**
     * 获取配置信息，不存在则返回false
     * @param $key
     * @return false
     */
    public static function getValue($key){
        return CacheUtils::getConfig($key);
    }

    /**
     * 配置初始化
     */
    public static function init($time=600){
        $config = CacheUtils::initAppConfig($time);
        foreach ($config as $key=>$value){
            Config::set($key,$value);
        }
    }
}