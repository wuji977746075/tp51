<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-13
 * Time: 15:02
 */

namespace app\src\admin\helper;

use app\src\base\helper\ConfigHelper;
use app\src\base\utils\CacheUtils;
use Config;

class AdminConfigHelper extends ConfigHelper
{

    /**
     * 获取博也接口参数配置
     * @return mixed
     */
    public static function getByApiConfig(){
        return config('by_api_config');
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
    public static function init($time=86400){
        $config = CacheUtils::initAppConfig($time);
        foreach ($config as $key=>$value){
            config($key,$value);
        }
    }
}