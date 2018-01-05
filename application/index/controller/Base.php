<?php
/**
 * User: rainbow
 * Date: api基类
 * 返回: json ; [code,data,msg,notify_id];
 * code = -1 : api异常 , data不加密为整个原始数据
 * code = 0  : api正确 , data为加密字符串 , 额外字段 time type sign
 * code > 0  : api错误 , data不加密一般为空
 */

namespace app\index\controller;

use ErrorCode as EC;
use DesCrypt;
use CryptUtils;
use src\client\ClientLogic;
use think\Controller;
use think\Exception;
use think\Response;
use think\Request;


/**
 * 接口基类
 * Class Base
 * 数据封装后itboye + client_id 传过来
 * @author rainbow <977746075@qq.com>
 * @package app\index\Controller
 */
abstract class Base extends Controller{

    protected $client_id     = "";
    protected $client_secret = "";
    protected $notify_id     = ""; //请求id

    // protected $allow_controller = ['token','file','alipayapp','alipayrefund','wxpayapp','alipaydirect','contract','test','repair'];

    /**
     * 构造函数
     */
    // public function __construct(){
    //     parent::__construct();
    public function initialize(){
      // header("X-Copyright:".POWER);

      // $controller = strtolower(request()->controller());
      // if(empty($controller) || ( !in_array($controller,$this->allow_controller) &&  $controller != "index" )){
      //     $this->returnErr(ErrorCode::Not_Found_Resource,"请求资源不存在!");
      // }

      $this->getClientID(); // req: client_id
      // index.php 专用
      if(method_exists($this,"_initialize")){
        $this->decodePost(); // req : itboye
        $this->_initialize();
      }
    }

    protected function suc($data=[],$msg='') {
      $msg  = $msg ? $msg : 'api success';
      $data = CryptUtils::encrypt($data);
      empty($this->notify_id) && $this->notify_id = 0;
      $param = [ // 多余参数为签名规则需要
        'code'          => 0,     // 表示请求接受正常
        'data'          => $data, // 业务成功数据 data加密
        'msg'           => $msg,  // 成功信息
        'client_secret' => $this->client_secret, // 签名用,下面unset
        'notify_id'     => $this->notify_id,     // 原请求id :app验证签名用
        'time'          => strval(time()),         // 返回时间 :app验证签名用
        'type'          => 'T',                  // app验证签名用
      ];
      $param['sign'] = CryptUtils::sign($param);//app验证签名用
      unset($param['client_secret']);
      ret($param);
    }

    // notify_id : 该请求的 请求id
    // time      : 服务器返回时的时间
    protected function err($msg='',$code=0,$data=[]) {
      $code = $code ? $code : EC::Business_Error;
      $msg = $msg ? $msg : 'api error out';
      empty($this->notify_id) && $this->notify_id = 0;
      ret(['code' => $code,'msg'=>$msg,'data' => $data,'notify_id'=>$this->notify_id,'time'=>strval(time())]);
    }

    protected function getClientID(){
        $client_id = $this->_param("client_id","");
        empty($client_id) && $this->err(Llack("client_id"),EC::Lack_Para);
        $_GET['client_id'] = $client_id;
        $this->client_id   = $client_id;
        unset($_POST['client_id']);
        $r = (new ClientLogic)->getInfo(['client_id'=>$this->client_id]);
        if(empty($r)){
            $this->err(Linvalid('Client_ID'),EC::Invalid_Para);
        }
        $this->client_secret = $r['client_secret'];
    }

    protected function decodePost(){
        $post = $this->_param('itboye','');
        empty($post) && $this->err(Llack('itboye'),EC::Lack_Para);
        $data = DesCrypt::decode(base64_decode($post),$this->client_secret);
        $data = $this->filter_post($data);
        $obj = json_decode($data,JSON_OBJECT_AS_ARRAY);
        $this->notify_id = $obj['notify_id'];

        $this->request->post($obj);
    }

    /**
     * 过滤末尾多余空白符 ASCII码等于7的奇怪符号
     * @param $post
     * @return string
     */
    protected function filter_post($post){
        $post = trim($post);
        for ($i=strlen($post)-1;$i>=0;$i--) {
            $ord = ord($post[$i]);
            if($ord > 31 && $ord != 127){
                $post = substr($post,0,$i+1);
                return $post;
            }
        }
        return $post;
    }


    /**
     * @param $key
     * @param string $default
     * @param string $nullMsg  未定义时的报错
     * @return mixed
     */
    public function _param($key,$default=null,$nullMsg=null){
        return $this->checkParamNull(input("param.".$key),$key,$default,$nullMsg);
    }
    public function _post($key,$default=null,$nullMsg=null){
        return $this->checkParamNull(input("post.".$key),$key,$default,$nullMsg);
    }
    public function _get($key,$default=null,$nullMsg=null){
        return $this->checkParamNull(input("get.".$key),$key,$default,$nullMsg);
    }
    public function checkParamNull($val,$key,$df,$nul){
        $name  = preg_replace('/\/\w$/', '', $key);
        if(is_null($val)){
            if(!is_null($nul)){
                $this->err($nul ?: Lack($name),EC::Lack_Para);
            }else{
                return $df;
            }
        }
        return $val;
    }


}