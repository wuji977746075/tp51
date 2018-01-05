<?php
namespace app\admin\controller;

use src\user\UserLogic;

class Login extends Base{

  public function index(){

    if(IS_GET){ // admin login view
      return $this->show();
    }else{ // admin login
      $uname = $this->_param('uname','','需要用户名');
      $upass = $this->_param('upass','','需要密码');
      $d_token = $this->_param('d_token','');
      $d_type  = $this->_param('d_type','');
      $client_id = 'by571846d03009e1'; // admin端
      // ? user
      $r = (new UserLogic)->checkUser($uname,$upass);
      !$r['status'] && $this->error($r['info']);
      $uinfo = $r['info'];
      $uid   = $uinfo['id'];

      // todo : 查询用户角色id
      // $r = (new RoleLogic)->getRoleId($uid);
      // !$r['status'] && $this->error($r['info']);
      // $role_id = (int) $r['info'];

      // todo : 是否为此客户端角色
      // $r = (new AuthLogic)->checkRole($uid,$client_id);
      // !$r['status'] && $this->error($r['info']);

      // todo : 是否拥有登陆权限
      // $r = (new AuthLogic)->checkRoleRight($role_id,'user_login');
      // !$r['status'] && $this->error($r['info']);

      // 登陆
      $r = (new UserLogic)->login($uid,$d_token,$d_type,true);
      !$r['status'] && $this->error($r['info']);

      $this->redirect(url('manager/index'));
    }
  }
}