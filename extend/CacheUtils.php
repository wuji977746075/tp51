<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 16/4/17
 * Time: 19:02
 */

use src\config\ConfigLogic;

/**
 *
 * 获取数据库配置信息要调用这个类
 * Class CacheUtils
 * @package app\utils
 */
class CacheUtils {

    /**
     * 初始化app配置信息
     * @param int $cacheTime
     */
    static public function initAppConfig($cacheTime = 86400){
        $config = cache('global_config');

        if ($config === false) {
            $map = [];
            $fields = 'type,name,value';
            $api = new ConfigLogic();
            $r = $api->query($map, false, $fields);

            $config = [];
            if (is_array($r)) {
                foreach ($r as $value) {
                    $config[$value['name']] = self::_parse($value['type'], $value['value']);
                }
            }
            //缓存配置$cacheTime秒
            cache("global_config", $config, $cacheTime);
        }

    }

    /**
     *
     * @param $name
     * @return false 或 object
     */
    static public  function getConfig($name){
        self::initAppConfig();
        $config = cache('global_config');
        if(isset($config[$name])){
            return $config[$name];
        }
        return false;
    }


    /**
     * 根据配置类型解析配置
     * @param  integer $type 配置类型
     * @param  string $value 配置值
     * @return array|string
     */
    private static function _parse($type, $value) {
        switch ($type) {
            case 3 :
                $value = str_replace('&amp;', '&', $value); //fix & error
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val,2);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }
        return $value;
    }

}