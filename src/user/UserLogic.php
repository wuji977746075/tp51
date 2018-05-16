<?php

namespace src\user;
use src\base\BaseLogic;
use src\session\SessionLogic;

class UserLogic extends BaseLogic{
  const SUPER_USERS = [1];

  // return
  public function login($uid,$token='',$type='',$isAdmin=false){
    // add new session and logout the oldest
    $sid = (new SessionLogic)->add($uid,$token,$type);
    $isAdmin && session(SessionLogic::ADMIN_KEY,$uid);
    return $sid;
  }

  public function logout(){

  }
  // return
  public function isSuperUser($uid){
    return in_array($uid, self::SUPER_USERS);
  }

  // return
  public function getAllInfo($uid){
    $model = $this->getModel();
    $r = $model->get($uid);
    $r->extra;
    return $r->toArray();
  }

  // return
  // throws
  public function checkUser($name='',$pass=''){
    $r = $this->getInfo(['name'=>$name]);
    empty($r) && throws('无此用户');
    !$this->checkPass($r['pass'],$pass) && throws('密码错误');
    return $r;
  }
  // return
  public function checkPass($pass,$check){
    return $pass == $this->getPass($check);
  }
  //return
  public function getPass($pass,$salt=''){
    $salt = $salt ? $salt : 'rainbow';
    return md5($pass.$salt);
  }
  // return
  // throws
  public function queryCountWithRole($map = null, $page = false, $order = false, $params = false, $fields = false) {
    empty($page) && $page = ['page'=>1,'size'=>10];
    empty($fields) && $fields = 'u.*';
    $model = $this->getModel();

    $query = $model ->alias('u');
    $query = $query->where($map)->order($order)->field($fields);
    $query = $query->join('user_role ur','ur.uid=u.id','left');
    $query = $query->join('sys_role r','r.id=ur.role_id','left');
    // $query = $query->join('user_extra e','e.uid=u.id','left');

    $start = max(0,(intval($page['page'])-1)*intval($page['size']));
    $list  = $query -> limit($start,$page['size']) -> select();

    $count = $model ->alias('u')-> where($map)->join('user_role ur','ur.uid=u.id','left') -> count();
    return ["count" => $count, "list" => $list->toArray()];

  }
}