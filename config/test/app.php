<?php

define("CLIENT_ID","by58018f50cfcae1");
define("CLIENT_SECRET","cb0bfaf5b9b2f53a216bf518e18fef18");

return [
    'api_url' => 'http://tp51/index.php',
    'site_url' => 'http://tp51/',
    // 默认输出类型
    'default_return_type' => 'html',
    'view_replace_str'    => [
        '__PUBLIC__' => __ROOT__ . '/static/default/' . request()->module() . '/',
        '__JS__'     => __ROOT__ . '/static/default/' . request()->module() . '/js/',
        '__CSS__'    => __ROOT__ . '/static/default/' . request()->module() . '/css/',
        '__IMG__'    => __ROOT__ . '/static/default/' . request()->module() . '/img/',
        '__CDN__'    => ITBOYE_CDN,
    ],
    //测试用
];