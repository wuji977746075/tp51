<?php
/**
 * api 业务基类
 * 具体最好调用 logic/action
 */
namespace app\index\domain;
use \ErrorCode as EC;
use src\base\traits\Jump;
use src\base\traits\PostData;

use \DesCrypt;
/**
 * 基础领域模型
 * Class BaseDomain6
 * @package app\src\domain
 */
abstract class BaseDomain {
  use Jump,PostData;
  const ERROR = EC::ERROR_DOMAIN;
  protected $allowType     = ["json", "rss", "html"];
  protected $data;
  protected $api_ver       = 100;//请求的api_ver
  protected $domain_class;
  protected $lang;
  protected $time;  // api req : 客户端请求发起的时间
  protected $type;
  protected $unCheckApi = ['UserDomain/token','UserDomain/login']; //不检查令牌的接口
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
  protected function checkVersion($vers =[100],$msg='') {
    if(!is_array($vers)) $vers = [intval($vers)];
    // 是否存在 等值字符串/数字
    if(!in_array($this->api_ver,$vers)) {
      $msg = lang('tip_update_api_version',['version'=>end($vers)]);
      $this->err($msg,EC::API_NEED_UPDATE);
    }
    return  $this->api_ver;
  }
}