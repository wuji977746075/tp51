<?php
/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/2/9
 * Time: 上午11:42
 */

namespace app\src\wxpay\action;


use app\src\base\action\BaseAction;
use think\Exception;

class WxPayJsAction extends BaseAction
{
    public function __construct()
    {
        ini_set('date.timezone','Asia/Shanghai');
        require_once(VENDOR_PATH.'WxPayApiV3/api/WxPay.JsApiPay.php');
        require_once(VENDOR_PATH.'WxPayApiV3/lib/WxPay.Api.php');
    }

    public function buildPay($name, $code, $price)
    {
        //打印输出数组信息
        function printf_info($data)
        {
            foreach($data as $key=>$value){
                echo "<font color='#00ff55;'>$key</font> : $value <br/>";
            }
        }

        //①、获取用户openid
        $tools = new \JsApiPay();
        $openId = $tools->GetOpenid();
        if(!$openId) exception('获取openid失败','1001');

        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($name);
        $input->SetAttach($code);
        $input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee($price * 100); //单位分
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("sp");
        $input->SetNotify_url(config('site_url').'/wxpay/wxnotify');
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        //echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        //printf_info($order);
        $jsApiParameters = $tools->GetJsApiParameters($order);

        //③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
        /**
         * 注意：
         * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
         * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
         * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
         */
        return $jsApiParameters;
    }
}