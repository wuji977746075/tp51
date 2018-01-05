<?php
/**
 * Created by PhpStorm.
 * User: Zhoujinda
 * Date: 2017/1/6
 * Time: 17:28
 */

namespace app\weixin\helper;


use app\src\admin\helper\ByApiHelper;

class WxApiHelper {

    public static function callRemote($type, $data) {

        $t = [
            'api_ver'   => '100',
            'notify_id' => self::getNotifyId(),
            'type'      => $type
        ];

        $data = array_merge($t, $data);

        return ByApiHelper::getInstance()->callRemote($data);

    }

    protected static function getNotifyId() {
        return time();
    }

}