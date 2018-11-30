<?php
namespace app\admin\controller;

use src\sys\role\SysRoleLogic as RoleLogic;
use src\user\role\UserRoleLogic;
use src\user\user\UserExtraLogic;
use src\user\wallet\UserWalletLogic as WalletLogic;
use src\user\wallet\UserWalletHisLogic as WalletHisLogic;
use src\fy\fy\FyAccountLogic;
use src\fy\fy\FyAccountUserLogic;
use src\fy\fy\FyInviteLogic;

class User extends CheckLogin {
  protected $model_id = 2;
  protected $banDelIds = [1];
  protected $banEditFields = ['id','name'];

  protected function init() {
    $this->cfg['theme'] = 'layer';
  }
  // logic ajax page-list
  function index() {
    $this->checkUserRight();
    $role  = $this->_get('role/d',0);
    $this->assign('role',$role);
    // 查询所有角色
    $roles = (new RoleLogic)->query();
    $this->assign('roles',$roles);
    return $this->show();
  }

  // 下级管理
  function mine() {
    // 查询所有角色
    $roles = (new RoleLogic)->query();
    $this->assign('roles',$roles);
    return $this->show();
  }

  // excel 数据导入 前端已trim
  function excel() {
    if(IS_POST){
      !($this->isSuper || $this->isTopSaler) && $this->err('非一级分销商');
      $this->trans();
      $data     = $_POST['data'];
      $adds     = [];
      $pid_srcs = [];
      $c        = 0;$f=0;
      $fa  = (new FyAccountLogic);
      $fau = (new FyAccountUserLogic);
      $fi  = (new FyInviteLogic);
      try{
        foreach ($data as $v) {
          $v = array_map('trim',$v);
          $temp_pid   = $v[11];
          $temp_real  = fixTimeStamp($v[15],true);
          $temp_reg   = fixTimeStamp($v[0],true);
          $temp_login = fixTimeStamp($v[1],true);
          $temp_1buy  = fixTimeStamp($v[3],true);
          $temp_order_code = trim($v[13]);
          $temp_invite = $fa->getInviteUidByPid($temp_pid);
          $temp_money  = $fa->getInviteMoney($temp_login,$temp_real,$temp_1buy);
          // add
          // $adds[] = [
          $add = [
            'reg_time'      => $temp_reg,
            'login_time'    => $temp_login,
            'active_time'   => fixTimeStamp($v[2],true),
            'first_buy_time'=> $temp_1buy,
            'receive_time'  => fixTimeStamp($v[4],true),
            'user_mobile'   => $v[5],
            'user_status'   => $fi->getUserStatus($v[6]), //1,2
            'order_type'    => $fi->getOrderStatus($v[7]), //1,2,3
            'media_id'      => $v[8],
            'site_id'       => $v[9],
            'pid'           => $temp_pid,
            'pid_name'      => $v[12],
            'order_code'    => $temp_order_code,
            'act_name'      => $v[14],
            'money'         => $temp_money, // int,分
            'invite_uid'    => $temp_invite,
            'add_uid'       => UID,
          ];
          if($temp_order_code && $fi->getInfo(['order_code'=>$temp_order_code],'id asc','id')) {
            $this->err('订单:'.$temp_order_code.'已经导入过了');
          }

          $id = (new FyInviteLogic)->add($add);
          if($id){
            $c ++;
            $temp_pindex = $fa->getField(['pid'=>$temp_pid],'id');
            if($temp_pindex && $temp_invite){ // 存在淘宝客账户 和 邀请人
              // 从1往下返佣到$temp_invite - 递归
              if($temp_money) {
                (new WalletLogic)->fenyong($temp_pindex,$temp_invite,$temp_money);
                $f ++ ;
              }
            }
          }
        }
        // $adds && $c = count($fi->addAll($adds));
      }catch(\Exception $e){
        // throw $e;
        $this->err($temp_pid.':'.$e->getMessage(),$e->getCode());
      }
      $this->commit();
      $this->suc('导入成功,共'.$c.',分佣'.$f);
    }else{// 下载
      $this->error('暂未开放');
    }
  }

  function detail() {
    $this->checkUserRight(0,'index');

    $id = $this->id;
    $tab = $this->_param('tab','');
    $this->assign('tab',$tab);
    $dt_type = $this->_param('dt_type','');
    $this->assign('dt_type',$dt_type);
    // 用户信息
    $info = $this->logic->getInfo(['id'=>$id]);
    !$info && $this->error(Linvalid('user'));
    $this->assign('info',$info);
    // 用户角色
    $user_roles = (new UserRoleLogic)->query(['uid'=>$id]);
    $user_roles = getArr($user_roles,'','role_id');
    // 查询所有角色
    $roles = (new RoleLogic)->query();
    foreach ($roles as &$v) {
      $v['check'] = in_array($v['id'],$user_roles) ? 1 : 0;
    } unset($v);
    $this->assign('roles',$roles);
    // 账户变动类型
    $dtTypes = getDatatree('wallet_change_item','id',null);
    $this->assign('dtTypes',$dtTypes);
    // 用户资产
    // 用户订单
    return $this->show();
  }

