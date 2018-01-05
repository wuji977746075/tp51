<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-13
 * Time: 15:17
 */

namespace app\admin\controller;

use app\src\admin\controller\CheckLoginController;
use app\src\admin\helper\AdminConfigHelper;
use app\src\admin\helper\AdminFunctionHelper;
use app\src\admin\helper\AdminSessionHelper;
use app\src\message\logic\MessageLogic;
use app\src\admin\helper\MenuHelper;
use app\src\base\helper\ResultHelper;
use app\src\base\logic\BaseLogic;
use app\src\powersystem\logic\AuthGroupAccessLogic;
use app\src\powersystem\logic\AuthGroupLogic;
use think\Request;
use think\Controller;

class Admin extends CheckLoginController
{

    protected $_top_mid  = 0;
    protected $_left_mid = 0;
    protected $isMobile;
    protected $size; //默认分页大小
    protected $pager; //默认分页参数
    protected $_msg_num = 0;

    protected function initialize()
    {
        parent::initialize();   // 检测登陆 并 定义$this->_uid
        $this->initConfig();    // table -> config
        $this->setActiveMenu(); // get and set active ids : set new msg_num
        $this->initUserMenu();  // user menuids + active menuids
        $this->initGlobalPageValue(); // set _menuList by_skin user
    }

    //==后台初始化开始

    private function initConfig(){
        AdminConfigHelper::init();
        $size = AdminConfigHelper::getValue('LIST_ROWS');
        $this->size = $size>0 ? $size : 10;
        $this->pager = ['curpage'=>input('p/d',1),'size'=>$this->size];
    }

    protected function exitIfError($r,$trans=false){
        if(!$r['status']){
            if($trans) \think\Db::rollback();
            $this->error($r['info']);
        }
    }
    protected function exitIfNotEmpty($r,$msg='this guy must be empty'){
        if(!empty($r['info'])) $this->error($msg);
    }
    protected function exitIfEmpty($r,$msg='this guy must\'t be empty'){
        if(empty($r['info'])) $this->error($msg);
    }

    /**
     * 初始化页面全局变量
     */
    private function initGlobalPageValue(){
        $this->isMobile = $this->request->isMobile();
//        if(!IS_AJAX){

//            $url = $this->_param('ret_url','');
//
//            if(empty($url) && isset($_SERVER['HTTP_REFERER'])){
//                $url = $_SERVER['HTTP_REFERER'];
//            }
//
//            if(isset($_SERVER['PHP_SELF']) && $url != $_SERVER['PHP_SELF']){
//                if(strpos($url,$_SERVER['PHP_SELF']) === false){
//                    $url = "javascript:parent.window.itboye.history.back();";
//                }
//            }
            // $url = "javascript:itboye.top_back();";
            // $this->assign('_g_ret_url',$url);
//        }




        $menu = MenuHelper::getFMenu($this->_uid);
        if($this->isMobile){
        }
        // $this->assign("is_mobile",$this->isMobile);
        $top_menu  = [];
        $top_mid   = $this->_top_mid;
        foreach ($menu as $k=>$v) {
            if(!$v['parent']){
                $top_menu[$v['id']] = $v;
                unset($menu[$k]);
            }
        }
        isset($top_menu[$top_mid]) && $top_menu[$top_mid]['active'] =1;

        $left_menu = [];
        $left_mid  = $this->_left_mid;
        if($top_mid){
            $left_mids = [];
            foreach ($menu as $k=>$v) {
                if($v['parent'] == $top_mid){
                    $left_mids[]         = $v['id'];
                    $v['child']          = [];
                    $left_menu[$v['id']] = $v;
                    unset($menu[$k]);
                }
            }
            if($left_menu){
                foreach ($menu as $v) {
                    if(in_array($v['parent'],$left_mids) && isset($left_menu[$v['parent']])){
                        if($left_mid == $v['id']){
                            $left_menu[$v['parent']]['active'] = 1;
                            $v['active'] = 1;
                        }
                        $left_menu[$v['parent']]['child'][] = $v;
                    }
                }
            }
        }
        $this->assign('_top_mid',$top_mid);
        $this->assign('_left_mid',$this->_left_mid);
        $this->assign('_top_menu',array_values($top_menu));
        $this->assign('_left_menu',array_values($left_menu));
        if(session('?_msg_num')){
            $this->_msg_num = session('_msg_num') ? session('_msg_num') : 0;
        }else{
            $this->_msg_num = (new MessageLogic)->countNew();
        }
        $this->assign('_msg_num',$this->_msg_num);

        // $skinCode = AdminConfigHelper::getValue('BY_SKIN');
        $this->assign('_skin','df'); //AdminFunctionHelper::getBySkin($skinCode))
        $this->assign('_uinfo',AdminSessionHelper::getUserInfo());
    }

    /**
     * 设置当前激活状态的菜单
     */
    private function setActiveMenu(){
        // 当前顶部导航
        $top_mid = input('_top_mid/d',0);
        if ($top_mid>0) {
            session('_top_mid',$top_mid);
            $this->_top_mid = $top_mid;
        }else{
            $this->_top_mid = session('_top_mid') ?: 0;
        }
        // 当前三级导航
        $left_mid = input('_left_mid/d',0);
        if ($left_mid>0) {
            session('_left_mid',$left_mid);
            $this->_left_mid = $left_mid;
        }else{
            $this->_left_mid = session('_left_mid') ?: 0;
        }
    }

