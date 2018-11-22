<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

namespace app\admin\controller;

use app\src\admin\helper\AdminConfigHelper;
use app\src\system\logic\ConfigLogic;
use think\Cache;
use think\Request;
use think\View;

class Config  extends Admin {
    protected $groups;
    public function initialize() {
        parent::initialize();

        $this->groups = config('CONFIG_GROUP_LIST');
        $this -> assignTitle(L('C_CONFIG'));
    }

    /**
     * 配置
     */
    public function index() {
        $map   = [];
        $param = [];
        $name  = $this->_param('name', '');
        if($name){
            $map['name']   = ['like', '%' . $name . '%'];
            $param['name'] = $name;
        }
        $group = $this->_param('group/d',0);
        $this->assign('group',$group);
        if($group !== -1){
            $map['group']   = $group;
            $param['group'] = $group;
        }

        $page = ['curpage' => $this->_param('p/d', 1), 'size' => $this->size];
        $order = 'update_time desc';
        $r =  (new ConfigLogic)->queryWithPagingHtml($map, $page, $order,$param);
        if ($r['status']) {
            $list = $r['info']['list'];
            $show = $r['info']['show'];
            foreach ($list as &$v) {
                if(in_array(intval($v['type']),[0,1])){
                  $v['show_value'] = $v['value'];
                }else{
                  $v['show_value'] = '其他->';
                }
            } unset($v);
            $this -> assign("config_groups", $this->groups);
            $this -> assign("show", $show);
            $this -> assign("list", $list);
            return $this -> show();
        } else {
            $this -> error($r['info']);
        }
    }

    private function setConfigView($group){
        $map = array('group'=>$group);
        $result = (new ConfigLogic)->queryNoPaging($map);
        if($result['status']){
            return $this->view->fetch("default/Widget/config_set",['group'=>$group,'list'=>$result['info']]);
        }
        return "-0-";

    }

    /**
     * 设置
     */
    public function set(){
        if(IS_GET){
            $this->configVars();
            return $this->show();
        }else{
            $config = $this->_post('config/a',[]);
            $order = 'sort desc';
            $result = (new ConfigLogic)->set($config);
            if($result['status']){
                //清除缓存
                cache("config_" . session_id() . '_' . $this->_uid ,null);
                // $this->success(L('RESULT_SUCCESS'),url('Config/set'));
                return ajaxReturnSuc(L('RESULT_SUCCESS'),url('Config/set',['group'=>$this->_param('group/d',0)]));
            }else{
                return ajaxReturnErr($result['info']);
            }
        }
    }

    /**
     * 更新保存，根据主键默认id
     * 示列url:
     * /Admin/Menu/save/id/33
     * id必须以get方式传入
     * @param string $primarykey
     * @param null $entity
     * @param bool $redirect_url
     */
    public function save($logic=null, $primarykey = 'id', $entity = NULL, $redirect_url = false){
        $group = $this->_param('group/d',0);
        parent::save(new ConfigLogic,$primarykey , $entity, url('config/index',['group'=>$group]));
    }

    /**
     * 添加
     */
    public function add() {
        if (IS_GET) {
            $this->configVars();
            return $this -> show();
        } else {
            $menu = Request::instance()->param();
            $result = (new ConfigLogic())->add($menu);

            if($result['status']){
                $this->success("操作成功",url('Admin/Config/index'));
            }

            $this->error('操作失败',url('Admin/Config/index'));
        }
    }

    public function edit() {
        $this->configVars();
        $map = array('id' => $this->_param('id',0));
        $result = (new ConfigLogic())->getInfo($map);

        if ($result['status'] === false) {
            $this -> error(L('C_GET_NULLDATA'));
        } else {
            $this -> assign("entity", $result['info']);
            return $this->show();
        }
    }

    public function delete(){
        $this->_delete(new ConfigLogic());
    }



    /**
     * 配置分组与类型参数
     */
    protected function configVars() {
        //配置分组
        $config_groups = AdminConfigHelper::getValue('CONFIG_GROUP_LIST');
        //配置类型
        $config_types = AdminConfigHelper::getValue('CONFIG_TYPE_LIST');
        $config_view = [];
        foreach ($config_groups as $key=>&$item) {
            $config_view[$key]  = $this->setConfigView($key);
        }
        // dump($config_view);
        // dump($config_groups);
        // dump($config_types);
        $this -> assign('config_view', $config_view);
        $this -> assign('config_groups', $config_groups);
        $this -> assign('config_types', $config_types);
    }

}
