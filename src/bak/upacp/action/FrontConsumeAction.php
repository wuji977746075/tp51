<?php
/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/3/16
 * Time: 上午11:06
 */

namespace app\src\upacp\action;


use app\src\base\action\BaseAction;
use com\unionpay\acp\sdk\AcpService;
use com\unionpay\acp\sdk\SDKConfig;

class FrontConsumeAction extends BaseAction
{
    private $merId = '403310048990028';
    public function __construct()
    {
        vendor("upacp.sdk.acp_service");
    }

    public function createHtml($orderId, $txnTime, $txnAmt)
    {
        header ( 'Content-type:text/html;charset=utf-8' );
        $params = array(

            //以下信息非特殊情况不需要改动
            'version' => SDKConfig::getSDKConfig()->version,                 //版本号
            'encoding' => 'utf-8',				  //编码方式
            'txnType' => '01',				      //交易类型
            'txnSubType' => '01',				  //交易子类
            'bizType' => '000201',				  //业务类型
            'frontUrl' =>  config('api_url').'/upacp/front',  //前台通知地址
            'backUrl' => config('api_url').'/upacp/back',	  //后台通知地址
            'signMethod' => SDKConfig::getSDKConfig()->signMethod,	              //签名方法
            'channelType' => '08',	              //渠道类型，07-PC，08-手机
            'accessType' => '0',		          //接入类型
            'currencyCode' => '156',	          //交易币种，境内商户固定156

            //TODO 以下信息需要填写
            'merId' => $this->merId,		//商户代码，请改自己的测试商户号
            'orderId' => $orderId,	//商户订单号，8-32位数字字母，不能含“-”或“_”，可以自行定制规则
            'txnTime' => $txnTime,	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间
            'txnAmt' => $txnAmt,	//交易金额，单位分


            // 请求方保留域，
            // 透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据。
            // 出现部分特殊字符时可能影响解析，请按下面建议的方式填写：
            // 1. 如果能确定内容不会出现&={}[]"'等符号时，可以直接填写数据，建议的方法如下。
            //    'reqReserved' =>'透传信息1|透传信息2|透传信息3',
            // 2. 内容可能出现&={}[]"'符号时：
            // 1) 如果需要对账文件里能显示，可将字符替换成全角＆＝｛｝【】“‘字符（自己写代码，此处不演示）；
            // 2) 如果对账文件没有显示要求，可做一下base64（如下）。
            //    注意控制数据长度，实际传输的数据长度不能超过1024位。
            //    查询、通知等接口解析时使用base64_decode解base64后再对数据做后续解析。
            //    'reqReserved' => base64_encode('任意格式的信息都可以'),
            'payTimeout' => date('YmdHis', strtotime('+15 minutes')),

            //TODO 其他特殊用法请查看 pages/api_05_app/special_use_preauth.php
        );

        AcpService::sign ( $params ); // 签名
        $url = SDKConfig::getSDKConfig()->frontTransUrl;

        $html_form = AcpService::createAutoFormHtml( $params, $url );

        return $html_form;
    }

}