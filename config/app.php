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
// | 应用设置
// +----------------------------------------------------------------------

const ITBOYE_CDN = 'http://115.29.220.243:900/';
return [
    //本地不同步百川信息
    'alibaichuan_sync' => true,
    //阿里百川 - 安卓
    'alibaichuan_cfg'=>[
        'is_debug'   => true,//是否测试
        'app_key'    => '24734772',
        'app_secret' => 'b3b7793a1dfa15b3491545eb7bf34405',
    ],

    //全局的接口配置信息
    'by_api_config'=>[
        'alg'           =>'md5_v3',
        'client_id'     =>'by571846d03009e1',
        'client_secret' =>'964561983083ac622f03389051f112e5',
        'api_url'       =>'http://115.29.220.243:903/index.php',
        'debug'         =>false
    ],

    //地址配置
    'site_url'=>'http://115.29.220.243:903', //ueditor
    'api_url'=>'http://115.29.220.243:903/index.php',
    'avatar_url'=>'http://115.29.220.243:903/index.php/picture/avatar',
    'picture_url'=>'http://115.29.220.243:903/index.php/picture/index?id=',
    'file_curl_upload_url'=>'http://115.29.220.243:903/index.php/file/curl_upload',
    'upload_path'=>'http://115.29.220.243:903/', // 上传用

    //百度地图ak
    'baidu_map_ak' =>'NB4fAMqntPrs1RSGkTXBzjK9FVCMx9ix',//300w/d

    //验证码配置
    'code_cfg'=>[
        'type'=>'local', //local:本地弹窗 qcloud: 腾讯云 juhe: 聚合
        'extra'=>[
            //腾讯云配置
//            'sdk_app_id'=>"1400018532",
//            "app_key"=>"18d087393ef7df76214d5f6ec087a5ba"
            //聚合配置
            "key"=>"b771aa8f615679f52990ce44ad2d9042"
        ]
    ],

    //阿里百川
    'alibaichuan_cfg'=>[
        'is_debug'   => true,//是否测试
        'app_key'    => '23500185',
        'app_secret' => 'b7f5a4c77e7e91f5266d1f9ea7468874',
    ],
    //支持的支付方式
    'app_support_payways'=>[
        ['name'=>'支付宝','type'=>1,'desc'=>'需要手机安装支付宝'],
        ['name'=>'Paypal','type'=>2,'desc'=>'支持paypal'],
        ['name'=>'微信支付','type'=>3,'desc'=>'需要手机安装微信'],
        ['name'=>'余额支付','type'=>4,'desc'=>'钱包余额支付'],
    ],

    //多语言支持
    'lang_support'=>[
        ['name'=>'简体中文','value'=>'zh-cn'],
        ['name'=>'한국','value'=>'ko'],
        ['name'=>'English','value'=>'en'],
        ['name'=>'Tiếng Việt','value'=>'vi'],
    ],

    //融丰支付配置
    // 'rf_pay_config'=>[
    //     //接口地址
    //     'api_url'=> "http://api.ktb.wujieapp.net",
    //     //
    //     'org_no'=> "99999999",
    //     'mer_no'=> "101607256868749",
    //     //
    //     'key'=> "bea91d7d61ecd36fcabfd4303c75a06f",
    //     //rsa私钥 base64形式
    //     'pem_path'=> "/www/wwwroot/api.guannan.com/application/src/rfpay/pem/base64.pem",
    //     //订单创建成功后的回调地址
    //     "no_card_order_backUrl"=>"http://api.ihomebank.com/public/index.php/rfpay"
    // ],

    // 加密salt定义
    'security_salt' => [
        'password'  => 'itboyep;[230',
    ],
    //分页配置
    'paginate' => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],

    //队列
    'queue'=>[
        'type'=>'database', //驱动类型，可选择 sync(默认):同步执行，database:数据库驱动,redis:Redis驱动,topthink:Topthink驱动
        //或其他自定义的完整的类名
        'table' => 'queue_jobs'
    ],
    /* 音频上传相关配置 */
    'user_audio_upload' =>[
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 5120*1024, //上传的文件大小限制 (不大于0或者不填-不做限制)
        'exts'     => 'mp3', //必填,允许上传的文件
        // 'autoSub'  => true, //自动子目录保存文件
        'subName'  => ['date', 'Ymd'], //必填,子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './upload/userAudio/',
    ],
    /* 音频上传驱动 */
    'audio_upload_driver'=>'local',
    /* 图片上传相关配置 */
    'user_picture_upload' => [
//上传公用配置
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 500*1024, //上传的文件大小限制 (不大于0或者不填-不做限制)
        'exts'     => 'jpg,gif,png,jpeg', //必填,允许上传的文件
        // 'autoSub'  => true, //自动子目录保存文件
        'subName'  => ['date', 'Ymd'], //必填,子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './upload/userPicture/', //必填,保存根路径

//新版上传配置
        // 'house_rate' => [4,3],//房源图片比例4:3

//一下为兼容curl_upload的老版配置
        // 'savePath' => '',
        //curl使用 - 保存路径 eg: '1/'
        // 'saveName' => ['uniqid', ''], //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        // 'saveExt'  => '',//文件保存后缀，空则使用原后缀
        'replace'  => true,//存在同名是否覆盖
        'hash'     => true,//是否生成hash编码
    ],
    //图片上传相关配置（文件上传类配置）
    'picture_upload_driver'=>'local',

    //阿里百川
    // 'ALBAICHUAN_CFG'=>[
    //     'is_debug'   => true,//是否测试
    //     'app_key'    => '23456139',
    //     'app_secret' => '4647cb9e09046b8ef8e56c5aa5f95a61',
    // ]


    // 应用调试模式
    'app_debug'              => Env::get('app_debug',false),
    // 应用Trace
    'app_trace'              => Env::get('app_trace',false),
    // 应用模式状态
    "app_status"             => Env::get('app_status',''),

    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [
        'src'=>'../src/'
    ],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => 'trim,htmlspecialchars',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,

    // 视图输出字符串内容替换
    'view_replace_str'       => [],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => Env::get('think_path') . 'tpl/dispatch_jump.tpl',
    'dispatch_error_tmpl'    => Env::get('think_path') . 'tpl/dispatch_jump.tpl',

    // 异常页面的模板文件
    'exception_tmpl'         => Env::get('think_path') . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => ''
];
