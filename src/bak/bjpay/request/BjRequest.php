<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-22
 * Time: 12:01
 */

namespace app\src\bjpay\request;


use app\src\base\utils\CacheUtils;
use app\src\bjpay\po\BjBaseReqPo;
use app\src\bjpay\po\BjLoginReqPo;
use app\src\bjpay\po\BjLoginRespPo;
use app\src\bjpay\po\BjLogoutReqPo;
use app\src\bjpay\po\BjNormalReqPo;
use app\src\bjpay\po\BjQueryBalanceReqPo;
use app\src\bjpay\po\BjQueryBalanceRespPo;
use think\Cache;
use think\Config;

/**
 * Class BjRequest
 * 北京银行银企接口
 * @package app\src\bjpay\request
 */
class BjRequest
{
    private static $nchttps_url = "http://106.14.57.159:5777";
    private static $ncsign_url = "http://106.14.57.159:4437";
    private static $sessionId = "";
    private static $is_debug = false;

    public static function isDebug($debug=false){
        self::$is_debug = $debug;
    }

    public static function getSessionID(){
        return Cache::get("bj_pay_session_id");
    }

    public static function setSessionID($sessionId){
        //20分钟
        Cache::set("bj_pay_session_id",$sessionId,1200);
    }

    /**
     * 查询余额请求
     * @param BjQueryBalanceReqPo $reqPo
     * @return array
     */
    public static function queryBalance(BjQueryBalanceReqPo $reqPo){

        $result = self::request($reqPo);
        if($result['status']){
            $respPo = new BjQueryBalanceRespPo();
            $parseResult = $respPo->loadXml($result['info']);
            if($parseResult['code'] == 0){
                $result['info'] = $respPo;
            }
        }
        return $result;
    }

    /**
     * 一般交易请求
     * @param BjNormalReqPo $reqPo
     * @return array
     */
    public static function request(BjNormalReqPo $reqPo){
//        dse_sessionId：在登录时得到的，每次交易必须上传。
//		opName：交易名称。每个交易按照约定，具有唯一性。
//reqData：交易请求XML报文
        $url = self::$nchttps_url."/servlet/com.bccb.inbs.api.servlet.APIReqServlet";
        $reqData = $reqPo->toXML();
        dump($reqData);
//        $reqData = preg_replace("#\s+#","",$reqData);
        $reqData = urlencode($reqData);
        $reqData = mb_convert_encoding($reqData, 'GBK');
        $url = $url . '?dse_sessionId='.$reqPo->getDseSessionId();
        $url = $url . '&opName='.urlencode($reqPo->getOpName());
        $url = $url . '&reqData='.$reqData;
        $result = self::curl($url,[],[],"POST");
        $result['info'] = mb_convert_encoding($result['info'], "UTF-8", "GBK");

        return $result;
    }

    /**
     * 单笔实时交易
     * @param BjNormalReqPo $reqPo
     * @return array
     */
    public static function singleTrade(BjNormalReqPo $reqPo){
//        dse_sessionId：在登录时得到的，每次交易必须上传。
//		opName：交易名称。每个交易按照约定，具有唯一性。
//reqData：交易请求XML报文
        $url = self::$nchttps_url."/servlet/com.bccb.inbs.api.servlet.APIReqServlet";
        $xml = $reqPo->toXML();
        $result = self::sign($xml);
        if(!isset($result['sign'])){
            return ['status'=>false,'info'=>'签名失败'];
        }

        $reqData = $result['sign'];

        $reqData = preg_replace("#\s+#","",$reqData);
        $reqData = urlencode($reqData);
        $reqData = mb_convert_encoding($reqData, 'GBK');
        $url = $url . '?dse_sessionId='.$reqPo->getDseSessionId();
        $url = $url . '&opName='.urlencode($reqPo->getOpName());
        $url = $url . '&reqData='.$reqData;
        $result = self::curl($url,[],[],"POST");
        //$result['info'] = mb_convert_encoding($result['info'], "UTF-8", "GBK");
        if($result['status']){
            $respPo = new BjLoginRespPo();
            $info = $result['info'];
            self::debugDump($info);
            $parseResult = $respPo->loadXml($info);
            if($parseResult['code'] == 0){
                if($respPo->isSuccess()){
                    $result['info'] = $respPo;
                }else{
                    return ['status'=>false, 'info'=>$respPo->getErrMsg()];
                }

            }
        }

        return $result;
    }


