<?php
/**
 * api 业务基类
 * 具体最好调用 logic/action
 */
namespace app\index\domain;

use ErrorCode as EC;
use src\base\exception\ApiException;


use src\base\helper\ValidateHelper;
use src\log\model\ApiHistory;
use think\Debug;
use think\exception\DbException;
use think\Response;
use src\sys\session\SessionLogic;
use src\sys\client\ClientLogic;
use src\sys\role\RoleLogic;
use \DesCrypt;
/**
 * 基础领域模型
 * Class BaseDomain
 * @package app\src\domain
 */
abstract class BaseDomain {

  protected $allowType     = ["json", "rss", "html"];
  protected $business_code = '';
  protected $data;
  protected $api_ver       = 100;//请求的api_ver
  protected $domain_class;
  protected $lang;
  protected $time;  // api req : 客户端请求发起的时间
  protected $type;
  protected $unCheckApi = ['UserDomain/login']; //不检查令牌的接口
  protected $cacheTime  = 1800; //令牌缓存时间

  public function __construct($data) {
    debug('begin');
    $this->data = $data;
    $temp = $this->checkParam('time,domain_class,api_ver,type','lang,app_version,app_type');
    $sid = $this->data['sid'];
    if(!in_array($this->domain_class,$this->unCheckApi)){
      empty($sid) && $this->err($this->domain_class.Llack('sid'));
      $uid = $this->checkToken($sid);
      define('UID',$uid);
      // 如果没传uid则补全
      if(!isset($this->data['_data_uid'])) $this->data['_data_uid'] = $uid;
      // sid 顺延
      $this->cacheToken($sid,time()+$this->cacheTime);
    }
    if(method_exists($this,'_init')){
      $this->_init();
    }
  }

  // 检查登录 token
  protected function checkToken($sid){
    $exp = $this->cacheToken($sid);
    empty($exp) && $this->err('认证失败,请重新登录',EC::API_NEED_LOGIN);
    // 一般已经没有缓存了
    $exp<time() && $this->err('认证失效,请重新登录',EC::API_NEED_LOGIN);
    $uid = base64_decode((new DesCrypt)->decode($sid,CLIENT_SECRET_REQ));
    $uid = substr($uid,10);
    if(!is_numeric($uid) || $uid<1) $this->err('认证失败,请重新登录.'.$uid,EC::API_NEED_LOGIN);
    return (int) $uid;
  }

  // 暂时使用缓存保存 token
  // todo : redis
  protected function cacheToken($s,$v=null,$e=null){
    $e = $e === null ? $this->cacheTime : max(intval($e),0);
    if($v){
      cache('sid-'.$s,$v,$e);

      // $sids = cache('sids');
      // $sids = $sids ? $sids : [];
      // $sids['sid-'.$s] = $v;
      // cache('sids',$sids);
    }else{
      return cache('sid-'.$s);
    };
  }
  // 锁登录标志 token //30min 有效
  protected function buildToken($uid,$e=null){
    $e = $e === null ? $this->cacheTime : max(intval($e),0);
    (!is_numeric($uid) || $uid<1) && $this->err(Linvalid('uid:'.$uid));
    // app 无 session_id()
    $exp = time() + $e; // 创建时间
    $sid = (new DesCrypt)->encode(base64_encode($exp.$uid),CLIENT_SECRET_REQ);
    $this->cacheToken($sid,$exp,$e);
    return $sid;
  }

  // 最外层参数 未加 _data_
  private function checkParam($need,$unneed=''){
    $arr = explode(',',$need);
    foreach ($arr as $v) {
      $name  = preg_replace('/\/\w$/', '', $v);
      $this->$name = $this->data[$v];
      empty($this->data[$v]) && $this->err(Llack($name));
    }
    // 下面才有err/suc
    if($unneed){
      $arr = explode(',',$unneed);
      foreach ($arr as $v) {
        $name  = preg_replace('/\/\w$/', '', $v);
        $this->$name = $this->data[$v];
      }
    }
  }
  /**
   * 服务端允许的api版本/列表
   * @param string|array $vers 许可的api_vers
   * @param string $msg  string [更新的说明]
   * @internal param $ [int|array]     $vers
   */
  protected function checkVersion($vers = 100,$msg='') {
    if(!is_array($vers)) $vers = [intval($vers)];
    // 是否存在 等值字符串/数字
    if(!in_array($this->api_ver,$vers)) {
      $msg = lang('tip_update_api_version',['version'=> implode(',', $vers)]);
      $this->err($msg,EC::API_NEED_UPDATE);
    }
    return  $this->api_ver;
  }

