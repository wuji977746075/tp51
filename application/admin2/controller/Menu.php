<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-16
 * Time: 14:22
 */

namespace app\admin\controller;


use app\src\admin\helper\AdminConfigHelper;
use app\src\admin\helper\MenuHelper;
use app\src\menu\logic\MenuLogic;
use think\Request;

class Menu extends Admin
{


    protected function initialize() {
        parent::initialize();
        //
        $pid = $this->_param('pid',0);
        if ($pid != 0) {
            $result = (new MenuLogic)->getInfo(['id' => $pid]);
            if ($result['status']) {
                $this -> assign('parentMenu', $result['info']);
            } else {
                $this -> error(L('UNKNOWN_ERR'));
            }
        }
    }

    /**
     * 菜单
     */
    public function index() {
        $map = [];
        $pid = $this->_param('pid/d',0);
        $map['pid'] = $pid;

        $page = array('curpage' => $this->_param('p/d',1), 'size' => $this->size);
        $order = "sort desc";
        $r = (new MenuLogic)->queryWithPagingHtml($map, $page, $order);
        !$r['status'] && $this->error($r['info']);

        //2. 不能删除的菜单id
        $cant_delete_menus = '284,164';
        $this->assign("cant_delete",$cant_delete_menus);
        $this -> assign("show", $r['info']['show']);
        $this -> assign("list", $r['info']['list']);
        return $this->show();
    }

    /**
     * 保存
     */
    public function save($logic=null,$primarykey = 'id', $entity = NULL, $redirect_url = false) {

        $entity = $this->request->param();
        $pid = $this->_param('pid/d',0);
        $id  = $this->_param('id/d',0);

        $entity['pid'] = $pid;
        $redirect_url = url('Menu/index', ['pid' => $pid]);

        //TODO: 保存到权限规则表中
        $result = (new MenuLogic)->getInfo(['id'=>$id]);
        if($result['status'] && is_array($result['info'])){
            $newEntity = array(
                'title'=>$entity['title'],
                'name'=>$entity['url'],
                'type'=>$entity['pid'],
            );
        }else{
            $this->error("获取数据错误，请重试！");
        }

        $hide = $this->_post('hide','');
        if($hide === ''){
            $entity['hide']= 0;
        }

        $is_front = $this->_post('is_front','');
        if($is_front === ''){
            $entity['is_front']= 0;
        }

        $result = (new MenuLogic)->saveByID($id, $entity);
        if ($result['status'] === false) {
            $this -> error($result['info']);
        } else {
            $this -> success(L('RESULT_SUCCESS'), $redirect_url);
        }

    }

    public function edit() {
        if (IS_GET) {
            $id = $this->_param('id/d',0);

            $map = ['id' => $id];
            $r = (new MenuLogic)->getInfo($map);
            !$r['status'] && $this -> error(L('C_GET_NULLDATA'));
            $this -> assign("entity", $r['info']);

            $map = [['id','<>' , $id]];
            $r = (new MenuLogic)->queryNoPaging($map);
            !$r['status'] && $this -> error($r['info']);
            $menus = MenuHelper::toFormatTree($r['info']);
            $this -> assign("pid", $this->_param('pid',0));
            $this -> assign("menus", $menus);
            return $this->show();
        }
    }

    /**
     * 增加菜单
     */
    public function add() {
        if (IS_GET) {
            $result = (new MenuLogic)->queryNoPaging();
            if ($result['status']) {
                $menus = MenuHelper::toFormatTree($result['info']);
                $this -> assign("pid", $this->_param('pid',0));
                $this -> assign("menus", $menus);
                return $this->show();
            } else {
                $this -> error($result['info']);
            }
        } else {
            $menu = Request::instance()->param();

            $menu['pid'] = $this->_param('pid','');
            $success_url = url('Admin/Menu/index', array('pid' => $menu['pid'] ));

            $result =  (new MenuLogic())->add($menu);
            if ($result['status'] === false) {
                $this -> error($result['info']);
            } else {
                $this -> success(L('RESULT_SUCCESS'), $success_url);
            }
        }
    }

    /**
     * 删除菜单
     */
    public function delete($success_url = false)
    {
        $pid = $this->_param('id', -1);
        $map = array('pid' => $pid);
        $result = (new MenuLogic())->query($map);
        if ($result['status'] && !is_null($result['info'])) {

            if (count($result['info']['list']) > 0) {
                $this->error(L('ERR_CANT_DEL_HAS_CHILDREN'));
            } else {

                $map = array('id' => $pid);
                //获取菜单信息
                $result = (new MenuLogic())->getInfo($map);

                if ($result['status']) {
                    $entity = $result['info'];
                } else {
                    $this->error($result['info']);
                }

                $result = (new MenuLogic())->delete($map);

                if ($result['status'] === false) {
                    $this->error($result['info']);
                } else {

                    if(empty($success_url)){
                        $success_url = url('Admin/Menu/index');
                    }

                    $this->success(L('RESULT_SUCCESS'), $success_url );
                }

            }
        }
    }
}