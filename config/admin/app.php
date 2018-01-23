<?php

define("CLIENT_ID","by580d6fd2da37e1");
define("CLIENT_SECRET","4256e5b38f10b5794e099c23321cd24f");

return [
    'is_debug' => true,
    //测试用
    //    'USER_ADMINISTRATOR'=>1,
    'session'             => [
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => 'itboye_sid',
        // SESSION 前缀
        'prefix'         => 'itboye_admin',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true
    ],

    'baidu_map_key'=> 'bV4eyokvXF2Z36PeTbww7fHQ',
    'default_module'         => 'admin',
    // 默认控制器名
    'default_controller'     => 'Manager',
    // 默认操作名
    'default_action'         => 'index',
    // 默认输出类型
    'default_return_type' => 'html',
    'view_replace_str'    => [
        '__PUBLIC__' => __ROOT__ . '/',
        '__JS__'     => __ROOT__ . '/static/default/' . request()->module() . '/js/',
        '__CSS__'    => __ROOT__ . '/static/default/' . request()->module() . '/css/',
        '__IMG__'    => __ROOT__ . '/static/default/' . request()->module() . '/img/',
        '__CDN__'    => ITBOYE_CDN,
        '__SKIN__'   => 'http://115.29.220.243:902/learn-layui/',
    ],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => APP_PATH . '/web/view/dispatch/dispatch_jump.tpl',
    'dispatch_error_tmpl'    => APP_PATH . '/web/view/dispatch/dispatch_jump.tpl',
];