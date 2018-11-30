<?php
namespace src\sys\role;

use src\base\BaseLogic;
use src\sys\auth\SysAuthLogic as AuthLogic;
use src\sys\client\SysClientLogic as ClientLogic;

class SysRoleLogic extends BaseLogic{
  const SUPER        = -1;
  const ADMIN        = 1;
  const DEFAULT_ROLE = 2;
  const SALER        = 3;

  const API_AUTH  = 'api_auth'; // api权限(所有客户端) : json字符串存储
  const MENU_AUTH = 'menu_auth';// admin权限 : id,连接存储

  static function  isSaler($role_id){
    return $role_id == self::SALER ? $role_id : 0;
  }
  static function  isSuper($role_id){
    return $role_id==self::SUPER ? 1 :0;
  }
  static function  isAdmin($role_id){
    return $role_id==self::ADMIN ? 1 :0;
  }
  static function  isAuth($role_id){
    return in_array($role_id,[self::ADMIN,self::SUPER]);
  }

  function getAuthByRole($role_id,$type=''){
    // todo : role : status
    $r = $this->getInfo(['id'=>$role_id],'id asc',self::API_AUTH.','.self::MENU_AUTH);
    if(empty($r)) return [];
    if($type == self::API_AUTH){
      $auth = json_decode($r[$type],true);
    }elseif($type == self::MENU_AUTH){
      $auth = explode(',', $r[$type]);
    }else{
      $auth = [];
    }
    return $auth;
  }

  function checkApiAuth($uid,$node='',$client_id){
    $node    = strtolower(trim($node));
    // if(empty($node)) return true;
    // 用户角色
    $role_id = (new UserRoleLogic)->getRoleId($uid,$client_id);
    // 角色的所有客户端 api权限
    $auth = $this->getAuthByRole($role_id,self::API_AUTH);
    $cid  = (new ClientLogic)->getField(['client_id'=>$client_id],'id');
    // 角色的客户端 api权限
    if(!isset($auth[$cid])) return false;
    $auth    = (array) $auth[$cid];
    // 权限对应的id
    $node_id = (new AuthLogic)->getIdByName($node);
    // 角色客户端权限是否存在
    return in_array($node_id,$auth);
  }
}
