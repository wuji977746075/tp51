<?php
/**
 * Created by PhpStorm.
 * User: 64
 * Date: 2017/3/23 0023
 * Time: 14:26
 */
namespace app\weixin\controller;
use app\src\user\action\LoginAction;
use app\src\user\logic\UcenterMemberLogic;
use think\Db;
use app\wxapp\controller\BaseController;
class Login extends  Base{

    public function mobile_login(){

        return $this->fetch('login/MoblieLogin');
    }

    public function login(){
        $mobile=input('mobile');
        $password=input('password');

        if(empty($mobile)) $this->error('手机号不能为空');

        $validate=(new UcenterMemberLogic())->getInfo(['mobile'=>$mobile]);

        if($validate['status']){
            if(!empty($validate['info'])){
                $uid=$validate['info']['id'];
            }
        }
        if(!empty($uid)){
            $login=(new LoginAction())->loginByUID($uid,'wxwep','','');
            if ($login['status'] && is_array($login['info'])) {
                $user_Info = $login['info'];
                //$userInfo['wxapp_session_key'] = $session_key;
                //登录成功
                session('memberinfo', $user_Info);
                $this->success('登录成功','index/index');
            }
        }else{
            $this->error('登录错误，请重新登录');
        }
    }


}