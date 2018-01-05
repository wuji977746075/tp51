<?php
/**
 * api 业务基类
 * 具体最好调用 logic/action
 */
namespace app\index\domain;

use ErrorCode as EC;
use src\base\helper\ValidateHelper;
use src\log\model\ApiHistory;
use think\Debug;
use think\exception\DbException;
use think\Response;
use src\session\SessionLogic;
use src\client\ClientLogic;
use src\role\RoleLogic;
/**
 * 基础领域模型
 * Class BaseDomain
 * @package app\src\domain
 */
class BaseDomain {

  protected $allowType     = ["json", "rss", "html"];
  protected $business_code = '';
  protected $data;
  protected $api_ver       = 100;//请求的api_ver
  protected $client_id;
  protected $client_secret;
  protected $domain_class;
  protected $lang;
  protected $notify_id;
  protected $time;  // api req : 客户端请求发起的时间
  protected $type;
  protected $nodes; // domain节点

  private function checkParam($arr){
    foreach ($arr as $v) {
      $name = preg_replace('/\/\w$/', '', $v);
      empty($this->data[$name]) && $this->err(EC::Lack_Para,Llack($name));
      $this->$name  = $this->data[$name];
    }
  }

  public function __construct($data) {
    debug('begin');
    $this->data = $data;
    $this->checkParam(['client_secret','notify_id/d','time/f','client_id','domain_class','api_ver/d','lang','type']);

    $this->nodes = require(APP_PATH.'/index/domain.php');
    // 授权检查
    $this->checkAuth();
  }

    // 权限检查
    // 根据配置 (type与节点的对应,每个都要有)
    // 检查客户端和用户是否有权限 和需要登陆
    private function checkAuth(){

      $client_id = $this->client_id;
      $domain    = lcfirst($this->data['domain']);
      $action    = $this->data['action'];
      $sid       = $this->data['sid'];

      !isset($this->nodes[$domain]) && $this->err('api不存在:'.$this->domain_class);
      $nodes = $this->nodes[$domain];
      !isset($nodes[$action]) && $this->err('api不存在:'.$this->domain_class);

      $nodes = $nodes[$action];
      $check_api = isset($nodes[0]) ? $nodes[0] :'';
      $tmp = explode('_', $check_api);
      if($check_api && count($tmp) == 1){
        $check_api = $domain.'_'.$check_api;
      }
      $check_login = isset($nodes[1]) ? (bool)$nodes[1] : false;

      // 是否session 登陆
      $info = $sid ? (new SessionLogic)->getInfo(['session_id'=>$sid]) : [];
      $uid  = $info ? $info['uid'] : 0;
      // 检查登陆
      if($check_login && $uid<1) $this->err('请先登陆');

      // 检查客户端和角色 api权限
      if($check_api){ // 若配置了 api权限
        // 检查客户端 api权限
        !((new ClientLogic)->checkAuth($client_id,$check_api)) && $this->err('该客户端无此权限',EC::Api_No_Auth);
        if($uid){ // 登录时 检查用户角色 api权限
          !((new RoleLogic)->checkApiAuth($uid,$client_id,$check_api)) && $this->err('该用户无此权限',EC::Api_No_Auth);
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
        $this->err($msg,EC::Api_Need_Update);
      }
      return  $this->api_ver;
    }

    // notify_id : 该请求的 请求id
    // time      : 服务器返回时的时间
    protected function err($msg='',$code=0,$data=[]) {
      $code = $code ? $code : EC::Business_Error;
      $msg = $msg ? $msg : 'api error in';
      empty($this->notify_id) && $this->notify_id = 0;
      ret(['code' => $code,'msg'=>$msg,'data' => $data,'notify_id'=>$this->notify_id,'time'=>strval(time())]);
    }

    /**
     * 仅适用 index模块
     * @param $key
     * @param string $default
     * @param string $emptyErrMsg 是否
     * @return mixed
     */
    public function _post($key, $default = '', $emptyErrMsg = false) {

        $value = isset($this->data["_data_" . $key]) ? $this->data["_data_" . $key] : $default;

        if ($default === $value){
            if($emptyErrMsg) {
                $emptyErrMsg = Llack($key);
                $this->apiErr($emptyErrMsg, EC::Lack_Parameter);
            }
        }

        $value = $this->escapeEmoji($value);

        if ($default == $value && !empty($emptyErrMsg)) {
            $emptyErrMsg = Llack($key);
            $this->apiErr($emptyErrMsg, EC::Lack_Parameter);
        }

        return $value;
    }

    /**
     * @param $key
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
     * @param    [type]                   $str  [description]
     * @param    boolean                  $need [description]
     * @return   [type]                         [description]
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
                $this->apiErr(Llack($p[0]), EC::Lack_Parameter);
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
                        $this->apiErr(Linvalid($p[0]), EC::Invalid_Parameter);
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