<?php
namespace think;

// [ 应用启动配置示例 ]

ini_set('memory_limit','16M');
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
define('CONFIG_PATH', __DIR__ . '/../config/');
define('POWER','rainbow');

// 开始运行时间和内存使用
define('START_TIME', microtime(true));
define('START_MEM', memory_get_usage());
//环境变量
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
define('IS_CGI',(0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? 1 : 0 );
define('IS_CLI_', PHP_SAPI == 'cli' ? true : false);
define('NOW_TIME', $_SERVER['REQUEST_TIME']);
define('REQUEST_METHOD', IS_CLI_ ? 'GET' : $_SERVER['REQUEST_METHOD']);
define('IS_AJAX', (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false);
define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);


// 当前文件名
if(!defined('_PHP_FILE_')) {
  $_temp  = explode('.php',$_SERVER['PHP_SELF']);
  define('_PHP_FILE_',    rtrim(str_replace($_SERVER['HTTP_HOST'],'',$_temp[0].'.php'),'/'));

  //index模块 无法自动绑定 调整为主动绑定
  define('BIND_MODULE',ltrim($_temp[0],'/'));

}

if(!defined('__ROOT__')) {
    $_root  =   rtrim(dirname(_PHP_FILE_),'/');
    define('__ROOT__',  (($_root=='/' || $_root=='\\')?'':$_root));
}

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

try{
// 支持事先使用静态方法设置Request对象和Config对象
// 执行应用并响应
Container::get('app', [defined('APP_PATH') ? APP_PATH : ''])
  ->bind(BIND_MODULE)
  ->run() // frame bug : index模块 无法自动绑定
  ->send();

}catch(\Exception $e){
  echo $e;
}