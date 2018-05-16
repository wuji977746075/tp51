<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2016-12-06 14:29:42
 * Description : [本地配置-rainbow-覆盖主配置]
 * 本地测试的话
 */

return [
  //本地不同步百川信息
  'alibaichuan_sync' => false,

  'site_url'=>'http://tp51', //ueditor
  'api_url'=>'http://tp51/index.php',
  'avatar_url'=>'http://tp51/index.php/picture/avatar',
  'picture_url'=>'http://tp51/index.php/picture/index',
  'file_curl_upload_url'=>'http://tp51/index.php/file/curl_upload',
  'upload_path'=>'http://tp51/', // 上传用

  //全局的接口配置信息
  'by_api_config'=>[
      'alg'           =>'md5_v3',
      'client_id'     =>'by571846d03009e1',
      'client_secret' =>'964561983083ac622f03389051f112e5',
      'api_url'       =>'http://tp51/index.php',
      'debug'         =>false
  ],
  // 数据库配置
  'database'   =>[
    //v5.06+ 关闭日期自动转换
    "datetime_format" =>false,
    // 数据库类型
    'type'           => 'mysql',

    // 服务器地址    // 数据库名    // 用户名    // 密码
    'hostname'       => '127.0.0.1',
    'database'       => 'fly',
    'username'       => 'root',
    'password'       => '1',

    'hostport'       => '3306',
    // 连接dsn
    'dsn'            => '',
    // 数据库连接参数
    'params'         => [],
    // 数据库编码默认采用utf8
    'charset'        => 'utf8',
    // 数据库表前缀
    'prefix'         => 'f_',
    // 数据库调试模式
    'debug'          => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'         => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'    => false,
    // 读写分离后 主服务器数量
    'master_num'     => 1,
    // 指定从服务器序号
    'slave_no'       => '',
    // 是否严格检查字段是否存在
    'fields_strict'  => true,
    // 数据集返回类型 array 数组 collection Collection对象
    'resultset_type' => 'array',
    // 是否自动写入时间戳字段
    'auto_timestamp' => false,
    // 是否需要进行SQL性能分析
    'sql_explain'    => false,
  ],
  //本地socketlog 修改host文件 和 config.php
  //127.0.0.1 slog.thinkphp.cn
  'log' => [
    // 文件形式日志
    // 'type' => 'File', // 支持 file socket trace sae
    // 'path' => LOG_PATH,
    // 'file_size'=>51200 //50M

    // 关闭日志写入
    'type'  => 'test',

    // socketlog形式 bug:slow
    // 'type'                => 'socket',
    // 'optimize'            => false,
    // 'show_included_files' => false,
    // 'error_handler'       => false,
    // 'host'                => '127.0.0.1',//'slog.thinkphp.cn',
    // 'force_client_ids'    => [],
    // 'allow_client_ids'    => [],
  ],
];