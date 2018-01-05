<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-23
 * Time: 16:25
 */

namespace app\src\message\sms;


use app\src\base\exception\BusinessException;
use app\src\message\facade\MessageEntity;
use app\src\message\interfaces\IMessage;

class JuheSms implements IMessage
{

    private $key;
    private $mobile;
    private $tpl_id;
    private $tpl_value;

    public function __construct($cfg)
    {
        if(!isset($cfg['key'])){
            throw new BusinessException('sms config error params');
        }

        $this->key = $cfg['key'];

    }

    public function init(MessageEntity $msg)
    {
        $this->mobile = $msg->getMobile();
        $this->tpl_id = $msg->getTplId();
        $this->tpl_value = $msg->getTplValue();

        return $this;
    }

    function create()
    {

    }

    function send()
    {
        if(empty($this->key)){
            return ['status'=>false,'info'=>'参数错误'];
        }

        $sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
        $smsConf = [
            'key'       => $this->key, //您申请的APPKEY
            'mobile'    => $this->mobile, //接受短信的用户手机号码
            'tpl_id'    => $this->tpl_id, //您申请的短信模板ID，根据实际情况修改
            'tpl_value' => $this->tpl_value //您设置的模板变量，根据实际情况修改
        ];

        $content = $this->juhecurl($sendUrl,$smsConf,1); //请求发送短信

        if($content){
            $result = json_decode($content,true);
            $error_code = $result['error_code'];
            if($error_code == 0){
                //状态为0，说明短信发送成功
                return ['status'=>true,'info'=>"短信已发送,请注意查收!"];
            }else{
                //状态非0，说明失败
                $msg = $result['reason'];
                return ['status'=>false,'info'=>"短信发送失败(".$error_code.")：".$msg];
            }
        }else{
            //返回内容异常，以下可根据业务逻辑自行修改
            //echo "请求发送短信失败";
            return ['status'=>false,'info'=>"请求发送短信失败"];
        }

    }

    /**
     * 请求接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string $params [请求的参数]
     * @param  int $ipost [是否采用POST形式]
     * @return  string
     */
    private function juhecurl($url,$params=false,$ispost=0){
        $httpInfo = [];
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }

}