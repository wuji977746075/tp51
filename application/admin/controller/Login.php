<?php
namespace app\admin\controller;

use src\user\user\UserLogic;
use src\user\role\UserRoleLogic;
// use think\facade\Session;

class Login extends Base{
  protected $model_id = 13;

  public function index(){
    if(IS_GET){ // admin login view
      $login_token = time();
      session('login_token',$login_token);
      $this->assign('token',$login_token);
      $auth_type = getConfig('admin_login_auth_type',0);
      session('auth_type',$auth_type);
      $this->assign('auth_type',$auth_type);
      return $this->show();
    }else{ // admin login
      // check
      $token = $this->_param('token','','非法访问.nt');
      $login_token = session('login_token');
      session('login_token',null); // only once
      if($login_token != $token){
        $this->error('非法访问.it');
      };
      $auth_type = session('auth_type');
      session('auth_type',null);   // only once
      if($auth_type == 'auth_code'){ // 验证码
        if(!captcha_check($this->_param('auth_code','','需要验证码'),'login')){
          $this->error('验证码错误或失效');
        }
      }elseif(in_array($auth_type,['','auth_slide'])){
      }else{
        $this->error('非法访问');
      }
      $uname = $this->_param('uname','','需要用户名');
      $upass = $this->_param('upass','','需要密码');
      // ? user : 出错跳转登陆
      try{
        $uinfo = (new UserLogic)->checkUser($uname,$upass);
        $uid   = $uinfo['id'];
        // ? 超级管理员
        $ur = new UserLogic;
        if(!$ur->isSuper($uid)){
          // todo : 查询用户角色id
          // $r = $ur->getRoleId($uid);
          // !$r['status'] && $this->error($r['info']);
          // $role_id = (int) $r['info'];

          // todo : 是否为此客户端角色
          // $r = (new AuthLogic)->checkRole($uid,CLIENT_ID);
          // !$r['status'] && $this->error($r['info']);

          // todo : 是否拥有登陆权限
          // $r = (new AuthLogic)->checkRoleRight($role_id,'user_login');
          // !$r['status'] && $this->error($r['info']);

        }
        // 登陆
        $sid = (new UserLogic)->login($uid,'','web',true);
      }catch(\Exception $e){
        $this->opErr($e->getCode().':'.$e->getMessage(),url('login/index'));
      }
      $this->redirect(url('manager/index'));
    }
  }
}