    /**
     * 查询当前用户 各角色 菜单id
     */
    private function initUserMenu(){
        $uid = $this->_uid;
        if($uid > 0){
            $current_user_menu = AdminSessionHelper::getCurrentUserMenu();
            if (!empty($current_user_menu)) {
                return 0;
            }

            $menuList = '';
            $r = (new AuthGroupAccessLogic)->queryNoPaging(['uid' => $uid]);
            if ($r['status']) {
                $group_ids = [];
                foreach ($r['info'] as $groupAccess) {
                    $group_ids[] = $groupAccess['group_id'];
                }
                if ($group_ids) {
                    $r = (new AuthGroupLogic)->queryNoPaging([['id','in',$group_ids]]);
                    if ($r['status'] && is_array($r['info'])) {
                        foreach ($r['info'] as $group) {
                            //合并多角色
                            $menuList .= $group['menulist'];
                        }
                    }
                }
            }
            AdminSessionHelper::setCurrentUserMenu($menuList);
        }else{
            $this->error('需要先登陆',url('index/login'),'',2);
        }
    }


    /**
     * @param $key
     * @param string $default
     * @param string $nullMsg  未定义时的报错
     * @return mixed
     */
    public function _param($key,$default=null,$nullMsg=null){
        return $this->checkParamNull(input("param.".$key),$key,$default,$nullMsg);
    }
    public function _post($key,$default=null,$nullMsg=null){
        return $this->checkParamNull(input("post.".$key),$key,$default,$nullMsg);
    }
    public function _get($key,$default=null,$nullMsg=null){
        return $this->checkParamNull(input("get.".$key),$key,$default,$nullMsg);
    }
    public function checkParamNull($val,$key,$df,$nul){
        $name  = preg_replace('/\/\w$/', '', $key);
        if(is_null($val)){
            if(!is_null($nul)){
                $this->error($nul ?: Lack($name));
            }else{
                return $df;
            }
        }
        return $val;
    }

    //== 其它方法
    /**
     * 分页查询结果处理
     */
    public function queryResult($result) {
        if ($result['status']) {
            $this -> assign("show", $result['info']['show']);
            $this -> assign("list", $result['info']['list']);
            return $this->boye_display();
        } else {
            LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
            $this -> error($result['info']);
        }
    }

    //===========================通用CRUD操作方法





    /**
     * 更新保存，根据主键默认id
     * 示列url:
     * /Admin/Menu/save/id/33
     * id必须以get方式传入
     * @param $logic
     * @param string $primarykey
     * @param null $entity
     * @param bool $redirect_url
     */
    protected function save($logic=null,$primarykey = 'id', $entity = null, $redirect_url = false) {
        if (IS_POST) {
            if ($redirect_url === false) {
                $redirect_url = url(CONTROLLER_NAME . '/index');
            }
            if (is_null($entity)) {
                $entity = $this->request->param();
            }

            $id = $this->_param($primarykey, 0);
            if(method_exists($logic,"saveByID")){

                if(isset($entity[$primarykey])){
                    unset($entity[$primarykey]);
                }

                $result = $logic->saveByID($id, $entity);

            }else{
                $result = ResultHelper::error('Admin.php error logic param');
            }

            if ($result['status'] === false) {
                LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
                $this -> error($result['info']);
            } else {
                $this -> success(L('RESULT_SUCCESS'), $redirect_url);
            }
        } else {
            $this -> error("不支持get方式save");
        }
    }

    /**
     * 删除
     * @param 删除成功后跳转|bool $success_url 删除成功后跳转
     */
    public function _delete($logic,$success_url = false) {
        if ($success_url === false) {
            $success_url = url('Admin/' . CONTROLLER_NAME . '/index');
        }

        $map = array('id' => $this->_param('id', -1));

        $result = ResultHelper::error('logic 缺失delete方法');
        if(method_exists($logic,"delete")){
            $result = $logic->delete($map);
        }

        if ($result['status'] === false) {
            $this -> error($result['info']);
        } else {
            $this -> success(L('RESULT_SUCCESS'), $success_url);
        }
    }

    /**
     * 启用
     * @param BaseLogic $logic
     * @param string|boolean $success_url 删除成功后跳转
     * @param string $pk
     */
    public function _enable($logic,$success_url = false,$pk="id") {
        if ($success_url === false) {
            $success_url = url('Admin/' . CONTROLLER_NAME . '/index');
        }

        $map = array($pk => $this->_param($pk, -1));

        $result = ResultHelper::error('logic 缺失enable方法');
        if(method_exists($logic,"enable")){
            $result = $logic->enable($map);
        }

        if ($result['status'] === false) {
            $this -> error($result['info']);
        } else {
            $this -> success(L('RESULT_SUCCESS'), $success_url);
        }
    }

    /**
     * 禁用
     * @param BaseLogic $logic
     * @param string|boolean $success_url 删除成功后跳转
     * @param string $pk
     */
    public function _disable($logic,$success_url = false,$pk="id") {
        if ($success_url === false) {
            $success_url = url('Admin/' . CONTROLLER_NAME . '/index');
        }

        $map = array($pk => $this->_param($pk, -1));

        $result = ResultHelper::error('logic 缺失disable方法');
        if(method_exists($logic,"disable")){
            $result = $logic->disable($map);
        }

        if ($result['status'] === false) {
            $this -> error($result['info']);
        } else {
            $this -> success(L('RESULT_SUCCESS'), $success_url);
        }
    }
}