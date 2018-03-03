#!/usr/bin/env php
<?php
namespace think;

ini_set('display_errors', 'on');
// if(strpos(strtolower(PHP_OS), 'win') === 0)
// {
//     exit("start.php not support windows.\n");
// }
// 检查扩展
// if(!extension_loaded('pcntl'))
// {
//     exit("Please install pcntl extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
// }
// if(!extension_loaded('posix'))
// {
//     exit("Please install posix extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
// }
// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
define('CONFIG_PATH', __DIR__ . '/config/');
define('POWER','rainbow');
define('__ROOT__',  '');

// 加载基础文件
require __DIR__ . '/thinkphp/base.php';

// 执行应用并响应
Container::get('app',[APP_PATH])->bind('push/Run')->run()->send();

// gatewayClient
// gateClient是用来辅助 workerman或者是gateway进行用户分组以及向用户发送信息的组件，同时，能够快速便捷的将原有系统的uid和clientid绑定起来。
// github地址：https://github.com/walkor/GatewayClient

// ws = new WebSocket("ws://127.0.0.1:8282");
// ws.onopen = function() {
//     console.log("连接成功");
// };
// ws.onmessage = function(e) {
//     console.log("收到服务端的消息：" + e.data);
// };

// a = {};a.from=1,a.to=2,a.msg='test...';
// as =  JSON.stringify(a);
// ws.send(as);