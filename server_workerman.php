#!/usr/bin/env php
<?php
namespace think;

// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
define('CONFIG_PATH', __DIR__ . '/config/');
define('POWER','rainbow');
define('__ROOT__',  '');

// 加载基础文件
require __DIR__ . '/thinkphp/base.php';

// 执行应用并响应
Container::get('app',[APP_PATH])->bind('push/Worker')->run()->send();

// cmd:
// php server.php
// js :
//ws = new WebSocket("ws://tp51:2346");
// ws.onopen = function() {
//     alert("连接成功");
//     ws.send('tom');
//     alert("给服务端发送一个字符串：tom");
// };
// ws.onmessage = function(e) {
//     alert("收到服务端的消息：" + e.data);
// };