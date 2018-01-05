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
    // fix app_status
    $file = CONFIG_PATH .config('app_status').Env::get('config_ext','.php');
    is_file($file) && Config::set(include $file);
  }
}