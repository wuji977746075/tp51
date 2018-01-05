<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-15
 * Time: 10:41
 */

namespace app\src\admin\helper;


use app\src\menu\logic\MenuLogicV2;

class MenuHelper
{

    public static function getBreadcrumb(){
        $breadcrumb = [];
        $active_menu_id = AdminSessionHelper::getCurrentActiveTopMenuId();
        if ($active_menu_id > 0) {
            $map = array('id' => $active_menu_id);
            $result = (new MenuLogic())->getInfo($map);
            if ($result['status']) {
                $url = AdminFunctionHelper::getURL($result['info']['url']);
                array_push($breadcrumb, array('title' => $result['info']['title'], 'url' => $url));
            } else {
                LogRecord($result['info'], '[FILE]' . __FILE__ . ' [LINE]' . __LINE__);
            }
        }
        //二级导航
        $active_left_menu_id = AdminSessionHelper::getCurrentActiveLeftMenuId();
        //TODO: 如果为了链接考虑，可以转为Cookie来存取
        if ($active_left_menu_id > 0) {
            $map = array('id' => $active_left_menu_id);
            $result = (new MenuLogic())->getInfo($map);
            if ($result['status']) {
                $url = AdminFunctionHelper::getURL($result['info']['url']);
                array_push($breadcrumb, array('title' => $result['info']['title'], 'url' => $url));
            } else {
                LogRecord($result['info'], '[FILE]' . __FILE__ . ' [LINE]' . __LINE__);
            }
        }

        return $breadcrumb;
    }

    public static function getTopMenu($uid){
        AdminSessionHelper::setTopMenu(null);
        $list = AdminSessionHelper::getTopMenu();

        if ($list === false || is_null($list)) {
            $map = array('pid' => 0);

            $result = (new MenuLogic())->queryShowingMenu($map, ' sort desc ');

            if ($result['status']) {
                $list = $result['info'];
                $current_menus = AdminSessionHelper::getCurrentUserMenu();

                foreach ($list as &$vo) {
                    //不在菜单id中且非超级管理员
                    if(strpos($current_menus, $vo['id'].',') === false && ! AdminFunctionHelper::isRoot($uid)){
                        //动态隐藏无权限的菜单
                        $vo['dynamic_hide'] = 1;
                    }
                }

                AdminSessionHelper::setTopMenu($list);
            } else {
                echo $result['info'];
                return;
            }
        }

        if (!empty($list) && !AdminSessionHelper::hasCurrentActiveTopMenuId()) {
            for($k=0;$k<count($list);$k++){
                if(!isset($list[$k]['dynamic_hide'])){
                    AdminSessionHelper::setCurrentActiveTopMenuId($list[$k]['id']);
                    break;
                }
            }
        }

        return $list;
    }

    public static function getLeftMenu($uid){

        //是否有激活的一级菜单
        $left_menu = [];

        if (AdminSessionHelper::hasCurrentActiveTopMenuId()) {
            $pid =  AdminSessionHelper::getCurrentActiveTopMenuId();
            $map = array('pid' => $pid);
            $result = (new MenuLogic())->queryShowingMenu($map, ' sort desc ');

            if ($result['status']) {
                $left_menu = $result['info'];
                $hasSubmenuID = false;
                //TODO: 为了速度 可以考虑把 二级菜单，三级菜单一起查询出来，再来组合成需要的数据结构,而不必如下，进行多次查询
                $current_menus = AdminSessionHelper::getCurrentUserMenu();
                $isRoot = AdminFunctionHelper::isRoot($uid);

                foreach ($left_menu as &$vo) {

                    //不在菜单id中且非超级管理员
                    if (strpos($current_menus, $vo['id'] . ',') === false && !$isRoot) {
                        $vo['dynamic_hide'] = 1;
                    }

                    $map['pid'] = $vo['id'];
                    $result = (new MenuLogic())->queryShowingMenu($map, " sort desc ");


                    if ($result['status']) {
                        $vo['children'] = $result['info'];
                        if (!$hasSubmenuID && !empty($result['info']) && count($result['info']) > 0) {
                            $hasSubmenuID = true;
                        }

                        foreach ($vo['children'] as &$child) {
                            //不在菜单id中且非超级管理员
                            if (strpos($current_menus, $child['id'] . ',') === false && !$isRoot) {
                                $child['dynamic_hide'] = 1;
                            }
                        }
                    }
                }
            }
        }else{

        }
        return $left_menu;
    }

    /**
     *
     * @param  $uid
     * @return array|bool|mixed
     */
    public static function getFMenu($uid){
        $is_super = AdminFunctionHelper::isRoot($uid);
        $menuList = [];//AdminSessionHelper::getAdminTopMenuList($uid);
        if(empty($menuList)){//未缓存、或过期
            $current_menus = AdminSessionHelper::getCurrentUserMenu();
            $current_menus = explode(',',rtrim($current_menus,','));
            $current_menus = array_unique($current_menus);

            $menuList = [];
            $list = (new MenuLogicV2)->queryNoPaging([] ,"sort desc");
            foreach($list as $val){
                if(in_array($val['id'],$current_menus) || $is_super){
                    if($is_super || intval($val['hide'])===0){
                        $menuList[] = [
                            'id'     => (int) $val['id'],
                            'parent' => (int) $val['pid'],
                            'name'   => $val['title'],
                            'icon'   => (int) $val['icon'],
                            'active' => 0,
                            'url'    => preg_replace('/^\/?Admin\//', '', $val['url'])
                        ];
                    }
                }
            }
        }
        return $menuList;
    }


    /**
     * 将格式数组转换为树
     *
     * @param array $list
     * @param integer $level 进行递归时传递用的参数
     */
    private static  $formatTree; //用于树型数组完成递归格式的全局变量

    private static function _toFormatTree($list,$level=0,$title = 'title') {
        foreach($list as $key=>$val){
            $tmp_str = str_repeat("&nbsp;",$level*2);
            $tmp_str.= "└";

            $val['level'] = $level;
            $val['title_show'] = $level==0 ? $val[$title]: $tmp_str.$val[$title];
            // $val['title_show'] = $val['id'].'|'.$level.'级|'.$val['title_show'];


            if(!array_key_exists('_child',$val)){
                array_push(self::$formatTree,$val);
            }else{
                $tmp_ary = $val['_child'];
                unset($val['_child']);
                array_push(self::$formatTree,$val);
                self::_toFormatTree($tmp_ary,$level+1,$title); //进行下一层递归
            }
        }
        return;
    }

    public static function toFormatTree($list,$title = 'title',$pk='id',$pid = 'pid',$root = 0){
        $list = self::list_to_tree($list,$pk,$pid,'_child',$root);
        self::$formatTree = [];
        self::_toFormatTree($list,0,$title);
        return self::$formatTree;
    }


    public static function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }
        return $tree;
    }

}