    /**
     * 签退交易
     * @param BjLogoutReqPo $reqPo
     * @return array
     */
    public static function logout(BjLogoutReqPo $reqPo){
        self::debugDump("session_id = ".$reqPo->getDseSessionId());
        $url = self::$nchttps_url."/servlet/com.bccb.inbs.api.servlet.APISignOffReqServlet";
        $url = $url . '?dse_sessionId='.$reqPo->getDseSessionId();
        $result = self::curl($url,[],[],"POST");
        $result['info'] = mb_convert_encoding($result['info'], "UTF-8", "GBK");

        return $result;
    }

    private static function debugDump($var){
        if(self::$is_debug){
            dump($var);
        }
    }

    /**
     * 登录交易
     * @param BjLoginReqPo $reqPo
     * @return array
     */
    public static function login(BjLoginReqPo $reqPo){

        $url = self::$nchttps_url."/servlet/com.bccb.inbs.api.servlet.APISessionReqServlet";

        self::debugDump($reqPo->toXML());

        $xml = $reqPo->toXML();
//        $xml = mb_convert_encoding($xml, 'GBK');
        $result = self::sign($xml);
        if(!isset($result['sign'])){
            return ['status'=>false,'info'=>'签名失败'];
        }

        $reqData = $result['sign'];
        $reqData = preg_replace("#\s+#","",$reqData);
        $reqData = urlencode($reqData);
        $reqData = mb_convert_encoding($reqData, 'GBK');
//        dump($reqData);
//        $reqData = "MIIM5AYJKoZIhvcNAQcCoIIM1TCCDNECAQExCzAJBgUrDgMCGgUAMIIBXwYJKoZIhvcNAQcBoIIBUASCAUw8P3htbCB2ZXJzaW9uPSIxLjAiIGVuY29kaW5nPSJHQjIzMTIiPz4NCjxCQ0NCRUJhbmtEYXRhPg0KCSAgIDxvcFJlcT4JIA0KCQkgICA8b3BOYW1lPkNlYmFua1VzZXJMb2dvbk9wPC9vcE5hbWU%2BIA0KCQkgICA8c2VyaWFsTm8%2BQkpIVEIxNzM5MTM0NDU5NTcxODMwNDk8L3NlcmlhbE5vPiAgICAgICANCiAgICAgICAgICAgPHJlcVRpbWU%2BMTQ4NjYxOTA5OTwvcmVxVGltZT4gDQogICAgICAgICAgIDxSZXFQYXJhbT48dXNlcklEPkNsb3VkPC91c2VySUQ%2BPHVzZXJQV0Q%2BMDAwMDAwPC91c2VyUFdEPjwvUmVxUGFyYW0%2BDQoJICAgIDwvb3BSZXE%2BDQo8L0JDQ0JFQmFua0RhdGE%2BDQoNCqCCClAwggXAMIIEqKADAgECAgobQAAAAAAAABQfMA0GCSqGSIb3DQEBBQUAMFIxCzAJBgNVBAYTAkNOMQ0wCwYDVQQKDARCSkNBMRgwFgYDVQQLDA9QdWJsaWMgVHJ1c3QgQ0ExGjAYBgNVBAMMEVB1YmxpYyBUcnVzdCBDQS0yMB4XDTEzMDkxNTE2MDAwMFoXDTE4MDkxNjE1NTk1OVowZDELMAkGA1UEBhMCQ04xDTALBgNVBAoMBEJKQ0ExEjAQBgNVBAoMCVBUQ0EtVU5JVDEYMBYGA1UECgwP5paH5Lu26K%2BB5Lmm5LqMMRgwFgYDVQQDDA%2Fmlofku7bor4HkuabkuowwgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAKiu5FJ%2FbS03%2FCEiTi8VSyZfvPwwJgWqx79T4y1rLpsfdXz9Tn44uhpZjc%2Bxutij1AvHItNunTzlxXjbLYOwLjazmutH3Vlv8KKaQX8Mi7eWjhsKxUWExw7DV7i5ASVbdzcomqLFZRVsYsQcwjveMlF7p7Dit8qa5Ch12LMe8wCZAgMBAAGjggMIMIIDBDAfBgNVHSMEGDAWgBT7t9RWF1iMI33V%2BEIB1O13m1fr6TAdBgNVHQ4EFgQUfR3geokc3FAfat7NSQ%2BcpHA8w14wga0GA1UdHwSBpTCBojBsoGqgaKRmMGQxCzAJBgNVBAYTAkNOMQ0wCwYDVQQKDARCSkNBMRgwFgYDVQQLDA9QdWJsaWMgVHJ1c3QgQ0ExGjAYBgNVBAMMEVB1YmxpYyBUcnVzdCBDQS0yMRAwDgYDVQQDEwdjYTRjcmwxMDKgMKAuhixodHRwOi8vbGRhcC5iamNhLm9yZy5jbi9jcmwvcHRjYS9jYTRjcmwxLmNybDAJBgNVHRMEAjAAMBEGCWCGSAGG%2BEIBAQQEAwIA%2FzAXBghghkgBhvhEAgQLSkoxNTE0Njg5MzgwGwYIKlaGSAGBMAEEDzAxMTAwMDEwMDA1MDcxODAUBgUqVgsHCQQLSkoxNTE0Njg5MzgwGQYGKlYLBwEIBA8xMENASkoxNTE0Njg5MzgwgecGA1UdIASB3zCB3DA1BgkqgRwBxTiBFQEwKDAmBggrBgEFBQcCARYaaHR0cDovL3d3dy5iamNhLm9yZy5jbi9jcHMwNQYJKoEcAcU4gRUCMCgwJgYIKwYBBQUHAgEWGmh0dHA6Ly93d3cuYmpjYS5vcmcuY24vY3BzMDUGCSqBHAHFOIEVAzAoMCYGCCsGAQUFBwIBFhpodHRwOi8vd3d3LmJqY2Eub3JnLmNuL2NwczA1BgkqgRwBxTiBFQQwKDAmBggrBgEFBQcCARYaaHR0cDovL3d3dy5iamNhLm9yZy5jbi9jcHMwYgYIKwYBBQUHAQEEVjBUMCgGCCsGAQUFBzABhhxPQ1NQOi8vb2NzcC5iamNhLm9yZy5jbjo5MDEyMCgGCCsGAQUFBzAChhxodHRwOi8vY3JsLmJqY2Eub3JnL2NhaXNzdWVyMDIGA1UdEQQrMCmkJzAlMQ8wDQYDVQQRDAYxMDAwMDAxEjAQBgNVBAgMCeWMl%2BS6rOW4gjALBgNVHQ8EBAMCAzgwDQYJKoZIhvcNAQEFBQADggEBAJbyYas9jYifYkABpzy6%2F4CALNlzszDpnOuo%2F0gPiTle0cG4QW4jX5FpqT%2FfJ9EtaNAzQGhm33IFAgvcijMnanQUNbGdZNh2Ivp5tBpKUZTHGks0F5WOEcN3KM%2FEfCUSwhXwAtWMixlVHvVD%2FD0TradpBXaiYACHqFxJwtWJ%2B5sE6968%2F3dvjDZniiOJ3AwcDI%2FsdbCuBIt8Sbc5VrJWw9V6scsOWGEmqkc88lLH62KYo6%2B50kmwtdcGBssKXRS69Lv3KgrnRwVer74Berw8pMFpynx2yff%2BGLXOx6ItZUVoM8dWo03kPIZppNAuJpC4n1xH2Vr%2BfeafBh3Ly34VzgMwggSIMIIDcKADAgECAgoQAAAAAAAAAgADMA0GCSqGSIb3DQEBBQUAMFoxCzAJBgNVBAYTAkNOMQ0wCwYDVQQKDARCSkNBMR0wGwYDVQQLDBRQdWJsaWMgVHJ1c3QgUm9vdCBDQTEdMBsGA1UEAwwUUHVibGljIFRydXN0IFJvb3QgQ0EwHhcNMDUwMTAxMDgwMDAwWhcNMjQxMjMxMDgwMDAwWjBSMQswCQYDVQQGEwJDTjENMAsGA1UECgwEQkpDQTEYMBYGA1UECwwPUHVibGljIFRydXN0IENBMRowGAYDVQQDDBFQdWJsaWMgVHJ1c3QgQ0EtMjCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAKyGWfyvjdxEQhdTrBEfPg0%2F3S0S0hMClIw11U8nG8CQPMbgjyREZyv6XrLyY9v7p6GV1YKUgyCxvX7n9SrJA0FyyJKfiU5Su3I81WH5xCJcYgtmyltgPT22vJXLpKKVd2RYX5cGI%2FswzYzttVKkuHvWYwP3jK%2B5dVMb64aq%2BefcMAfLDMXNM3dGsuPbaWYenrSQ4yGmdtvlWl9QZYhWNUOpx2JSRRDFfsPPMSCSKozANqNSCzmtaJAZjnhHAbCMSKITd4%2F50z%2B9yI0eIDsoksCMgPZVFh8Hxb3XUgxrZHQsHyuGEsyKHUFZd4ErljyKdXDaDaY0jKYX40%2FPz2OijUcCAwEAAaOCAVYwggFSMB8GA1UdIwQYMBaAFGBXY5wwG3CiAQOZ0to29GkIwDylMB0GA1UdDgQWBBT7t9RWF1iMI33V%2BEIB1O13m1fr6TAMBgNVHQ8EBQMDBwYAMD8GA1UdIAQ4MDYwNAYAMDAwLgYIKwYBBQUHAgEWImh0dHA6Ly93d3cuYmpjYS5vZy5jbi9jcHMvY3BzLmh0bWwwDwYDVR0TBAgwBgEB%2FwIBAjCBrwYDVR0fBIGnMIGkMHOgcaBvpG0wazELMAkGA1UEBhMCQ04xDTALBgNVBAoMBEJKQ0ExHTAbBgNVBAsMFFB1YmxpYyBUcnVzdCBSb290IENBMR0wGwYDVQQDDBRQdWJsaWMgVHJ1c3QgUm9vdCBDQTEPMA0GA1UEAxMGcHRyb290MC2gK6AphidodHRwOi8vbGRhcC5iamNhLm9yZy5jbi9hcmwvcHRyb290LmNybCAwDQYJKoZIhvcNAQEFBQADggEBABVHv10KB6GNtw8bHWyYlGVVnXyZEkpOGb%2BFcRbjXPcBhsIHonGPIKa%2Fm9u7f8fRupjBJnqweaL8EfDnpdBEmZxx170dIy%2B1XUTxLVo7o8rHuyPgoEpPnTBE6%2FRgql0VdSsBHAZspFNb4s4dP7Y2IQc4he98ofi9WqvJ2CCS%2FKoywsi%2BcfiPs5UfA1%2F35SGrT6%2Bw9KUdwgG6Te0rNRu4oxXh3NxJ66swBCqnph0tInIQvNWsI98GEX%2FaJTfwXwJNXDUQp1bMCXjJxSl4hPIZTKAcNLTpcEm57sYa8zCqvnpsgA9nLKkxPViUwefp4jz8T4wpFqrFCaoaPDUodRemTPMxggEGMIIBAgIBATBgMFIxCzAJBgNVBAYTAkNOMQ0wCwYDVQQKDARCSkNBMRgwFgYDVQQLDA9QdWJsaWMgVHJ1c3QgQ0ExGjAYBgNVBAMMEVB1YmxpYyBUcnVzdCBDQS0yAgobQAAAAAAAABQfMAkGBSsOAwIaBQAwDQYJKoZIhvcNAQEBBQAEgYBIUrdAYpLSmNqi0%2BdcX9Jx5TUfLHFTRbR40p72gSCh8tZmcEgdCFeIboRMAA1CrXU7BFQtc5tIMwWSLhfa5vC0QyLg7mU6TGCrY34QXAlyr9DJoYzsc93L6JdiV2vi5XLVhP6fejXmdilXeBrQpYpYRW%2FvrS7Q8jYshl49oWPZTw%3D%3D";

        self::debugDump($reqData);
        $url = $url .'?netType=3&reqData='.$reqData;
        $result = self::curl($url,[],[],"POST");
        $result['info'] = mb_convert_encoding($result['info'], "UTF-8", "GBK");

        if($result['status']){
            $respPo = new BjLoginRespPo();
            $info = $result['info'];
            $arr = explode("|",$info);
            self::debugDump($arr);
            if(count($arr) == 2){
                self::setSessionID($arr[0]);
                $parseResult = $respPo->loadXml($arr[1]);
                if($parseResult['code'] == 0){
                    $result['info'] = $respPo;
                }
            }
        }

        return $result;
    }

    /**
     * 签名服务
     * @param $data
     * @return array
     */
    public static function sign($data){

//        $length = strlen($data);
        $result = self::curl(self::$ncsign_url,$data,array("Content-type: INFOSEC_SIGN/1.0"));
        vendor("simple_html_dom.simple_html_dom");
        $html = new \simple_html_dom();
        $html->load($result['info']);
        $sign = $html->find('sign',0);
        return ['status'=>true,'info'=>$result['info'],'sign'=>$sign->plaintext];
    }

    /**
     * 请求
     * @param $url
     * @param $data
     * @param string $header
     * @param string $method
     * @return array
     */
    private static function curl($url,$data,$header="",$method="POST"){
        $url = rtrim($url,"/");
        $ch     = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'API');
        curl_setopt($ch, CURLOPT_TIMEOUT,100);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $error = curl_errno($ch);
        if($error){
            return ['status' => false, 'info' => 'curl_error='.$error];
        }else{
            return ['status' => true, 'info' => $tmpInfo];
        }
    }
}