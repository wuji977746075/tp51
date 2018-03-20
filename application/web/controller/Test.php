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

  // code(0=>成功,其他失败),msg(失败信息),data
  public function json($data,$msg='',$code=0){
    return json(["code"=>$code,"msg"=>$msg,"data"=>$data]);
  }

  //IM :我的信息、好友列表、群组列表。
  public function ajaxImList($uid){
    $uid2 = 3 - $uid;
    $data = [
      "mine"=>[
         "id"       => $uid,
         "username" => "user_".$uid,
         "avatar"   => avaUrl($uid),
         "sign"     => "sign_".$uid.":在深邃的编码世界，做一枚轻盈的纸飞机",
         "status"   => "online", //online:在线、hide:隐身
      ],
      "friend"=>[[
        "groupname"=> "好友",
        "id"=> 1, //分组ID
        "list"=> [[
          "id"       => $uid2,
          "username" => "user_".$uid2,
          "avatar"   => avaUrl($uid2),
          "sign"     => "sign_".$uid2,
          "status"   => "online", //offline:离线，online或空:在线
        ]]
      ]],
      "group"=>[
        [
        "groupname"=> "客服",
        "id"=> 101,
        "avatar"=> "//tva1.sinaimg.cn/crop.0.0.200.200.50/006q8Q6bjw8f20zsdem2mj305k05kdfw.jpg"
        ], [
        "groupname"=>"前端",
        "id"=>102,
        "avatar"=>"//tva2.sinaimg.cn/crop.0.0.199.199.180/005Zseqhjw1eplix1brxxj305k05kjrf.jpg"
        ]
      ]
    ];
    return $this->json($data);
  }
  // IM:群员列表
  public function ajaxImMembers($id){
    $uid = 8;
    $data = [];
    $data['list'] = [];
    $data['list'][] = [
      "username" => "马小云",
      "id"       => $uid,
      "avatar"   => avaUrl($uid),
      "sign"     => "让天下没有难写的代码"
    ];
    return $this->json($data);
  }
  // 用户1
  public function im1(){
    return $this -> fetch();
  }
  // 用户2
  public function im2(){
    return $this -> fetch();
  }
  // 聊天处理
  public function imHandle(){
    Gateway::$registerAddress = '127.0.0.1:1238'; // 注册

    // GatewayClient支持GatewayWorker中的所有接口(Gateway::closeCurrentClient Gateway::sendToCurrentClient除外)
    // Gateway::sendToAll($this->msg('test'));
    // Gateway::isOnline($client_id);
    // Gateway::bindUid($client_id, $uid);
    // Gateway::isUidOnline($uid);
    $r = Gateway::getClientIdByUid($_GET['uid']);
    echo $r[0];
    Gateway::sendToClient($r[0], ["uid"=>8,"msg"=>'test - msg','type'=>'ping']);
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