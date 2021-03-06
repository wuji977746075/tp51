<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------

const CDN = 'http://cdn.my/';
const LIB_CSS    = 'http://test.my/learn-layui/';
const CSS_VER = 2018120701;
return [
    // 默认过滤方法 用于普通标签输出
    'default_filter' => '', //htmlspecialchars

    // 标签库标签开始标记
    'taglib_begin' => '{',
    // 标签库标签结束标记
    'taglib_end'   => '}',
    // 模板引擎普通标签开始标记
    'tpl_begin'    => '{',
    // 模板引擎普通标签结束标记
    'tpl_end'      => '}',
    // bug : request()->module()/action() 获取不到
    'tpl_replace_string'  =>  [
        '__PUBLIC__' => __ROOT__ . '/',
        '__JS__'     => __ROOT__ . '/static/default/' . BIND_MODULE . '/js/',
        '__CSS__'    => __ROOT__ . '/static/default/' . BIND_MODULE . '/css/',
        '__IMG__'    => __ROOT__ . '/static/default/' . BIND_MODULE . '/img/',
        '__CDN__'    => CDN,
        '__SELF__' => request()->url(),
        '__SKIN__'   => LIB_CSS,
        '__VERSION__'=> CSS_VER,
    ],
    // 模板引擎类型 支持 php think 支持扩展
    'type'         => 'Think',

    // 模板路径
    'view_path'    => '',
    // 模板后缀
    'view_suffix'  => 'html',
    // 模板文件名分隔符
    'view_depr'    => DIRECTORY_SEPARATOR,
];
