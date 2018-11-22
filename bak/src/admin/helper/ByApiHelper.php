<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-13
 * Time: 15:20
 */

namespace app\src\admin\helper;
use app\src\encrypt\algorithm\AlgFactory;
use app\src\encrypt\algorithm\AlgParams;
use think\Exception;

/**
 * 博也接口调用帮助类
 * Class ByApiHelper
 * @package app\src\base\helper
 */
class ByApiHelper
{
    private $apiUrl;//接口地址，根地址
    private $client_id;
    private $client_secret;
    private $algInstance;//通信算法
    private $alg;
    private $debug = true;

    private static $helper = false;

    public static function getInstance(){
        if(!self::$helper){
            $cfg = AdminConfigHelper::getByApiConfig();

            self::$helper = new ByApiHelper($cfg);
        }
        return self::$helper;
    }

    function __construct($cfg = array()){
        if(!isset($cfg['api_url']) || !isset($cfg['client_id'])
            || !isset($cfg['client_secret']) || !isset($cfg['alg'])){
            throw  new Exception("接口调用配置参数缺失");
        }
        $this->apiUrl        = rtrim($cfg['api_url'],'/');
        $this->client_id     = $cfg['client_id'];
        $this->client_secret = $cfg['client_secret'];
        $this->alg           = $cfg['alg'];
        $this->algInstance   = AlgFactory::getAlg($this->alg);
        $this->debug         = isset($cfg['debug']) ? $cfg['debug'] : false;
        if(empty($this->algInstance)){
            throw  new Exception("无效的通信算法");
        }

    }

    /**
     *
     * @param $data
     * @param string $api_url
     * @return array
     * @internal param $url
     * @internal param bool $is_debug
     */
    public function callRemote($data,$api_url=''){

        if(!isset($data['type']) || empty($data['type']))
            return ['status' => false, 'info' => lang('param-need',['type'])];
        $type = $data['type'];
        unset($data['type']);
        if(!isset($data['api_ver']) || empty($data['api_ver']))
            return ['status' => false, 'info' => lang('param-need',['api_ver'])];
        $apiVer = $data['api_ver'];
        unset($data['api_ver']);
        if(!isset($data['notify_id']) || empty($data['notify_id'])){
            $notify_id = time();
        }else{
            $notify_id = $data['notify_id'];
            unset($data['notify_id']);
        }

        $encrypt_data = $this->algInstance->encryptData($data);
        $algParams = new AlgParams();
        $algParams->setClientId($this->client_id);
        $algParams->setClientSecret($this->client_secret);
        $algParams->setData($encrypt_data);
        $algParams->setNotifyId($notify_id);
        $algParams->setTime(strval(time()));
        $algParams->setType($type);
        $algParams->setApiVer($apiVer);

        $itboye = $this->algInstance->encryptTransmissionData($algParams->getResponseParams(),$this->client_secret);
        $param = [
            'itboye'    => $itboye,
            'client_id' => $this->client_id,
            'alg'       => $this->alg
        ];

        $r = $this->curlPost($param,$api_url);
        // 同一进程 - 出错全部终止
        if($r['status']){
            //curl请求成功 - 不代表服务器执行结果
            $info = $r['info'];
            if(isset($info['data']) && isset($info['sign'])){

                $decrypt_data = $this->algInstance->decryptData($info['data']);
                $algParams = new AlgParams();
                $algParams->initFromArray($info);
                $algParams->setClientSecret($this->client_secret);

                //签名校验
                if(!$this->algInstance->verify_sign($info['sign'],$algParams)){
                    $r = ['status'=>false,'info'=>LL('request data sign verify fail !')];
                }
                if($decrypt_data['code'] != 0){
                    $pre = $this->debug ? 'API_'.$type.'=>' : '';
                    $r = ['status'=>false,'info'=>$pre.$decrypt_data['data'],'code'=>$decrypt_data['code']];
                }else{
                    $r = ['status'=>true,'info'=>$decrypt_data['data']];
                }

            }else{
                $r = ['status'=>false,'info'=>$info];
            }

        }

        return $r;
    }

    /**
     * 取得accessToken
     * @return mixed
     */
    public function getAccessToken(){
        return $this->client_id;
    }


    /**
     *
     * @param $data  array
     * @param bool $api_url
     * @return array
     * @internal param $url
     */
    protected function curlPost($data, $api_url=false) {

        $url = $this->apiUrl;

        if(!empty($api_url)){
            $url = $api_url;
        }
        $url = rtrim($url,"/");
        $ch     = curl_init();
        $header = ['Accept-Charset'=>"utf-8"];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.64 Safari/537.36'); //chrome46/mac
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $error = curl_errno($ch);
        if($error){
            return ['status' => false, 'info' => $error];
        }else{

            $js = json_decode($tmpInfo,true);
            if(is_null($js)){
                $js = "$tmpInfo";
            }
            return ['status' => true, 'info' => $js];
        }
    }
}