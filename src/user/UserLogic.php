<?php

namespace src\user;
use src\base\BaseLogic;
use src\session\SessionLogic;

class UserLogic extends BaseLogic{

  private $super_ids = null;

  public function init(){
    $this->super_ids = [1]; //todo : getConfig
  }

  public function login($uid,$token='',$type='',$isAdmin=false){
    // add new session and logout the oldest
    $r = (new SessionLogic)->add($uid,$token,$type);
    if(!$r['status']) return $r;
    $sid = $r['info'];
    $isAdmin && session(SessionLogic::ADMIN_KEY,$uid);
    return returnSuc($sid);
  }

  public function logout(){

  }

  public function isSuperAdmin($uid){
    return in_array($uid, $this->super_ids);
  }

  public function getAll($uid){
    $model = $this->getModel();
    $r = $model->get($uid);
    $r->extra;
    return $r->toArray();
  }

  // apiReturn
  public function checkUser($name='',$pass=''){
    $r = $this->getInfo(['name'=>$name]);
    if(empty($r)) return returnErr('无此用户');
    if(!$this->checkPass($r['pass'],$pass)) return returnErr('密码错误');
    return returnSuc($r);
  }

  public function checkPass($pass,$check){
    return $pass == $this->getPass($check);
  }

  public function getPass($pass,$salt=''){
    $salt = $salt ? $salt : 'rainbow';
    return md5($pass.$salt);
  }

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