  // 我的经销商
  function ajaxChild() {
    $param = $this->_getPara('','kword,start,end');
    extract($param);
    $map = [['u.invite_uid','=',UID],['ur.role_id','=',RoleLogic::SALER]];
    $kword && $map[] = ['u.nick|u.name|u.phone|u.email','like',$kword.'%'];
    ($start || $end) && $map[] = getWhereTime('u.reg_time',$start,$end);
    $r = $this->logic->queryCountWithRole($map,$this->page,$this->sort,false,'u.id,u.rate,u.name,u.nick,u.sex,u.email,u.status,u.reg_time,u.phone,u.email_auth,u.phone_auth,ur.role_id,u.level');
    // 添加总佣金
    foreach ($r['list'] as &$v) {
      $v['fy_money'] = (new WalletHisLogic)->fyCount($v['id']);
    } unset($v);
    $this->checkOp($r);
  }
  // logic ajax page-list
  function ajax(){
    $this->checkUserRight(0,'index');

    $kword = $this->_param('kword','');  // 搜索昵称
    $role  = $this->_param('role/d',0); // 搜索角色
    $start = $this->_param('start',''); // 搜索start
    $end   = $this->_param('end','');   // 搜索end
    $invite = $this->_param('invite',0);   // 上级uid
    $level = $this->_param('level','');   // 几级

    $map   = [];
    $invite && $map[] = ['u.invite_uid','=',$invite];
    $level  && $map[] = ['u.level','=',$level];
    $kword && $map[] = ['u.id|u.name|u.phone|u.email','like',$kword.'%']; //|u.nick
    $role && $map[] = ['ur.role_id','=',$role];
    ($start || $end) && $map[] = getWhereTime('u.reg_time',$start,$end);
    $r = $this->logic->queryCountWithRole($map,$this->page,$this->sort,false,'u.id,u.rate,u.name,u.nick,u.sex,u.email,u.status,u.reg_time,u.phone,u.email_auth,u.phone_auth,ur.role_id,u.level');
    // dump(\think\Db::getLastSql());die();
    // "SELECT COUNT(*) AS tp_count FROM `f_user` `u` LEFT JOIN `f_user_role` `ur` ON `ur`.`uid`=`u`.`id` WHERE  `u`.`reg_time` BETWEEN '1534916580' AND '1535002980' LIMIT 1"
    $this->checkOp($r);
  }

  function set(){
    $this->checkUserRight();

    $saler = RoleLogic::isSaler(ROLE);
    $id = $this->id;
    $this->jsf = array_merge($this->jsf,[
      'name'   =>'用户名',
      'nick'   =>'昵称',
      'pass'   =>'密码',
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
      $role_id = '';
      if($id){ // detail
        $userRole = (new UserRoleLogic)->getInfo(['uid'=>$id]);
        $this->assign('userRole',$userRole);
        $role_id = $userRole['role_id'];// 查看的用户角色
      }else{ // new
        if($this->isSuper){
        }else{
          if($saler){
            // 代理商 只添加代理商
            $roles = [array_column($roles,null,'id')[$saler]];
          }else{
            $roles = [];
          }
        }
      }
      $jsfs = [
        ['*name',$id ? '|layui-disabled' : '',$id ? 'disabled': '']
      ];
      $jsfs[] = $id ? ['pass*'] : ['*pass*'];
      $jsfs = array_merge($jsfs,[
        ['nick','input-long'],
        ['avatar|btimg','',1],
        ['phone'],
        ['email'],
        ['status|radio'],
        ['*role|selects|'.$role_id,'',$roles]
      ]);
      $this->jsf_tpl = $jsfs;
      return parent::set();
    }else{      // save
      $paras = $this->_getPara('nick','avatar,phone,email,status|0|int,pass');
      $role  = $this->_param('role/d',0);
      empty($role) && $this->err(Llack('role'));
      // 当前角色
      if($this->isAdmin) {
      }elseif($saler) {
        ROLE != $role && $this->err('无权管理此角色');
      }else{
        $this->err('无权限');
      }
      $uinfo = UINFO;
      $level = intval($uinfo['level'])+1;
      if($id){  // edit
        // $this->logic->isExistName($paras['name']);
        // $this->logic->isValidName($paras['name'],ture);
        // unset($paras['name']); // 用户名添加后不许改变
        if($paras['pass']){
          $this->logic->isValidPass($paras['pass']);
          // check and encrypt pass
          $paras['pass'] = $this->logic->getPass($paras['pass']);
        }else{
          unset($paras['pass']);
        }
        $this->trans();
        $this->logic->save(['id'=>$id],$paras);
        // add user role
        (new UserRoleLogic)->setRole($id,$role);
      }else{  // add
        $paras['name'] =  $this->_param('name');
        $paras['invite_uid'] = UID; // 邀请人
        $paras['level']    = $level;
        $paras['reg_from'] = 4; // admin
        $id = $this->logic->addUser($paras,$role);
      }
      $this->suc_url = url(CONTROLLER_NAME.'/index');
      $this->suc(LL('op suc'));
    }
  }

  // 删除, 由id标志, check是检查是否含parent=id
  function del(){
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
  function dels(){
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