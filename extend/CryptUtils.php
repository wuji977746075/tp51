<?php
use \ErrorCode as EC;
use src\base\traits\Jump;
class CryptUtils {
    const ERROR = EC::CRYPT_ERROR;
    use Jump;
    /**
     * 签证签名
     * @param $sign
     * @param $data
     * @return bool
     * @throws BaseException
     */
    public static function verify_sign($sign,$data){
        $tmp_sign = self::sign($data);
        if($sign == $tmp_sign) return true;
        return false;
    }

    /**
     * 对数据进行解密,base64_decode 2次而已
     * @param $encrypt_data
     * @return string
     * @internal param $decrypt_data
     * @internal param $data
     */
    public static function decrypt($encrypt_data){
        return json_decode(base64_decode(base64_decode($encrypt_data)),JSON_OBJECT_AS_ARRAY);
    }

    /**
     * 对数据进行加密,base64_decode2次而已
     * @param $data
     * @return string
     */
    public static function encrypt($data){
        $str = json_encode($data);
        return base64_encode(base64_encode($str));
    }

    /**
     * 签名
     * @param $param  [client_secret,data,time,type,notify_id]
     * @return string
     * @throws \Exception
     */
    public static function sign($param){
        $client_secret = isset($param['client_secret']) ? $param['client_secret'] : '';
        $notify_id     = isset($param['notify_id'])     ? $param['notify_id']     : ''; //请求id
        $time = isset($param['time']) ? $param['time'] : '';
        $type = isset($param['type']) ? $param['type'] : '';
        $data = isset($param['data']) ? $param['data'] : '';

        empty($client_secret) && $this->err("client_secret参数非法!");
        empty($time) && $this->err("time参数非法!");
        empty($type) && $this->err("type参数非法!");
        empty($notify_id) && $this->err("notify_id参数非法!");

        $text = $time.$type.$data.$client_secret.$notify_id;
        return md5($text);
    }
}