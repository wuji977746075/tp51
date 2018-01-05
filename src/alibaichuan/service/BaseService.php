<?php
/**
 * Created by PhpStorm.
 * User: rainbow
 * Date: 2017-12-23 15:16:05
 * Time: 16:14
 */

namespace src\alibaichuan\service;

abstract class BaseService
{
    protected $config;


    protected function obj2arr($list){
        $ret = [];
        if(is_array($list)){
            foreach ($list as $v) {
                $ret[] = $this->obj2arr($v);
            }
            return $ret;
        }elseif(is_object($list)){
            if(empty($list)) return '';
            $list = (array) $list;
            foreach ($list as $k=>$v) {
                $ret[$k] = $this->obj2arr($v);
            }
            return $ret;
        }else{
            return $list;
        }
    }
    protected function checkErr($r){
        $r = $this->obj2arr($r);
        if(isset($r['code']) && $r['code'])
            throw new \Exception('IM'.$r['code'].':'.$r['msg']);
        return $r;
    }
    /**
     * 解析返回中的 异常
     * @param object $resp 淘宝调用返回xml对象
     * @return ['code'=>"0:成功 其它异常",'info'=>'返回数据/ 异常信息','extra'=>'返回的原始信息xml对象'];
     */
    public function parseResp($resp){
        $parse_code = "0";
        $parse_msg  = "请求成功";
        $extra = $resp;
        if(is_object($resp) && isset($resp->error_response)){
            //
            $parse_code = "-1";
            $error_response = $resp->error_response;
            $code = $error_response->code;
            $msg  = $error_response->msg;
            $sub_code = $error_response->sub_code;
            $sub_msg  = $error_response->sub_msg;
            if($sub_code == 'isv.param-error'){
                $parse_msg  = "(接口请求参数错误)";
            }elseif($sub_code == "isp.service-error"){
                $parse_msg  = "(服务内部错误,请稍后重试.)";
            }elseif($sub_code == "isv.data-duplicate-error"){
                $parse_msg  = "(重复添加)";
            }elseif($sub_code == "isp.http-read-timeout"){
                $parse_msg  = "(连接超时)";
            }elseif($sub_code == "isp.http-connection-refuse"){
                $parse_msg  = "(连接失败)";
            }elseif($sub_code == "isp.http-connection-timeout"){
                $parse_msg  = "(连接超时)";
            }else{
                $parse_msg = "";
            }
            $parse_msg = $msg.$parse_msg;

        }
        return ['code'=>$parse_code,'info'=>$parse_msg,'extra'=>$extra];
    }

    /**
     * @return \TopClient
     * @throws \Exception
     */
    public function getTopClient(){
        $client = new \TopClient($this->getAppKey(),$this->getAppSecret());
        $client->format = "json";
        return $client;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getAppKey(){
        $appkey = $this->config['app_key'];
        if(empty($appkey)){
            throw new \Exception("缺少app_key信息！~");
        }
        return $appkey;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getAppSecret(){
        $app_secret = $this->config['app_secret'];
        if(empty($app_secret)){
            throw new \Exception("缺少app_secret信息！~");
        }

        return $app_secret;
    }

    /**
     * BaseAlibaichuanService constructor.
     * @throws \Exception
     */
    function __construct($cfg)
    {
        $this->config = $cfg;;

        if(!is_array($this->config)){
            throw new \Exception("请先配置百川的配置信息！~");
        }

        //引入2016年09月14日最新代码
        require_once __DIR__.'/../sdk/TopSdk.php';
    }

}