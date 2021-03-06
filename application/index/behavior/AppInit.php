<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-12-19 10:19:02
 * Description : [Description]
 */

namespace app\index\behavior;
use Config,Env;

/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\
 * @example
 */
class AppInit {

  public function run(){

    // 加载用户配置 - 并覆盖项目配置
    $file = CONFIG_PATH .config('app_status').Env::get('config_ext','.php');
    if(is_file($file)){
      $cc = include $file;
      foreach ($cc as $k=>$v) {
        if(is_array($v)){
          Config::set($v,$k);
          unset($cc[$k]);
        }
      }
      $cc && Config::set($cc,'app');
    }
  }
}