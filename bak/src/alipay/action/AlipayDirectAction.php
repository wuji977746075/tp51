<?php
/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/1/17
 * Time: 下午2:02
 */

namespace app\src\alipay\action;


use app\src\base\action\BaseAction;

class AlipayDirectAction extends BaseAction
{

    private $alipay_config;

    public function __construct()
    {
        //引入支付官方sdk
        vendor("AlipayApp.lib.alipay_submit", '.class.php');

        $this->alipay_config = $this->getAlipayConfig();
    }

    /**
     * 支付宝网页即时到账 构建请求参数
     */
    public function create_direct_pay_by_user($out_trade_no,$subject,$total_fee,$body=''){
        /**************************请求参数**************************/
        //商户订单号，商户网站订单系统中唯一订单号，必填
        //$out_trade_no = $_POST['WIDout_trade_no'];

        //订单名称，必填
        //$subject = $_POST['WIDsubject'];

        //付款金额，必填
        //$total_fee = $_POST['WIDtotal_fee'];

        //商品描述，可空
        //$body = $_POST['WIDbody'];

        /************************************************************/

        //构造要请求的参数数组，无需改动
        $alipay_config = $this->alipay_config;
        $parameter = array(
            "service"       => $alipay_config['service'],
            "partner"       => $alipay_config['partner'],
            "seller_id"  => $alipay_config['seller_id'],
            "payment_type"	=> $alipay_config['payment_type'],
            "notify_url"	=> $alipay_config['notify_url'],
            "return_url"	=> $alipay_config['return_url'],

            "anti_phishing_key"=>$alipay_config['anti_phishing_key'],
            "exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
            "out_trade_no"	=> $out_trade_no,
            "subject"	=> $subject,
            "total_fee"	=> $total_fee,
            "body"	=> $body,
            "timeout_express"=>'15m',
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
            //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
            //如"参数名"=>"参数值"

        );

        //建立请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
    }

