<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2016-12-06 14:18:56
 * Description : [语法 或 功能 测试]
 */

namespace app\web\controller;

use src\post\logic\PostLogic;
use src\user\action\LoginAction;
use src\post\model\Post;
use think\Controller;

use src\client\ClientLogic;
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