<?php
namespace app\admin\controller;
use src\sys\session\SysSessionLogic as SessionLogic;
use src\admin\menu\MenuLogic;
use src\user\user\UserLogic;

class Manager extends CheckLogin{
  protected $model_id = 12;
  public $menu = [];
  public $lang = ''; // 前端语言

  public function initialize(){
    parent::initialize();
    $this->lang = 'zh';
    $this->module_id = 0;
    $this->menu = (new MenuLogic)->getUserMenu($this->module_id,UID,false);
    // 查询用户顶级菜单
    $top_menu_id = input('menu_id/d',0);
    $menu_id   = 0;
    $menu_all  = $this->menu;
    $menu_json = json_encode($menu_all);

    $this->assign('top_menu_id',$top_menu_id);
    $this->assign('menu_id',$menu_id);
    $this->assign('menu_json',$menu_json);
    $this->assign('uinfo',UINFO);// 当前登录用户信息 , 用户模块使用info
    $this->assign('skin','df');
    $this->assign('lang',$this->lang);
  }

  public function welcome(){
    $userNum = (new UserLogic)->count([]);
    $this->assign('userNum',$userNum);
    return $this->show();
  }

  public function logout(){
    (new SessionLogic())->adminLogout();
    return $this->suc(lang('success'));
  }
}