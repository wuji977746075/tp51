<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 16/4/22
 * Time: 14:08
 */

/**
 * DES
 * Class DesCrypt
 * @package app\vendor\Crypt
 */
final class DesCrypt{

    /**
     * 外层对称加密
     * @param 内容|$s
     * @param 密钥|$key
     * @param string $salt
     * @return 内容
     */
    static public function encode($s,$key) {
        // test only
        // $str = json_encode($s,0, 512);
        // return base64_encode(base64_encode($str));

        return openssl_encrypt($s ,"des-ecb", $key);

        // $td   = \mcrypt_module_open(MCRYPT_DES,'','ecb',''); //使用MCRYPT_DES算法,ecb模式
        // $size = mcrypt_enc_get_iv_size($td);//设置初始向量的大小
        // $iv   = mcrypt_create_iv($size, MCRYPT_RAND);//创建初始向量

        // $ks = mcrypt_enc_get_key_size($td);//返回所支持的最大的密钥长度（以字节计算）
        // // echo "<br/>".$ks;
        // $key = substr($key,0,$ks);
        // // echo "<br/>".$key;
        // mcrypt_generic_init($td, $key, $iv); //初始处理

        // //要保存到明文
        // //加密
        // $encode = mcrypt_generic($td, $s);

        // //结束处理
        // mcrypt_generic_deinit($td);
        // return $encode;
    }

    /**
     * 外层对称解密
     * @param $s    string
     * @param $sKey string 密钥
     * @return string
     * @internal param $content
     */
    static public function decode($s,$sKey) {
        // test only
        // return json_decode(base64_decode(base64_decode($s)),JSON_OBJECT_AS_ARRAY);
        return openssl_decrypt($s,"des-ecb", $sKey);

        // $td   = mcrypt_module_open(MCRYPT_DES,'','ecb',''); //使用MCRYPT_DES算法,ecb模式
        // $size =mcrypt_enc_get_iv_size($td);//设置初始向量的大小
        // $iv   = mcrypt_create_iv($size, MCRYPT_RAND);//创建初始向量
        // $ks   = mcrypt_enc_get_key_size($td);//返回所支持的最大的密钥长度（以字节计算）

        // $sKey = substr($sKey,0,$ks);

        // //初始解密处理
        // mcrypt_generic_init($td, $sKey, $iv);
        // //解密
        // $decrypted = mdecrypt_generic($td, $encode_content);
        // //解密后,可能会有后续的\0,需去掉
        // $decrypted=trim($decrypted);
        // //结束
        // mcrypt_generic_deinit($td);
        // mcrypt_module_close($td);
        // return $decrypted;
    }

    // 业务参数解密
    function decryptData(array $a) {
        return json_decode(base64_decode(base64_decode($a)),JSON_OBJECT_AS_ARRAY);
    }
    // 业务参数加密
    function encryptData(array $a) {
        $str = json_encode($a,0, 512);
        return base64_encode(base64_encode($s));
    }
}