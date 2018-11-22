<?php
namespace src\sys\client;

use src\base\BaseLogic;
use src\sys\auth\AuthLogic;
use ErrorCode as EC;

class ClientLogic extends BaseLogic{

  public function getAuthByClientID($client_id){
    $r = $this->getInfo(['client_id'=>$client_id]);
    empty($r) && $this->throws(Linvalid('client_id'),EC::Invalid_Para);
    return explode(',',$r['api_auth']);
  }

  public function checkAuth($client_id,$node){
    $node = trim($node);
    if(empty($node)) return true;
    // 权限对应的id
    $node_id = (new AuthLogic)->getIdByName($node);
    // 客户端的所有权限id
    $auth = $this->getAuthByClientID($client_id);
    // 判断
    return in_array($node_id,$auth);
  }
}
