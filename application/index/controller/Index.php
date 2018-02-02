<?php

// 出错返回 json [code,msg,data]
namespace app\index\controller;

use src\base\helper\ExceptionHelper;
use ErrorCode as EC;
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

  protected function _initialize(){ // init
    addLog("_initialize",$_GET,$_POST,"[接口初始化]");
    $this->_initParameter();
    $this->_check();
    CacheUtils::initAppConfig();
  }

  public function index() { // api接口请求入口

    try{
      $pre = substr($this->type,0,3);
      if($pre == 'BY_') $pre='Domain';
      //已登录会话ID
      $login_s_id = $this->getLoginSId();

      $type = preg_replace("/_/","/",substr(ltrim($this->type),3),1);
      $type = preg_split("/\//",$type);
      count($type)<2 && $this->err(Linvalid("type"),EC::Invalid_Para);

      $action_name     = $type[1];
      $controller_name = $type[0];
      $domainClass = $controller_name.$pre.'/'.$action_name;

      $this->decrypt_data['domain_class'] = $domainClass;
      $this->decrypt_data['sid']          = $login_s_id;
      $this->decrypt_data['domain']       = $controller_name;
      $this->decrypt_data['action']       = $action_name;

      $cls_name = "\\app\\index\\domain\\".$controller_name.$pre;
      if(!class_exists($cls_name,true)){
          $this->err($cls_name,EC::Not_Found_Resource);
      }
      // halt($this->decrypt_data);
      $class = new $cls_name($this->decrypt_data);
      if(!method_exists($class,$action_name)){
        $this->err($cls_name.'->'.$action_name,EC::Not_Found_Resource);
      }

      // addLog('',$cls_name.$action_name,$this->decrypt_data,'',true);
      //4. 调用方法
      $r = $class->$action_name();
      addLog($domainClass,$r,$_POST,"[接口调用结果]");
      $this->suc($r,'api success');

      //5. 这一步不会走到
      $this->err("无法处理!");
    }catch (\Exception $e) { // 返回错误信息
      // addDebugLog('[接口异常]'.$domainClass,$e->getCode().':'.$e->getMessage(),$_POST);
      $this->err($e->getMessage(),$e->getCode());
      // $this->err($e->getTrace(),$e->getCode());
    }

  }

  private function checkParam($arr){
    foreach ($arr as $v) {
      $name  = preg_replace('/\/\w$/', '', $v);
      empty($this->_post($v)) && $this->retErr(Llack($name));
      $this->$name = $this->_post($v);
      unset($_POST[$name]);
    }
  }
  /**
   * 初始化公共参数
   */
  private function _initParameter(){
      $this->checkParam(['time/f','sign','data','type','notify_id/d','api_ver/d','app_version','app_type','lang']);
  }

  /**
   * 解密验证
   */
  private function _check(){

    //1. 请求时间戳校验
    $now = microtime(true);

    //时间误差 +- 1分钟
    if($now - 60 > $this->time || $this->time > $now + 60){
        $this->err("该请求已失效!");
    }
    //2. 签名校验
    $param = [
        'client_secret' =>$this->client_secret,
        'notify_id'     =>$this->notify_id,
        'time'          =>$this->time,
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
        $this->decrypt_data['client_id']   = $this->client_id;
        $this->decrypt_data['app_version'] = $this->app_version;
        $this->decrypt_data['app_type']    = $this->app_type;
        $this->decrypt_data['lang']        = $this->lang;

        $data = CryptUtils::decrypt($this->data);
        foreach($data as $key=>$vo){
            $this->decrypt_data['_data_'.$key] = $vo;
        }
    }catch (Exception $e){
        $this->err($e->getMessage());
    }

  }

  /**
   * 获取登录会话id
   * @return string
   */
  private function getLoginSId(){
    $login_s_id = $this->_get('s_id','');
    if(empty($login_s_id)){
      $login_s_id = isset($this->decrypt_data['_data_s_id'])?($this->decrypt_data['_data_s_id']):"";
    }

    return $login_s_id;
  }

}
