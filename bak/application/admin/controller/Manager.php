<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-14
 * Time: 16:36
 */

namespace app\admin\controller;


use app\src\admin\helper\AdminSessionHelper;
use app\src\admin\helper\MenuHelper;

class Manager extends Admin
{

    //首页
    public function index(){
        // $this->assign('_top_mid',AdminSessionHelper::getCurrentActiveTopMenuId());
        // $this->assign('_left_mid',AdminSessionHelper::getCurrentActiveLeftMenuId());
        // halt(IS_DEBUG);
        // if($this->isMobile){
        //     return $this->boye_display();
        // }
        // return $this->boye_display("learun");
        // halt($this->view->seo); //seo cfg _menuList by_skin user
        return $this->show('index/index');
    }

    //介绍
    public function about(){

        if($this->isMobile){
            return $this->boye_display();
        }
        return $this->boye_display("learun");
    }

    //
    public function well(){
        $url = request()->scheme().'://'.request()->host();
        $this->assign('admin_url',$url);
        return $this->boye_display("learun");
    }

    public function userMenu()
    {
        if(IS_AJAX){
            $menuList = MenuHelper::getVMenu(UID);
            $this->success('获取用户菜单成功', null, $menuList);
        }
    }
    public function userData()
    {
        if(IS_AJAX){
            $menuList = MenuHelper::getVMenu(UID);
            $user_info = AdminSessionHelper::getUserInfo();

            $UCENTER_PLATFORM = config('UCENTER_PLATFORM');
            $user_info = [
                'id' => $user_info['id'],
                'username' => $user_info['username'],
                'nickname' => $user_info['nickname'],
                'head' => $user_info['head']
            ];
            $data = [
                'menuList' => $menuList,
                'userInfo' => $user_info,
                'platformInfo' => $UCENTER_PLATFORM
            ];
            $this->success('获取用户数据成功', null, $data);
        }
    }
}