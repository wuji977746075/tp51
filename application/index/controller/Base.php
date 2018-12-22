<?php
/**
 * User: rainbow
 * Date: api基类
 */
namespace app\index\controller;
use \ErrorCode as EC;
use src\base\traits\Jump;
use src\base\traits\Post;

use \DesCrypt;
use src\sys\client\SysClientLogic as ClientLogic;
use think\Controller;
/**
 * 接口基类
 * Class Base
 * 请求:
 *   数据封装后itboye + client_id 传过来
 * 返回:
 *   错误信息字符串(2处) or 数组的json字符串[code,data,msg,notify_id,time,type](1处);
 * @author rainbow <977746075@qq.com>
 * @package app\index\Controller
 */
abstract class Base extends Controller{
  // protected $allow_controller = ['test'];
  use Jump,Post;
  const ERROR = EC::ERROR_API;
  public function initialize(){
    try{
      // $controller = strtolower(request()->controller());
      // if(empty($controller) || ( !in_array($controller,$this->allow_controller) &&  $controller != "index" )){
      //    $this->err("请求资源不存在!",EC::NOT_FOUND_RESOURCE);
      // }
      $this->getClientID(); // req: client_id
      // index.php 专用
      if(method_exists($this,"_init")){
        $this->decodePost(); // req : itboye
        $this->_init();
      }else{
        $this->err('need implements _init()');
      }
    }catch(\Exception $e){
      $this->ret($e->getCode(),$e->getMessage(),$e->getTrace());exit;
    }
  }
  // die json
  protected function ret($code=-1,$msg='error',$data=[]) {
    err($msg,$code,$data);
  }
  // 外层client_id
  protected function getClientID(){
    $cl = new ClientLogic();
    $client_id = $this->_param('client_id','');
    empty($client_id) && $this->err(Llack('client_id'),EC::LACK_PARA);
    define('CLIENT_ID_REQ',$client_id);
    $client_secret = $cl->getField(['client_id'=>CLIENT_ID_REQ],'client_secret');
    empty($client_secret) && $this->err(Linvalid('client_id'));
    define('CLIENT_SECRET_REQ',$client_secret);
  }
  // 外层itboye
  protected function decodePost(){
    $post = $this->_param('itboye','');
    empty($post) && $this->err(Llack('itboye'),EC::LACK_PARA);
    $data = DesCrypt::decode($post,CLIENT_SECRET_REQ);
    $data = $this->filter_post($data);
    $obj = json_decode($data,JSON_OBJECT_AS_ARRAY);
    addTestLog('itboye',$post,$obj);
    $this->request->withPost($obj);
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

}