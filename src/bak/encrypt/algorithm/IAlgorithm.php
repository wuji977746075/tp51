<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-15
 * Time: 9:42
 */

namespace app\src\encrypt\algorithm;

/**
 * Class IAlgorithm
 * 数据传输算法
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\encrypt\algorithm
 */
abstract  class IAlgorithm
{

    /**
     * 对传输的数据流进行解密
     * @author hebidu <email:346551990@qq.com>
     * @param $transmissionData
     * @param $key
     */
    abstract function decryptTransmissionData($transmissionData,$key);

    /**
     * 获取用于传输、流通的数据流
     * @author hebidu <email:346551990@qq.com>
     * @param $param
     * @param $key
     * @return mixed
     */
    abstract function encryptTransmissionData($param,$key);

    /**
     * 签名校验
     * @param $sign
     * @param AlgParams $algParams
     * @return mixed
     * @internal param $data
     */
    abstract function verify_sign($sign,AlgParams $algParams);
    
    /**
     * 签名
     * @param $param
     * @return mixed
     */
    abstract function sign(AlgParams $param);

    /**
     * 解密数据内容
     * @param $encryptData
     * @return mixed
     */
    abstract function  decryptData($encryptData);

    /**
     * 加密数据内容
     * @param $data
     * @return mixed
     */
    abstract function  encryptData($data);
}