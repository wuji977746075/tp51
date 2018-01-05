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
 * Class Md5V3Alg
 * 使用了openssl扩展
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\encrypt\algorithm
 */
class Md5V3Alg extends IAlgorithm
{

    function decryptTransmissionData($transmissionData,$desKey)
    {
        $data = openssl_decrypt(base64_decode($transmissionData),"des-ecb", $desKey);

        return ($data);
    }

    function encryptTransmissionData($param,$desKey)
    {
        $data = openssl_encrypt(json_encode($param) ,"des-ecb", $desKey);
        return base64_encode($data);
    }

    function verify_sign($sign,AlgParams $algParams)
    {
        $tmp_sign = Md5V3Alg::sign($algParams);
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