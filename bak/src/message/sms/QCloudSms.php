<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-17
 * Time: 15:49
 */

namespace app\src\message\sms;


use app\src\base\exception\BusinessException;
use app\src\message\facade\MessageEntity;
use app\src\message\interfaces\IMessage;

class QCloudSms implements IMessage
{
    var $url;
    var $sdk_app_id;
    var $app_key;

    var $nationCode;
    var $phoneNumber;
    var $content;

    function __construct($config)
    {

        // url 需要根据我们说明文档上适时调整
        $this->url = "https://yun.tim.qq.com/v3/tlssmssvr/sendsms";

        if(!isset($config['sdk_app_id']) || !isset($config['app_key'])){
            throw new BusinessException('sms error params');
        }

        $this->sdk_app_id = $config['sdk_app_id'];
        $this->app_key = $config['app_key'];
    }

    public function init(MessageEntity $msg)
    {
        $this->nationCode = trim($msg->getCountry(),"+");
        $this->phoneNumber = $msg->getMobile();
        $this->content = $msg->getContent();

        return $this;
    }

    /**
         * @return mixed */
    function create(){

    }

    /**
     * @return mixed
     */
    function send()
    {
        $randNum = rand(100000, 999999);
        $wholeUrl = $this->url . "?sdkappid=" . $this->sdk_app_id . "&random=" . $randNum;
//            echo $wholeUrl;
        $tel = new \stdClass();
        $tel->nationcode = $this->nationCode;
        $tel->phone = $this->phoneNumber;
        $jsonData = new \stdClass();
        $jsonData->tel = $tel;
        $jsonData->type = "0";
        $jsonData->msg = $this->content;
        $jsonData->sig = md5($this->app_key . $this->phoneNumber);
        $jsonData->extend = "";     // 根据需要添加，一般保持默认
        $jsonData->ext = "";        // 根据需要添加，一般保持默认
        $curlPost = json_encode($jsonData);
//            echo $curlPost;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $wholeUrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec($ch);
        $info = "";
        $result = ['status'=>false,'info'=>''];
        if ($ret === false) {
            $info = (curl_error($ch));
        }
        else {

            $json = json_decode($ret, JSON_OBJECT_AS_ARRAY);
            if ($json['result'] != 0) {
                $info = $this->getErrMsg($json['result']);
            }else{
                $result['status'] = true;
                $info = lang('tip_sms_success');
            }
        }

        curl_close($ch);

        $result['info'] = $info;
        return $result;
    }

    private function getErrMsg($code){
        $err = [];
        $err['1001'] = lang('err_qcloud_1001');
        $err['1002'] = lang('err_qcloud_1002');
        $err['1003'] = lang('err_qcloud_1003');
        $err['1004'] = lang('err_qcloud_1004');
        $err['1006'] = lang('err_qcloud_1006');
        $err['1007'] = lang('err_qcloud_1007');
        $err['1008'] = lang('err_qcloud_1008');
        $err['1009'] = lang('err_qcloud_1009');
        $err['1011'] = lang('err_qcloud_1011');
        $err['1012'] = lang('err_qcloud_1012');
        $err['1013'] = lang('err_qcloud_1013');
        $err['1014'] = lang('err_qcloud_1014');
        $err['1015'] = lang('err_qcloud_1015');
        $err['1016'] = lang('err_qcloud_1016');
        $err['1017'] = lang('err_qcloud_1017');
        $err['1018'] = lang('err_qcloud_1018');
        $err['1019'] = lang('err_qcloud_1019');

        if(isset($err[$code])){
            return $err[$code];
        }

        return lang('err_qcloud_unknown');
    }

}