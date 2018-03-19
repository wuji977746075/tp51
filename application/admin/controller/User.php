<?php
namespace app\admin\controller;

use src\role\RoleLogic;
use src\role\UserRoleLogic;
use src\user\UserExtraLogic;

class User extends CheckLogin{
  protected $banDelIds = [1];
  // logic ajax page-list
  public function index(){
    $role  = $this->_get('role/d',0);
    $this->assign('role',$role);

    // 查询所有角色
    $roles = (new RoleLogic)->query();
    $this->assign('roles',$roles);
    return $this->show();
  }

  // logic ajax page-list
  public function ajax(){
    $kword  = $this->_get('kword','');  // 搜索昵称
    $role  = $this->_get('role/d',0); // 搜索角色
    $start = $this->_get('start',''); // 搜索start
    $end   = $this->_get('end','');   // 搜索end
    $map = [];
    $kword && $map[] = ['u.nick|u.name|u.phone|u.email','like','%'.$kword.'%'];
    $role  && $map[] = ['ur.role_id','=',$role];
    $r = $this->logic->queryCountWithRole($map,$this->page,$this->sort,false,'u.*,r.name as role_name');
    $this->checkOp($r);
  }

  public function set(){
    $id = $this->id;
    $this->jsf = array_merge($this->jsf,[
      'name'   =>'用户名',
      'nick'   =>'昵称',
      'avatar' =>'头像',
      'phone'  =>'手机',
      'email'  =>'邮箱',
      'role'   =>'角色',
    ]);
    if(IS_GET){ // view
      // 查询所有角色
      $roles = (new RoleLogic)->query();
      $this->assign('roles',$roles);
      // 查询用户角色
      if($id){
        $userRole = (new UserRoleLogic)->getInfo(['uid'=>$id]);
        $this->assign('userRole',$userRole);
      }
      return parent::set();
    }else{      // save
      $paras = $this->_getPara('name,nick','avatar,phone,email');
      $role  = $this->_param('role/d',0);
      $this->trans();
      if($id){  // edit
        $this->logic->save(['id'=>$id],$paras);
      }else{    // add
        $id = $this->logic->add($paras);
        // add user extra
        $r = (new UserExtraLogic)->add(['uid'=>$id,'id_code'=>''.intval(1000000+(int)$id)]);
      }
      if($role){
        // add user role
        (new UserRoleLogic)->setRole($id,$role);

        $this->suc_url = url(CONTROLLER_NAME.'/index');
        $this->suc('添加成功');
      }
      $this->err('需要制定角色');
    }
  }


  // 删除, 由id标志, check是检查是否含parent=id
  public function del(){
    $id    = $this->id;
    // ? id
    if($id<=0) $this->err(Linvalid('op'));
    if(in_array($id,$this->banDelIds)) $this->err(L('ban-del').' id: '.$id);

    $this->logic->delete(['id'=>$id]);
    (new UserExtraLogic)->delete(['uid'=>$id]);
    (new UserRoleLogic)->delete(['uid'=>$id]);
    $this->opSuc('','',0);
  }

  // 批量删除, 由id标志
  public function dels(){
    $ids   = $this->_param('ids/a',[]);
    // ? id
    if(empty($ids)) $this->err(Linvalid('op'));
    $banIds = array_intersect($this->banDelIds,$ids);
    if($banIds) $this->err(L('ban-del').' id: '.implode(L('&'), $banIds));

    $this->logic->delete([['id','in',$ids]]);
    (new UserExtraLogic)->delete([['uid','in',$ids]]);
    (new UserRoleLogic)->delete([['uid','in',$ids]]);
    $this->opSuc('','',0);
  }
}