<?php
namespace src\role;

use src\base\BaseLogic;
use src\auth\AuthLogic;

class RoleLogic extends BaseLogic{
  const ADMIN        = 1;
  const DEFAULT_ROLE = 2;

  const API_AUTH  = 'api_auth'; // api权限(所有客户端) : json字符串存储
  const MENU_AUTH = 'menu_auth';// admin权限 : id,连接存储

  function getAuthByRole($role_id,$type=''){
    $r = $this->get($role_id);
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
    $node = trim($node);
    if(empty($node)) return true;
    // 用户角色
    $role_id = (new UserRoleLogic)->getRoleId($uid);
    // 角色的所有客户端 api权限
    $auth = $this->getAuthByRole($role_id,self::API_AUTH);
    // 角色的客户端 api权限
    if(!isset($auth[$client_id])) return false;
    $auth =  (array) $auth[$client_id];
    // 权限对应的id
    $node_id = (new AuthLogic)->getIdByName($node);
    // 角色客户端权限是否存在
    return in_array($node_id,$auth);
  }
}
