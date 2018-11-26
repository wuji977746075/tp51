<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2018-03-21 13:40:21
 * Description : [His 辅助类]
 */

namespace by\sdk\his;
use by\sdk\his\Security;
use by\sdk\his\HisCode;
use by\component\regex\IdCardRegex;
use by\component\user\logic\UcenterMemberLogic;

/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\
 * @example
 */
class Util {

  // 是否为 手机号码
  public static function ismobile($mobile=''){
    $reg = '/^1[0-9]{10}$/';
    return preg_match($reg, $mobile);
  }
  // 是否为 正常的uid
  public static function isuid($uid=''){
    $info = (new UcenterMemberLogic)->getInfo(['id'=>$uid,'status'=>1]);
    return $info;
  }
  // 是否为 身份证号
  public static function isidcard($card_no=''){
    return IdCardRegex::isIdCard($card_no);
  }
  // 是否为 性别
  public static function issex($sex=''){
    return in_array($sex,[0,1,2]);
  }
  // 是否为 中文姓名
  public static function ischinesename($name){
    return preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $name);
  }
  // 是否为 年(1000-2999)-月-日
  public static function isdatetime($name){
    return preg_match('/^[1-2][0-9]{3}-[012][0-9]{1}-[0123][0-9]{1}$/', $name);
  }
  //数组key 转小写
  public static function arr2low(array $a=[]){
    $ret = [];
    foreach ($a as $k=>$v) {
      $ret[strtolower($k)] = is_array($v) ? self::arr2low($v) : $v;
    }
    return $ret;
  }
  //数组key 转大写
  public static function arr2up(array $a=[]){
    $ret = [];
    foreach ($a as $k=>$v) {
      $ret[strtoupper($k)] = is_array($v) ? self::arr2up($v) : $v;
    }
    return $ret;
  }
  public function arrHandle(array $a=[],$add=true){
    $ret = [];
    foreach ($a as $k=>$v) {
      if($add) $ret[$k] = '<![CDATA['.(string)$v.']]';
      else  $ret[$k] = preg_replace('/^<!\[CDATA\[.*\]\]$/', '', (string)$v);
    }
    return $ret;
  }

  // 错误检查
  public function parseErr($ret_arr,$err){
    //系统错误
    //业务错误
  }

  //     生成签名
  //     $Obj必须为一维数组
  public function getSign($Obj,$key) {
    unset($Obj['SIGN_TYPE']);
    $str = '';
    //签名步骤一：按字典序排序参数
    ksort($Obj);
    foreach ($Obj as $k => $v) {
      $str = ($str ? $str.'&' : '').strtoupper($k).'='.$v;
    }
    //签名步骤二：在str后加入KEY
    $str = $str."&KEY=".$key;
    // echo $str.'<br />';
    //签名步骤三：MD5加密
    $sign = strtoupper(md5($str));
    // echo '签名:'.$sign.'<br />';

    return $sign;
  }

  // 签名验证
  public function checkSign($data,$key){
    $sign = $data['SIGN'];
    unset($data['SIGN']);
    // echo $this->getSign($data,$key).'--';
    // echo $this->getSign($data,$key).'--';
    return $sign == $this->getSign($data,$key);
  }

  public function encrypt($req_arr,$key,$root="REQ",$xml=true){
    $s = new Security($key);
    if($xml){
      $xml_str = $this->arr2xml($req_arr,$root,'');
      return $s->encrypt($xml_str);
    }else{
      $root && $req_arr = [$root=>$req_arr];
      return $s->encrypt(json_encode($req_arr));
    }
  }

  public function decrypt($rsp_xml_str,$key){
    $s = new Security($key);
    return $s->decrypt($rsp_xml_str);
  }

  //获取指定长度的随机字符串
  protected function getRandChar($length){
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol)-1;

    for($i=0;$i<$length;$i++){
      $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
    }
    return $str;
  }

  public function soap($fun,$req,$url){
    // libxml_disable_entity_loader(false);
    // $options = [
    //   'soap_version'  =>SOAP_1_2,
    //   'trace'         =>true,
    //   'encoding'      =>'UTF-8',
    //   'ssl'   => [
    //     'verify_peer' => false
    //   ],
    //   'https' => [
    //     'curl_verify_ssl_peer'  => false,
    //     'curl_verify_ssl_host'  => false
    //   ],
    // ];
    // $req['ROOT'] = $this->arrHandle($req['ROOT']);
    // echo "soap1.2 <br /> 请求 => $url <br />方法 => $fun <br />参数=>";dump($req);
    try{
      // nusoap / curl
      $soap = new \nusoap_client($url,true);
      $soap->soap_defencoding = 'UTF-8';
      $soap->decode_utf8 = false;
      $err = $soap->getError();
      if ($err) {
        throw new \Exception('<p><b>Constructor error: ' . $err . '</b></p>');
      }
      $ret = $soap->call($fun,[$req]);

      // soap
      // $soap = new \SoapClient($url,$options);
      // $ret = $soap->__soapCall($fun,[$req]);
    }catch(\Exception $e){
      halt($e->getMessage());
      // echo $soap->__getLastRequest();
    }
    // $ret = $soap->NetTest($req);
    // echo $soap->__getLastRequest();
    // echo $soap->__getLastResponse();
    // halt('---');
    // $ret = get_object_vars($ret);
    $ret = $ret[lcfirst($fun).'Result'];
    // echo "返回 => <br />";halt($ret);
    return $ret;
  }

  //post https请求，CURLOPT_POSTFIELDS xml格式
  public function postXmlCurl($xml,$url,$second=5) {
    //初始化curl
    $ch = curl_init();
    $options = [
      CURLOPT_URL            =>$url,
      CURLOPT_TIMEOUT        =>$second,
      // CURLOPT_PROXY          =>'8.8.8.8',
      // CURLOPT_PROXYPORT      =>8080,
      CURLOPT_SSL_VERIFYPEER =>FALSE,
      CURLOPT_SSL_VERIFYHOST =>FALSE,
      CURLOPT_HTTPHEADER     =>['Content-Type:text/xml;charset=utf-8','Content-Length:'.strlen($xml)],
      CURLOPT_POST           =>TRUE,
      CURLOPT_POSTFIELDS     =>$xml,
      CURLOPT_HEADER         =>0,
      CURLOPT_RETURNTRANSFER =>TRUE,//结果装string
    ];
    // Note:
    // As with curl_setopt(), passing an array to CURLOPT_POST will encode the data as multipart/form-data, while passing a URL-encoded string will encode the data as application/x-www-form-urlencoded.
    curl_setopt_array($ch,$options);
    //运行curl
    $data = curl_exec($ch);
    //返回结果
    if($data) {
      curl_close($ch);
      return $data;
    } else {
      $error = curl_errno($ch);
      if($error == 28) throws('请求超时,请重试');
      else throws('HIS_CURL_'.$error);
      // echo "curl出错，错误码: $error"."<br>";
      // echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
      // curl_close($ch);
      // return false;
    }
  }

  /*
      获取当前服务器的IP
  */
  protected function get_client_ip()
  {
      if ($_SERVER['REMOTE_ADDR']) {
      $cip = $_SERVER['REMOTE_ADDR'];
      } elseif (getenv("REMOTE_ADDR")) {
      $cip = getenv("REMOTE_ADDR");
      } elseif (getenv("HTTP_CLIENT_IP")) {
      $cip = getenv("HTTP_CLIENT_IP");
      } else {
      $cip = "unknown";
      }
      return $cip;
  }


  /**
   *xml转成数组
   * 会去掉最外层标签
   */
  function xml2arr($xmlstr) {
    // addTestLog($xmlstr,'','');
    //禁止引用外部xml实体
    // libxml_disable_entity_loader(true);
    $ret = json_decode(json_encode(simplexml_load_string($xmlstr, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $this->fix_list_one($this->fix_null($ret));
  }
  // 修复返回列表(_LIST)成员为1是bug
  // @params : $flag :2维数组key后缀
  function fix_list_one($a,$flag='_LIST') {
    if(is_array($a)){ // []
      foreach ($a as $k=> &$v) {
        if(is_array($v)){ // k=>[]
          $v = $this->fix_list_one($v);//自内而外递归
          if(substr($k, -5) == $flag){ // xx_LIST=>[]
            $one = false;
            foreach ($v as $kk=>$vv) {
              if(!is_numeric($kk)){ //检测到2维数组
                $one = true;
                break;
              }
            }
            $one && $v = [$v];
          }
        }else{ // k=>v
        }
      }
    }
    return $a;
  }
  // 修复null值为''
  function fix_null($a){
    if(is_array($a)){
      $r = [];
      foreach ($a as $k=>$v) {
        if(is_array($v)){
          $r[$k] = $v ? $this->fix_null($v) : '';
        }else{
          $r[$k] = $v;
        }
      }
      return $r;
    }else{
      return $a ? $a : '';
    }
  }

  // 数组或字符串 转xml
  function arr2xml($arr,$root='ROOT',$head='<?xml version="1.0" encoding="UTF-8"?>') {
      $xml = $head.($this->arr_to_xml($arr,$root,true));
      return $xml;
  }
  // 数组或字符串 转xml
  function arr_to_xml($arr,$root="ROOT",$plus=true){
    $s    = "";
    $root = strtoupper($root);
    $pre  = "<$root>";$last = "</$root>";
    if(is_array($arr)){
      foreach ($arr as $k =>$v) {// 0=>[] or k=>[] or k=>v
        $k    = strtoupper($k);
        $temp = is_numeric($k);
        if($temp && is_array($v)){ // 0=>[]
            $plus = false;
            $s .= $this->arr_to_xml($v,$root);
        }else{
          if(is_array($v)){ // k=>[]
            $s .= $this->arr_to_xml($v,$k);
          }else{ // k=>v
            $s .= "<$k><![CDATA[".$v."]]></$k>";
          }
        }
      }
    }else{
      $s = $arr;
    }
    $s = $plus ? $pre.$s.$last : $s;
    return $s;
  }
}