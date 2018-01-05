<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-15
 * Time: 9:45
 */

namespace app\src\encrypt\algorithm;

use app\src\encrypt\des\Des;
use app\src\encrypt\exception\CryptException;

/**
 * Class Md5V2Alg
 * 目前使用于 php
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\encrypt\algorithm
 */
class Md5V2Alg extends IAlgorithm
{

    function decryptTransmissionData($transmissionData,$desKey)
    {
        $data = Des::decode(base64_decode($transmissionData) , $desKey);

        return ($data);
    }

    function encryptTransmissionData($param,$desKey)
    {
        $data = Des::encode(json_encode($param) , $desKey);
        return base64_encode($data);
    }

    function verify_sign($sign,AlgParams $algParams)
    {
        $tmp_sign = Md5V2Alg::sign($algParams); 
        if($sign == $tmp_sign){
            return true;
        }
        return false;
    }

    function sign(AlgParams $param)
    {
        $param->isValid();

        return $param->getSign();
    }

    function decryptData($encryptData)
    {
        return json_decode(base64_decode(base64_decode($encryptData)),JSON_OBJECT_AS_ARRAY);
    }

    function encryptData($data)
    {
        $str = json_encode($data,0, 512);
        return base64_encode(base64_encode($str));
    }

}