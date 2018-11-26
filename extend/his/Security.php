<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2018-03-21 13:37:07
 * Description : [His 加解密类 , 每次需实例化]
 */

namespace by\sdk\his;

class Security {
  protected $method;
  protected $key;
  protected $iv;
  protected $options;
  public function __construct($key, $method = 'AES-128-ECB', $options = OPENSSL_RAW_DATA) {
    $this->key    = $key;
    $this->method = $method;

    if(in_array($method,openssl_get_cipher_methods())){
      $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    }else{
      throw new \Exception('error ssl method');
    }
    $this->options = $options;
  }
  public function encrypt($str) {
    return base64_encode(openssl_encrypt($str, $this->method, $this->key, $this->options, $this->iv));
  }

  public function decrypt($str) {
    return openssl_decrypt(base64_decode($str), $this->method, $this->key, $this->options, $this->iv);
  }
}