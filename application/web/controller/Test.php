<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2016-12-06 14:18:56
 * Description : [语法 或 功能 测试]
 */

namespace app\web\controller;
use think\Controller;

// use src\post\logic\PostLogic;
// use src\user\action\LoginAction;
// use src\post\model\Post;
// use src\client\ClientLogic;
use GatewayClient\Gateway;
/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\
 * @example
 */
class Test extends Controller{
  public function msg($data){
    return json_encode(["code"=>0,"msg"=>"","data"=>$data]);
  }
  public function im(){

    return $this -> fetch();
  }

  public function imHandle(){
    Gateway::$registerAddress = '127.0.0.1:1238'; // 注册

    // GatewayClient支持GatewayWorker中的所有接口(Gateway::closeCurrentClient Gateway::sendToCurrentClient除外)
    // Gateway::sendToAll($this->msg('test'));
    // Gateway::isOnline($client_id);
    // Gateway::bindUid($client_id, $uid);
    // Gateway::isUidOnline($uid);
    $r = Gateway::getClientIdByUid($_GET['uid']);
    echo $r[0];
    // Gateway::sendToClient($r[0], ["uid"=>0,"msg"=>'msg','type'=>'ping']);
    // dump($r);dump($_SESSION['uid']);
    // Gateway::closeClient($r[0]);
    // Gateway::unbindUid($client_id, $uid);
    // Gateway::sendToUid($uid, $dat);
    // Gateway::joinGroup($client_id, $group);
    // Gateway::sendToGroup($group, $data);
    // Gateway::leaveGroup($client_id, $group);
    // Gateway::getClientCountByGroup($group);
    // Gateway::getClientSessionsByGroup($group);
    // Gateway::getAllClientCount();
    // Gateway::getAllClientSessions();
    // Gateway::setSession($client_id, $session);
    // Gateway::updateSession($client_id, $session);
    // Gateway::getSession($client_id);
  }

  public function index(){
    $r = (new ClientLogic)->getAuthByClientID('ddd');
  }

  public function faker(){
    // $faker = \Faker\Factory::create('zh_CN');
    // echo $faker->lastName;
    // halt('here');
  }

  public function temp(){
    // $result = (new PostLogic())->queryNoPaging();
    // halt($result);
    // $action = new LoginAction();
    // $result = $action->loginByCode('17195862186','554523','330100','pppp','ios','alg');
    // halt($result);
  }


  //单文件上传 -web
  public function upload(){
  //  if(IS_AJAX){
  //    $data = array(
  //       'username' => I('post.username',''),
  //       "password" => I('post.password',''),
  //       'api_ver'  =>$this->api_ver,
  //       'notify_id'=>$this->notify_id,
  //       'alg'       =>$this->alg,
  //       'type'    =>'BY_User_login',
  //     );

  //     $service = new BoyeService();
  //     $result = $service->callRemote("",$data,true);
  //     echo $this->parseResult($result);
  //   }else{
      $this->assign('type','单/多文件上传');
      // $this->assign('client_id',CLIENT_ID);
      $this->assign('post_url',config('upload_path').'/file/upload?client_id=');//.CLIENT_ID
      $this->assign('field',[
        ['uid','43',LL('need-mark user ID')],
        ['type','avatar',LL('need-mark file-type')],
      ]);
      return $this -> fetch();
  // }
  }
}