  protected function suc($data=[],$msg='') {
    $msg  = $msg!=='' ? $msg : 'success';
    throw new ApiException($msg,0,$data);
  }
  protected function err($msg='',$code=0,$data=[]) {
    $code = $code ? $code : EC::BUSINESS_ERROR;
    $msg = $msg!=='' ? $msg : 'error';
    throw new ApiException($msg,$code,$data);
  }

  /**
   * @title 仅适用 index模块
   * 兼容/模式 2018-07-26 13:53:47
   * @param string $key
   * @param string $default
   * @param string $emptyErr 是否
   * @return mixed
   */
  public function _post($key, $default = '', $emptyErr = false) {
    $key = explode('/',$key);
    $key_type = isset($key[1]) ? $key[1] : 's'; // string
    $key = $key[0]; // key has change
    $key_data = "_data_" . $key ;
    $v = isset($this->data[$key_data]) ? trim($this->data[$key_data]) : '';
    if($key_type == 's'){
      $v = $v ? $this->escapeEmoji($v) : '';
      empty($v) && $emptyErr && $this->err(Llack($key), EC::LACK_PARA);
    }elseif($key_type == 'f'){
      $v = floatval( $v );
    }elseif($key_type == 'd'){
      $v = (int) $v;
    }
    return $v;
  }

  /**
   * @param string $key
   * @param string $default
   * @param string $emptyErrMsg 为空时的报错
   * @return mixed
   */
  public function _get($key, $default = '', $emptyErrMsg = '') {
    $this->_post($key,$default,$emptyErrMsg);
  }

  /**
   * 放到utils中，作为工具类
   * @brief 干掉emoji
   * @autho chenjinya@baidu.com
   * @param {String} $strText
   * @return {String} removeEmoji
   **/
  protected function escapeEmoji($strText, $bool = false) {
    $preg = '/\\\ud([8-9a-f][0-9a-z]{2})/i';
    if ($bool == true) {
      $boolPregRes = (preg_match($preg, json_encode($strText, true)));
      return $boolPregRes;
    } else {
      $strPregRes = (preg_replace($preg, '', json_encode($strText, true)));
      $strRet = json_decode($strPregRes, JSON_OBJECT_AS_ARRAY);

      if ( is_string($strRet) && strlen($strRet) == 0) {
        return "";
      }

      return $strRet;
    }
  }

  /**
   * 获取post参数并返回
   * $need:是否必选
   * a|0|int   默认0
   * a         默认''
   * a|p       默认'p'
   * a||mulint 数字','链接字符串
   * a||float  小数
   * @DateTime 2016-12-13T10:25:17+0800
   * @param    string  $str  [description]
   * @param    boolean $need [description]
   * @return   array   [description]
   */
  protected function getPost($str,$need=false){
    if(empty($str) || !is_string($str)) return [];
    $r = [];
    $arr = explode(',', $str);
    $data = $this->data;
    foreach ($arr as $v) {
      //补全预定义
      $p = explode('|', $v);
      !isset($p[1]) && $p[1]='';   //默认值空字符串
      !isset($p[2]) && $p[2]='str';//默认类型字符串
      $key = '_data_'.$p[0];
      //_post number bug
      // if($need) $post = $this->_post($p[0],$p[1],Llack($p[0]));
      // else  $post = $this->_post($p[0],$p[1]);
      // fix bug
      !isset($data[$key]) && $data[$key]='';
      if($need && $data[$key] === ''){
        $this->err(Llack($p[0]), EC::LACK_PARA);
      }
      $post = $data[$key]==='' ? $p[1] : $data[$key];
      if($p[2] === 'int'){
        $post = intval($post);
      }elseif($p[2] === 'float'){
        $post = floatval($post);
      }elseif($p[2] === 'mulint'){
        $post = array_unique(explode(',', $post));
        $temp = [];
        foreach ($post as $v) {
          if(is_numeric($v)){
            $temp[] = $v;
          }else{
            $this->err(Linvalid($p[0]), EC::INVALID_PARA);
          }
        }
        $post = implode(',', $temp);
      }
      $r[$p[0]] = $post;
    }
    return $r;
  }
  /**
   * 合并必选和可选post参数并返回
   * $str: 需要检查的post参数
   * $oth_str: 不需检查的post参数
   */
  protected function parsePost($str='',$oth_str=''){
    return array_merge($this->getPost($str,true),$this->getPost($oth_str,false));
  }

  /**
   * 获取原始数据
   * @return array
   */
  protected function getOriginData(){
    $tmp = [];
    foreach ($this->data as $key=>$vo){
      $k = str_replace("_data_","",$key);
      $tmp[$k] = $vo;
    }
    return $tmp;
  }

}