    private function getAlipayConfig(){

        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        //合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner']		= '2088221666614017';//正式

        //收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
        $alipay_config['seller_id']	= $alipay_config['partner'];

        //商户的私钥,此处填写原始私钥去头去尾，RSA公私钥生成：https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.nBDxfy&treeId=58&articleId=103242&docType=1
        //$alipay_config['private_key']	= 'MIICXAIBAAKBgQD83gbimMReYugCUpRFiEA8ZpGJoDRONlZGAi1j7L+KoWuoS165MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCsNiOkk2F22n5WgY/6qUhMFSLU03IB4OSpOzB3/kTXRQUvQVRCmikKLTlRQgHPbQfdUrRycIWK3+GDeXfqK7mr9Nb8KZzpjNteCdD1z+TA/xbcHumPd4v3Z7pcYdoFGdVmvyxUDcnn1DUHlDHeEKez3CSGl1zHUIyKYW0ypEX5BNL4KWC/rch/Ycr9mQhp+83vml7ccLBL22LD6MUyp8h0x2JYEouaJQ4ptNagZdt3FOiGIptTJm5FjHUw9Dyh8BdToxY0nPcRLckQiW6NE7a7fx9Phxpa9/USVmsxIU3muD0qIgVUATVxIfQfhDkH31Q+ovNezARgvDdA5qEph7dtAgMBAAECggEALk18NeLeoMtMjsvVP4tGghZowBp+G90w/gOJG6pFLjBDnZoOIr46bJ8OOzfpLrFdHPgAxohUj6t0ghol0NOi34Y8S56QrwcNcHJpOb6E/hNYPSUenVGCU6oTJIW2zgpNI4whShRPBkM7YudIpTGLNcPqGjoWWwzgTNC3QJeErNhan/FcqxwiMX0+ag9lELU7Fzz4XnW3keUu+eAszjbOlSjdGiCjKiDVlctQQ1blgKAh2+ArRSeRmXkxgUXrFO69zO7fr4YvHPJiB6PPPgNHQRL8LsTPjWxY+5EbvnlGXou791Gdf+chRTq3p6P5zZuNr6xgAWzuHdQolIa2jkDqbQKBgQDqLKG+yg7q/e9Oy28u7McJ8uGXY1AFo7zQnv8tFCZV8iSHtmto1p5t4DV3XfY8cdfYQji0NwdBHtRctu93LRmVgLtcpbPGrjVDElfBvPisKZ8QdEHG/WtXDwuZwKKimy/1sL0OEskCrndRIkJbgs65QPG4vUEBLcgJIpmle88rdwKBgQC8QxT3xSwS/ThuowVLhNCGQ9JPkPtFVCaFBkGF5S7uN7y5jRIAGmO4Hl1yt7vWhlrw2voG4M5JkVCgbIt//C84Pl/ZT5ln5YrWITWKjuVXayAQsM4h3V5/xD0oPIq+iGsOQv4fuPruQU7v1nyyn9ILB08cW3mObO56q+5cO6OlOwKBgQDdwfJR6KsD4gFzTrc1ash6JWV4pXWsQYiWz1q656/+B9aMJjXFDCjvyDkZlwON7gkHJH7qOopGItncCujupOjraQMFE24RofuSTpaIQ1oCP1AAlveLZ4T05qyHp6Lb9bYPJpWB9Ewim/EmBhls64y0ZkoCNkaOxTn/XKK/0WU4tQKBgBf13bRPJvXfvo/uNZ1P8Q41kY3I4QII3MIvcqVs7tUoyN9Awhq7QRfM3Y3dLo32GZrv88RuVjLsyLsyNWr7mLLq1V4eEGM1xr7MCTlySGQg4Trelc2flAhk3HfDhNENIbr18cvtyhoKu9YwkTxWtO/sZTgxuD3VRWDdgv/AI2rDAoGAFSj4/5YC1SwOZnVqMdA69wJUxEyBBWpwBU2Gln3IgggtVIkbLYk7P5PwWgTmShz0NmQgCKon0Eu8M1AI3fbZJUAAZdtnqHgJ+9synjqnsOTXdRKmCcaS9s/W3pVrQ6yEsCN2vK+7gMQN2MhKJTO5IYQmUyVWRh2+a5n3i41fwps=';

        //商户的私钥（后缀是.pen）文件相对路径
        $alipay_config['private_key_path']	= APP_PATH.'src/alipay/key/zheng/partner.pem';

        //支付宝公钥（后缀是.pen）文件相对路径
        $alipay_config['ali_public_key_path']= APP_PATH.'src/alipay/key/alipay_public_key.pem';

        // 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
        $alipay_config['notify_url'] =  config('api_url')."/alipaydirect/notify";

        // 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
        $alipay_config['return_url'] = config('site_url')."/order/pay_success";

        //签名方式 不需修改
        $alipay_config['sign_type']    = strtoupper('RSA');

        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset']= strtolower('utf-8');

        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert']    = getcwd().'\\cacert.pem';

        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport']    = 'http';

        // 支付类型 ，无需修改
        $alipay_config['payment_type'] = "1";

        // 产品类型，无需修改
        $alipay_config['service'] = "create_direct_pay_by_user";

        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

        //↓↓↓↓↓↓↓↓↓↓ 请在这里配置防钓鱼信息，如果没开通防钓鱼功能，为空即可 ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

        // 防钓鱼时间戳  若要使用请调用类文件submit中的query_timestamp函数
        $alipay_config['anti_phishing_key'] = "";

        // 客户端的IP地址 非局域网的外网IP地址，如：221.0.0.1
        $alipay_config['exter_invoke_ip'] = "";

        //↑↑↑↑↑↑↑↑↑↑请在这里配置防钓鱼信息，如果没开通防钓鱼功能，为空即可 ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

        return  $alipay_config;
    }
}