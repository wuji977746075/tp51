<?php
return [
    // 默认输出类型

    'default_return_type' => 'html',
    'view_replace_str'    => [
        '__PUBLIC__' => __ROOT__ . '/static/' . request()->module() . '',
        '__JS__'     => __ROOT__ . '/static/' . request()->module() . '/js',
        '__CSS__'    => __ROOT__ . '/static/' . request()->module() . '/css',
        '__IMG__'    => __ROOT__ . '/static/' . request()->module() . '/img',
        '__SELF__' => request()->url(),
        '__CDN__'    => ITBOYE_CDN,

    ],

    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => APP_PATH . 'weixin/tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => APP_PATH . 'weixin/tpl' . DS . 'dispatch_jump.tpl',
    'real_site_url' => 'http://dehong.8raw.com/',

    'site_url' => 'http://dehong.8raw.com/weixin.php',
    //'site_url' => 'http://127.0.0.1/github/itboye_hutouben_api/public/mobile.php',
    'by_api_config'       =>[
    'alg'=>'md5_v2',
    'client_id'=>'by571846d03009e1',
    'client_secret'=>'964561983083ac622f03389051f112e5',
    'api_url'=>'http://dehong.8raw.com/index.php',
    'debug'=> false
    ],

    //微信支付配,
    'WXPAY_PAY_CONFIG'=>[
    'APPID'=>'wxf9e07872347069b9',
    'APPSECRET'=>'80d1b9e43eadb523fb0442b416593583',
    'MCHID'=>'1445776302',
    'KEY'=>'e19ba38181849ede8a637e456eea1010',//在微信发送的邮件中查看,patenerkey

    'NOTIFYURL'=>'http://dehong.8raw.com/weixin.php/weixin/Ajaxinform/ajaxinform',
    'JSAPICALLURL'=>'http://2test.8raw.com/index.php/Shop/OnlinePay/pay?showwxpaytitle=1',
    'SSLCERTPATH'=>'/alidata/8rawcert/10027619/apiclient_cert.pem',
    'SSLKEYPATH'=>'/alidata/8rawcert/10027619/apiclient_cert.pem',
    'CURL_PROXY_HOST' => "0.0.0.0",
    'CURL_PROXY_PORT' => '0',
    'REPORT_LEVENL' => 1,
    'PROCESS_URL'=>'http://dehong.8raw.com/weixin.php/weixin/Ajaxinform/ajaxinform',//异步处理地址
    ],


    //数据字典
    'datatree'=>[
    'double_ahead_plus'=>6225,//0,00Q00D,后后级消费-二级奖励-加提现积分
    'ahead_plus'=>6224,//0,00Q00C,后级消费-一级奖励-加提现积分,,

    'after_become_elite_plus'=>6223,//0,00Q00B,下级成为精英-出货-加提现积分,,
    'after_become_elite_minus'=>6222,//0,00Q00A,下级成为精英-出货-减库存积分,,

    'after_become_angel_minus'=>6221,//0,00Q009,推荐升级成天使-出货-减库存积分,,
    'after_become_angel_plus'=>6220,//0,00Q008,推荐升级成天使-出货奖励-加提现积分,

    'recommend_plus'=>6219,//0,00Q007,推荐升级成天使-推荐奖励-加提现积分,
    'after_buy_minus'=>6218,//0,00Q006,下级存货-减库存积分,
    'after_buy_plus'=>6228,//下级存货-加提现积分



    'become_leader_minus'=>6217,//0,00Q005,购买领袖商品-减提现积分
    'become_elite_minus'=>6216,//0,00Q004,购买精英商品-减提现积分
    'become_angel_minus'=>6215,//0,00Q003,购买天使商品-减提现积分
    'become_leader_plus'=>6227,// 0 00Q00F  购买领袖商品-加库存积分
    'become_elite_plus'=>6226,//  0 00Q00E  购买精英商品-加库存积分

    'buy_no_points_minus'=>6214,//0,00Q002,购买非积分商品-减提现积分
    'buy_points_minus'=>6213,//0,00Q001,购买积分商品-减提现积分

    'stock_points_plus'=>6229,//购买积分商品-加库存积分
    'stock_points_minus'=>6230,//存积分商品-减提现积分

    ],
    'alipay_config' => array (
        //应用ID,您的APPID。
        'app_id' => "2017041406724361",

        //商户私钥，您的原始格式RSA私钥
        'merchant_private_key' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCpkhSQXqP0FJdhzDw0FjLk7zaCo1K/jp5mv5BRxEcqQpzoY8QTYKvAZOcDNvfgB0g3lncGulI9cwPbEP+v6owMGxfgnYle9u++tHT/zq7B7B7tP4mlGg/9F1uASm2Ffh+M4JJpFjuZs1mIAFINfY703f3hUgxvsnv1+exdQs3jSYylwPSvRPRJNzlvSaEupc2hZ5He/lCfhbgwx3rckHLFgjCcA5liD7Zvvvgw23qb1rVB/Q2Jgj67s51MWtfUXvdt45mGAzCxKVn1aQ/O5tMg5AK9Xr/Xv7PmVZFGnbVfMciLyLDtRZeoepYNNQh5laAppSzEjkEh430Kp51BkIkLAgMBAAECggEBAIcLWKc1R/td3sW7IoMU3slmHRJjQcZerZYEn8oOt+JXFKEauw/3oDDj6vqrbzMSeZgTk41fG5nmnD59xjNyZsBJX3W54KopnPtSug7zqke0ZVjqjhNXsz4LuQptu9VhgXzvaIqaczLQG9Bs+OvXf99RYBxu/IqEKabWuD/5sQB9ybnJp4NS/C0hji8gbY3gVGfjlIaP/xG4TRxTXRKlcr8Agtn7TvZbf5tSEYDXWw2OHylAYkKSmV46IMAnFj3r9iLn7b9qn3P/3WLqWPVrBUa+X/0yv9oKjTvAvO9l4KbaBg3Ttp4Zs3LbFON53hAd96yv0mn1svNlsJxyL1SWoVECgYEA1VcIZ93XWJfDBb0MMcmM/AOQAE8RIpHGinxhmF+X2bshTsV43UU6D53FDl2flqKeDLXOmPnf4pjpJ4uqCx1oBs5TMQ9X8S65p1mqmfqtvVN+9LllQ4XPd2G4wPpwoS49jsG6kPmKQaspkUtBkXxPs5YTwFCfO1//FNMUI78zzWkCgYEAy3p5s9Tfs+63kt84NUaMywsABF1toqnctit8Z+uWjqtf7Su5XF3DVQXF9PlvDIIArPIHSZMyIv/5QKf0xjw+Y5CR0AN2I4Yv3mKsFROeWkfIZ3zF5VvjcDB5ecAbyzpI39516OQRp2/lh5FdVDQ97OCYvcCP28z22rI2rjLPcFMCgYBAoUMspCou7proTCM7mgGZ/0JKtalNPbhWD+RJEvyfu28VvAEnl2dFf9hIx9gm/FDBLPrPoTNQJF7N2iPDhj8TgMH3JNzRdgRMTH/Acg7cQkU9wkSJNipp0jL0U2p3idigPNRWQyK9TisjlxbgCjbjAt4s7r+ubyCJoICDa9M3sQKBgFxNXaHPAf9cXZfZfGTmcP22KrbQLuioBz/38AIwI2bqpKQwvnAA9Je/+7GJ6O8Slf3d+KJOjGLGHSpQRkAame6OaSakO1YYC+Tke80/zgiEIYuYVSkbCswcoS4ykCkbcm1jO7d+ped/Ye6iaWTGIgUvSPpivJ9nld9VEmrpD6KTAoGAdtYjQuEFmlgu6sCsYjkRlclqp588l7R+fQ3sEMbqAUA9sJ6JTy4Zz3cSp/o+A6td8WaafMP+wO76bb59S12uH89zNRv5CinE1gN/BSXHj87ldqmuRSOdnsN8X8DJnjDyjJuJ2gj6CiJ9XthAYtvIgMXNTk4TEVcmawGPdGMskBU=',

        //异步通知地址
        'notify_url' => "http://dehong.8raw.com/weixin.php/weixin/Alipaytest/notify_url",

        //同步跳转
        'return_url' => "http://dehong.8raw.com/weixin.php/weixin/Alipaytest/return_url",

        //编码格式
        'charset' => "UTF-8",

        //签名方式
        'sign_type'=>"RSA2",

        //支付宝网关
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

        //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        'alipay_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAj1ZccPufZ2y6sJsHrVHmpavFqBej6ZZqrZVVSlKQBd3T7TcIt1s594ivo3Vt4ih7aoA44gEcNwPkByohTVBqY0LsufoYlpriCnNbuafXb53Fi0Q4hvh5le/fBUVyMLob9kGh51PTQsvbs+K+UQaHLEEslemGfh0EQPn5XzR+4fM2u86Bixsgos2ealeK3RFeaWJ8aEsH/teKrglqFgVGjYVqLOELDcsbaSZuXe1nIl6UCxDIGOLwvKhSQ+JCeBts3LknhF/J9QekKd5CB6t5alPOvWbY7crT2sjTDanP9F1tGSRrVGBshqwVf94FGpAa3i532mZvx45Fs0bbwlkBBwIDAQAB',
    ),

];