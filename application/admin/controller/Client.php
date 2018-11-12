<?php
namespace app\admin\controller;
use src\auth\AuthLogic;
class Client extends CheckLogin{

  protected $banEditFields = ['client_id','id'];

  public function set(){
    $this->jsf = [
      'client_name'  => '客户端',
      'client_id'    => 'client_id',
      'client_secret'=> 'client_secret',
      'api_alg'      => '加密方式',
    ];
    if(IS_GET){ // view
      $this->jsf_tpl = [
        ['client_name'],
        ['client_id','input-long'],
        ['client_secret','input-long'],
        ['api_alg|select','input-long',['md5'=>'MD5']],
        ['desc|textarea','input-long'],
      ];
    }else{ // save
      $this->jsf_field = ['client_name,client_id,client_secret,api_alg|md5','desc'];
      // $this->suc_url   = url(CONTROLLER_NAME.'/index');
    }
    return parent::set();
  }

  // 客户端api权限配置
  public function auth($id=0){
    $r = $this->logic->get($id);
    if(IS_GET){ // edit
      empty($r) && $this->error(Linvalid('id'));
      $this->assign('id',$id);
      $api_auth = $r['api_auth']; unset($r['api_auth']);
      $this->assign('info',$r);
      $ids = explode(',', $api_auth);

      $auths = (new AuthLogic)->query([]);
      foreach ($auths as &$v) {
        $v['sel'] = in_array($v['id'],$ids) ? 1 :0;
      } unset($v);
      $this->assign('auths',$auths);
      return $this->show();
    }else{ // save
      empty($r) && $this->err(Linvalid('id'));
      $api_auth = $this->_param('api_auth/a',[]);
      $this->logic->saveById($id,['api_auth'=>implode(',', $api_auth)]);
      $this->suc();
    }
  }
}
