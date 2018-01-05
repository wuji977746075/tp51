<?php
namespace app\admin\controller;

use src\auth\AuthLogic;
use src\menu\MenuLogic;
use src\client\ClientLogic;

class Role extends CheckLogin{
  protected $banDelIds = [1,2];

  public function set(){
    $id = $this->id;
    $this->jsf = array_merge($this->jsf,[
      'name'=>'角色名',
    ]);
    if(IS_GET){ // view
    }else{      // save
      $this->jsf_field = ['name','desc,status|0|int'];
      $this->suc_url   = url('admin/role/index');
    }
    return parent::set();
  }

  // 权限管理
  public function auth($id=0){
    $info = $this->logic->get($id);
    if(IS_GET){ // view
      // ? id
      if(empty($info)) $this->error(Linvalid('id'));
      $this->assign('info',$info);
      $this->assign('id',$id);
      // 角色节点
      $rAuth  = $info['api_auth'];
      $rAuths = json_decode($rAuth,true);
      // $rAuths = explode(',', $rAuth);
      $mAuth  = $info['menu_auth'];
      $mAuths = explode(',', $mAuth);
      // 全部节点
      $nodes = (new AuthLogic)->query();
      $auths = [];
      // 查询客户端 (admin可以除外)
      $clients =  (new ClientLogic)->query([]);
      foreach ($clients as $v) {
        // 客户端开启的节点
        $cAuth  = $v['api_auth'];
        $cAuths = explode(',', $mAuth);
        $v['nodes'] = $nodes;
        if($rAuths && array_key_exists($v['id'],$rAuths)){
          $ids = $rAuths[$v['id']];
          // 设置节点选中
          foreach ($v['nodes'] as &$v2) {
            $v2['sel']  = in_array($v2['id'],$ids) ? 1:0;
            $v2['open'] = in_array($v2['id'],$cAuths) ? 1:0;
          } unset($v2);
        }else{
          foreach ($v['nodes'] as &$v2) {
            $v2['sel']  = 0;
            $v2['open'] = 0;
          } unset($v2);
        }
        $auths[] = $v;
      }
      $this->assign('auths',$auths);

      // 查询所有3级以内分级菜单
      $menus = (new MenuLogic)->getUserMenu(0,$this->uid,false);
      // 设置菜单选中
      foreach ($menus as &$v) {
        $v['sel'] = in_array($v['id'],$mAuths) ? 1:0;
        foreach ($v['child'] as &$v2) {
          $v2['sel'] = in_array($v2['id'],$mAuths) ? 1:0;
          foreach ($v2['child'] as &$v3) {
            $v3['sel'] = in_array($v3['id'],$mAuths) ? 1:0;
          }
        }
      } unset($v3);unset($v2);unset($v);
      $this->assign('menus',$menus);

      return $this->show();
    }else{ // save
      if(empty($info)) $this->err(Linvalid('id'));
      $type = input('param.type','');

      if($type == 'menu'){
        $menus = input('param.menu_auth/a',[]);
        $menu_auth = $menus ? implode(',', $menus) : '';
        $save = ['menu_auth'=>$menu_auth];
      }elseif($type == 'api'){
        $apis = input('param.api_auth/a',[]);
        // $api_auth = $apis ? implode(',', $apis) : '';
        $api_auth = [];
        foreach ($apis as $v) {
          $temp = explode(',', $v);
          if(count($temp)>1 && $temp[1]){
            $pk = $temp[0];
            isset($api_auth[$pk]) || $api_auth[$pk] = [];
            $api_auth[$pk][] = $temp[1];
          }
        }
        $save = ['api_auth'=>json_encode($api_auth)];
      }else{
        $this->err(Linvalid('type'));
      }

      $r = $this->logic->saveById($id,$save);
      $this->checkOp($r);
    }
  }
}