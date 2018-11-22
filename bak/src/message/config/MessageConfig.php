<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-21
 * Time: 11:09
 */

namespace app\src\message\config;


use app\src\base\helper\ConfigHelper;

class MessageConfig
{
    const TYPE_ALERT = "alert";

    const TYPE_LOCAL = "local";

    const TYPE_QCLOUD = "qcloud";

    const TYPE_JUHE   = "juhe";

    /**
     * 获取消息发送方式类型
     * @return mixed|string
     */
    public static function getMsgType(){
        $cfg = ConfigHelper::code_cfg();

        if(is_array($cfg)){
            return $cfg['type'];
        }

        return "";
    }

    /**
     * 取得额外配置信息
     * @return mixed|string
     */
    public static function getExtraCfg(){
        $cfg = ConfigHelper::code_cfg();

        if(is_array($cfg)){
            return $cfg['extra'];
        }

        return "";
    }
}