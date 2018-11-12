<?php
namespace app\admin\controller;

use src\user\UserLogic;
use src\role\UserRoleLogic;

class Login extends Base{

  public function index(){
    if(IS_GET){ // admin login view
      return $this->show();
    }else{ // admin login

      $uname = $this->_param('uname','','需要用户名');
      $upass = $this->_param('upass','','需要密码');
      $d_token = $this->_param('d_token','');
      $d_type  = $this->_param('d_type','');
      // ? user
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
        $sid = (new UserLogic)->login($uid,$d_token,$d_type,true);
      }catch(\Exception $e){
        $this->opErr($e->getCode().':'.$e->getMessage(),url('login/index'));
      }
      $this->redirect(url('manager/index'));
    }
  }
}