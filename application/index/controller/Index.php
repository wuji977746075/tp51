<?php

// 出错返回 json [code,msg,data]
namespace app\index\controller;

use src\base\exception\ApiException;
use \ErrorCode as EC;
use CacheUtils;
use CryptUtils;
class Index extends Base {

  protected $time;       //请求的时间戳,float,返回的请手动加
  protected $notify_id;  //请求的id,返回统一
  private $data;         //加密过的数据
  private $type;         //业务类型
  private $sign;         //签名
  private $api_ver;      //当前接口的版本，数字从100开始计数
  private $app_version;  //当前软件的版本
  private $app_type;     //当前软件的类型 ，ios,android,pc,test,admin,web

  private $decrypt_data; //解密过的数据

  protected function _init(){ // init
    // addLog("_init",$_GET,$_POST,"[接口初始化]");
    // addTestLog("_init",$_GET,$_POST);
    $this->_initParameter();
    $this->_check();
    CacheUtils::initAppConfig();
  }

  public function index() { // api接口请求入口
    try{
      $pre = strtolower(substr($this->type,0,3));
      if($pre == 'by_') $pre='Domain';
      $type = preg_replace("/_/","/",substr(ltrim($this->type),3),1);
      //已登录会话ID
      $sid = $this->getLoginSid($type);
      $type = preg_split("/\//",$type);
      count($type)<2 && $this->err(Linvalid("type"),EC::INVALID_PARA);

      $action_name     = $type[1];
      $controller_name = $type[0];
      $domainClass = $controller_name.$pre.'/'.$action_name;

      $this->decrypt_data['domain_class'] = $domainClass;
      $this->decrypt_data['sid']          = $sid;
      $this->decrypt_data['domain']       = $controller_name;
      $this->decrypt_data['action']       = $action_name;

      $cls_name = "\\app\\index\\domain\\".$controller_name.$pre;
      if(!class_exists($cls_name,true)){
          $this->err('未知class:'.$cls_name,EC::NOT_FOUND_RESOURCE);
      }

      //4. 调用方法
      // halt($this->decrypt_data);
      $class = new $cls_name($this->decrypt_data);
      if(!method_exists($class,$action_name)){
        $this->err($cls_name.'->'.$action_name,EC::NOT_FOUND_RESOURCE);
      }
      // addLog('',$cls_name.$action_name,$this->decrypt_data,'',true);
      $r = $class->$action_name();
      //4. 调用方法 - 反射
      // $reflectionMethod = new \ReflectionMethod($cls_name, $action_name);
      // $r = $reflectionMethod->invokeArgs(new $cls_name($this->decrypt_data), []);

      addLog($domainClass,$r,$_POST,"[接口调用结果]");
      $this->suc($r);

      //5. 这一步不会走到
      $this->err("无法处理!");
    }catch (\Exception $e) { // 返回错误信息
      if($e instanceof ApiException){
        echo $e; // toString
      }else{
        $this->ret($e->getCode(),$e->getMessage(),$e->getTrace());
      };
    }
    exit;
  }
  /**
   * 初始化公共参数
   */
  private function _initParameter(){
    $this->checkParam('time/f,sign,data,type,notify_id/d,api_ver/d','app_version,app_type,lang');
  }
  private function checkParam($need,$unneed=''){
    $arr = explode(',',$need);
    foreach ($arr as $v) {
      $name  = preg_replace('/\/\w$/', '', $v);
      if($name == 'data') {
        $this->$name = input('post.'.$name,'','');
      }else{
        $this->$name = $this->_post($v);
      }
      empty($this->_post($v)) && $this->err(Llack($name));
      unset($_POST[$name]);
    }
    define('NOTIFY_ID',$this->notify_id);
    // 下面才有err/suc
    if($unneed){
      $arr = explode(',',$unneed);
      foreach ($arr as $v) {
        $name  = preg_replace('/\/\w$/', '', $v);
        $this->$name = $this->_post($v);
        unset($_POST[$name]);
      }
      $this->lang = $this->lang ? $this->lang : 'zh-cn';
    }
  }
  /**
   * 解密验证
   */
  private function _check(){
    //1. 请求时间戳校验
    $now = microtime(true);
    //时间误差 +- 1分钟
    // if($now - 60 > $this->time || $this->time > $now + 60){
    //   $this->err("该请求已失效!");
    // }
    //2. 签名校验
    $param = [
      'client_secret' =>CLIENT_SECRET_REQ,
      'notify_id'     =>NOTIFY_ID,
      'time'          =>intval($this->time),
      'data'          =>$this->data,
      'type'          =>$this->type,
    ];
    try{
      if(!CryptUtils::verify_sign($this->sign,$param)){
        $this->err("签名验证错误!");
      }
      //3. 数据解密
      $this->decrypt_data = $param;
      $this->decrypt_data['api_ver']     = $this->api_ver;
      $this->decrypt_data['client_id']   = CLIENT_ID_REQ;
      $this->decrypt_data['app_version'] = $this->app_version;
      $this->decrypt_data['app_type']    = $this->app_type;
      $this->decrypt_data['lang']        = $this->lang;

      $data = CryptUtils::decrypt($this->data);
      foreach($data as $key=>$vo){
        $this->decrypt_data['_data_'.$key] = $vo;
      }
    }catch (\Exception $e){
      $this->err($e->getMessage());
    }

  }

  /**
   * 获取登录会话id
   * @return string
   */
  private function getLoginSid($type){
    $sid = $this->_get('sid','');
    if(empty($sid)){
      $sid = isset($this->decrypt_data['_data_sid']) ? ($this->decrypt_data['_data_sid']) : "";
    }
    return $sid;
  }
}
