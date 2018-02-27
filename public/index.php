<?php

// [ 应用入口文件 ]

// 加载启动配置
require __DIR__ . '/boot.php';

// 执行应用并响应
\think\Container::get('app', [defined('APP_PATH') ? APP_PATH : ''])
    ->bind('index')
    ->run()
    ->send();