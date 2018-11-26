<?php

namespace by\sdk\his;
use by\sdk\his\Util;
use by\sdk\his\HisCode;
use by\api\constants\ErrorCode;

class HisHelper {
  private $config = null;
  public function __construct(){
    $this->config = [
      // 外网 xxx:8087(xxx) his(xxx)
      "URL" =>"http://xxx/APP/interface.asmx?WSDL",
      "KEY" =>"xxx", // 16位加密key
    ];
  }

  public static function getErrMsg($code,$msg=''){
    if(is_array($code)){
      return 'HIS_ERROR_'.$code['code'].':'.$code['msg'];
    }
    return 'HIS_ERROR_'.$code.':'.$msg;
  }
  // xml curl 返回解析 错误检查
  public function parseSoapRet($xml_str=''){
    // $this->error($xml_str,-1);

    $util = new Util();
    // 返回解析  xml_str=> arr
    $ret = $util->xml2arr($xml_str); // 去掉ROOT了
    empty($ret) && $this->error('his异常:'.$xml_str,1);
    !isset($ret['SIGN']) && $this->error('his未返回签名',1);
    // 签名检查
    if($util->checkSign($ret,$this->config['KEY'])){
      // 错误检查
      if($ret['RETURN_CODE']){
        $this->error($ret['RETURN_MSG'],$ret['RETURN_CODE']);
      }else{
        // 数据解密
        $msg = $util->decrypt($ret['RES_ENCRYPTED'],$this->config['KEY']);
        // 转换成 RES数组
        // $this->error($msg,-1);
        $msg = $util->xml2arr($msg); // 去掉RES了
        return $msg;
      }
    }else{
      $this->error('his返回验签失败',-1);
    }
  }
  //  [1001,'nettest'],1000,
  //  业务参数
  //  [
  //   'hos_id'=>1001,
  //   ...
  //  ]
  //  [100=>''] //其他业务错误
  // return data / throw
  // todo : soap缓存问题 造成第一次请求很慢
  public function curl(array $fun=[],$user_id=7,array $req=[]){
  // public function soap(array $fun=[],$user_id=7,array $req=[]){
    $util = new Util();
    $a = [
      'FUN_CODE'      =>$fun[0],
      'USER_ID'       =>$user_id,
      'SIGN_TYPE'     =>'MD5',
      'REQ_ENCRYPTED' =>$util->encrypt($req,$this->config['KEY']),
    ];
    $a['SIGN'] = $util->getSign($a,$this->config['KEY']);

    $xml_str = $util->arr2xml($a,'ROOT',''); // curl xml

    addTestLog('his soap pre',$a,$req);
    // echo '请求:' ;dump($xml_str);
    // $ret = $util->postXmlCurl($xml_str,$this->config['URL']);
    // addTestLog($this->config['URL'],$fun[1],['xml'=>$xml_str]);
    $ret = $util->soap($fun[1],['xml'=>$xml_str],$this->config['URL']);
    addTestLog('his soap ret',$a,$ret);

    // 返回解析 错误检查
    $ret = $this->parseSoapRet($ret);
    return $ret;
  }
  // 对外his 返回 : 返回错误时不加密
  public function ret($code,$msg,$data,$xml=true){
    $util = new Util();
    $ret = [ //必须为一维数组
      'RETURN_CODE'   =>$code,
      'RETURN_MSG'    =>$msg,
      'SIGN_TYPE'     =>"MD5",
      'RES_ENCRYPTED' =>$code ? '' : $util->encrypt($data,$this->config['KEY'],'RES',$xml),
    ];
    $ret['SIGN'] = $util->getSign($ret,$this->config['KEY']);
    $ret = $util->arr2xml($ret);
    return $ret;
  }
  // 对外his 获取+检查参数
  public function getPost($r){
    $r['REQ'] = (new Util)->decrypt($r['REQ_ENCRYPTED'],$this->config['KEY']);
    return $r;
  }
  // his curl
  public function soap(array $fun=[],$user_id=7,array $req=[]){
    $util = new Util();
    $a = [
      'FUN_CODE'      =>$fun[0],
      'USER_ID'       =>$user_id,
      'SIGN_TYPE'     =>'MD5',
      'REQ_ENCRYPTED' =>$util->encrypt($req,$this->config['KEY']),
    ];
    $a['SIGN'] = $util->getSign($a,$this->config['KEY']);

    addTestLog('his curl pre',$a,$req);
    $xml_str = $util->arr2xml($a,'ROOT',''); // curl xml
    $xml_str = str_replace(['<','>'], ['&lt;','&gt;'], $xml_str);
    $url = $this->config['URL'];
    $str = '<?xml version="1.0" encoding="utf-8"?><soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope"><soap12:Body><'.$fun[1].' xmlns="http://www.bsoft.com.cn"><xml>'.$xml_str.'</xml></'.$fun[1].'></soap12:Body></soap12:Envelope>';
    // addTestLog($url,$fun[1],$str);
    $ret = $util->postXmlCurl($str,$url);
    // $ret = $util->soap($fun[1],['xml'=>$xml_str],$this->config['URL']);
    $fun_str = $fun[1].'Result';
    $ret = preg_replace('/^(.*)<'.$fun_str.'>(.*)<\/'.$fun_str.'>(.*)$/','$2',$ret);
    $ret = str_replace(['&lt;','&gt;'],['<','>'],$ret);
    addTestLog('his curl ret',$a,$ret);

    // 返回解析 错误检查
    $ret = $this->parseSoapRet($ret);
    return $ret;
  }

  // 抛出 his异常cdoe(code:msg) or 系统异常code(msg)
  private function error($msg,$code,$hisErr=true){
    if($hisErr){
      throw new \Exception($code.':'.$msg,ErrorCode::HIS_ERROR);
    }else{
      throw new \Exception($msg,$code);
    }
  }
}