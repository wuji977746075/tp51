<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/6
 * Time: 19:51
 */

namespace app\src\admin\helper;


use think\Request;

class DateHelper
{

    public static function _param($key,$default=''){

        $value = Request::instance()->param($key,$default);


        return $value;
    }

    /**
     * 获取一个日期时间段
     * 如果有查询参数包含startdatetime，enddatetime，则优先使用否则生成
     * @param $type 0|1|2|3｜其它
     * @return array("0"=>开始日期,"1"=>结束日期)
     */
    public static function getDataRange($type) {
        $result = array();
        switch($type) {
            case 0 :
                //今天之内
                $result['0'] = self::_param('startdatetime', (date('Y/m/d 00:00:00', time())));
                break;
            case 1 :
                //昨天
                $result['0'] = self::_param('startdatetime', (date('Y/m/d 00:00:00', time() - 24 * 3600)));
                $result['1'] = self::_param('enddatetime', (date('Y/m/d 00:00:00', time())));
                break;
            case 2 :
                //最近7天
                $result['0'] = self::_param('startdatetime', (date('Y/m/d H:i:s', time() - 24 * 3600 * 7)));
                break;
            case 3 :
                //最近30天
                $result['0'] = self::_param('startdatetime', (date('Y/m/d H:i:s', time() - 24 * 3600 * 30)));
                break;
            default :
                $result['0'] = self::_param('startdatetime', (date('Y/m/d 00:00:00', time() - 24 * 3600)));
                break;
        }
        if (!isset($result['1'])) {
            $result['1'] = self::_param('enddatetime', (date('Y/m/d H:i:s', time() + 10)));
        }
        return $result;
    }
}