<?php

namespace src\user;
use src\base\BaseLogic;
use src\session\SessionLogic;
use src\role\UserRoleLogic;
use src\role\RoleLogic;
use src\user\UserExtraLogic;
use Check;

class UserLogic extends BaseLogic{
  const SUPER_USERS = [1];

  // return
  // todo : redis ...
  function login($uid,$device_token='',$device_type='',$isAdmin=false){
    // add new session and logout the oldest
    $sid = (new SessionLogic)->add($uid,$device_token,$device_type); // api
    // define('UID',$uid); //常量不可改
    // 如果是后台 则写一个 session
    $isAdmin && session(SessionLogic::ADMIN_KEY,$uid);
    return $sid;
  }

  function logout(){

  }

  // 添加用户信息 扩展信息 和 角色
  function addUser($para,$role){
    $this->trans();
    $name = $para['name'];
    $pass = $para['pass'];
    $this->isValidName($name);
    $this->isExistName($name);
    $this->isValidPass($pass);
    $para['pass'] = $this->getPass($pass);
    $para['reg_time'] = time();
    $id = $this->add($para);
    // add user extra
    $r = (new UserExtraLogic)->add(['uid'=>$id,'id_code'=>$this->getIdCode($id)]);
    // add user role
    (new UserRoleLogic)->setRole($id,$role);
    $this->commit();
    return $id;
  }


  // return
  static function isSuper($uid){
    return in_array($uid, self::SUPER_USERS);
  }

  function isTopSaler($uid){
    $role = 0;
    if($uid == UID){
      $role = ROLE;
    }else{
      $role = (new UserRoleLogic)->getRoleId($uid,CLIENT_ID);
    }
    if(RoleLogic::isSaler(ROLE)){
      // 是否一级经销商
      return intval($this->getField(['id'=>$uid],'level'))===1 ? 1 :0;
    }else{
      return 0;
    }
  }

  function getIdCode($uid){
    return ''.intval(1000000+(int)$uid);
  }
  // return
  function getAllInfo($uid){
    $model = $this->getModel();
    $r = $model->get($uid);
    if($this->isSuper($uid)){
      $r['role_id'] = RoleLogic::SUPER;
    }else{
      $r['role_id'] = (new UserRoleLogic)->getRoleId($uid,CLIENT_ID);
    }
    $r->extra;
    return $r->toArray();
  }

  // return
  // throws
  function checkUser($name='',$pass=''){
    $pass = $this->getPass($pass);
    $r = $this->getInfo(['name'=>$name,'pass'=>$pass]);
    !$r && throws(L('user-name-pass-error'));
    return $r;
  }
  // return
  function isExistName($name,$uid=0,$throw=true){
    $map = [
      ['name','=',$name]
    ];
    $uid && $map[] = ['id','<>',$uid];
    $r = $this->getInfo($map,false,'id');
    $r && $throw && throws(L('user-name-exist'));
    return $r ? true : false;
  }
  function isValidPass($pass,$throw=true){
    $r=Check::is_alpha_number($pass,6,12);
    !$r && $throw && throws(L('tip-user-pass'));
    return $r;
  }
  function isValidName($name,$throw=true){
    $r = Check::is_alpha_number($name,4,32);
    !$r && $throw && throws(L('tip-user-name'));
    return $r;
  }
  function equalPass($pass,$check){
    return $pass == $this->getPass($check);
  }
  //return
  function getPass($pass,$salt=''){
    $salt = $salt ? $salt : 'rainbow';
    return md5($pass.$salt);
  }
  // return
  // throws
  function queryCountWithRole($map = null, $page = false, $order = false, $params = false, $fields = false) {
    empty($page) && $page = ['page'=>1,'size'=>10];
    empty($fields) && $fields = 'u.*';
    $model = $this->getModel();

    $count = $model ->alias('u')-> where($map)->join('user_role ur','ur.uid=u.id','left') -> count();
    $list = [];
    if($count){
      $query = $model ->alias('u');
      $query = $query->where($map)->order($order)->field($fields);
      $query = $query->join('user_role ur','ur.uid=u.id','left');
      $query = $query->join('sys_role r','r.id=ur.role_id','left');
      // $query = $query->join('user_extra e','e.uid=u.id','left');

      $start = max(0,(intval($page['page'])-1)*intval($page['size']));
      $list  = $query -> limit($start,$page['size']) -> select();
      $list  = $list->toArray();
    }
    return ["count" => $count, "list" => $list];
  }
}