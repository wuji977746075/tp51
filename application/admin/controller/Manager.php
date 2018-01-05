<?php
namespace app\admin\controller;
use src\menu\MenuLogic;
use src\user\UserLogic;
use src\session\SessionLogic;

class Manager extends CheckLogin{

  public $menu = [];
  public $lang = ''; // 前端语言

  public function initialize(){
    parent::initialize();
    $this->lang = 'zh';
    $this->module_id = 0;

    $this->menu = (new MenuLogic)->getUserMenu($this->module_id,$this->uid,false);
    // $this->userMenuIds = $this->getUserAuthMenuIds($this->uid);
    // 查询用户顶级菜单
    $top_menu_id = input('menu_id/d',0);
    $menu_id  = 0;
    $menu_all = $this->menu;
    // dump($menu_all);die();
    $uinfo = (new UserLogic)->getAll($this->uid);

    $menu_json = json_encode($menu_all);
    $this->assign('top_menu_id',$top_menu_id);
    $this->assign('menu_id',$menu_id);
    $this->assign('menu_json',$menu_json);
    $this->assign('uinfo',$uinfo);
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