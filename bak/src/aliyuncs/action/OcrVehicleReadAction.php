<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/3
 * Time: 11:45
 */

namespace app\aliyuncs\action;

use app\src\base\action\BaseAction;

/**
 * Class OcrVehicleReadAction
 * 行驶证识别接口
 * @package app\aliyuncs\action
 */
class OcrVehicleReadAction extends BaseAction
{
    private $appcode = "d9683b82762043a4a00daa592b6f62f6";

    public function read($base64Image = '')
    {

        $host = "https://dm-53.data.aliyun.com";
        $path = "/rest/160601/ocr/ocr_vehicle.json";
        $method = "POST";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $this->appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/json; charset=UTF-8");
        $querys = "";
        $bodys = "{\"inputs\":[{\"image\":{\"dataType\":50,\"dataValue\":\"" . $base64Image . "\"}}]}";
        $url = $host . $path;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        $result = curl_exec($curl);
        var_dump($result);

    }
}