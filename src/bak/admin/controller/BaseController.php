<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */
namespace app\src\admin\controller;

use think\Controller;
use think\Exception;

class BaseController extends Controller {

    protected $session_id;
    protected $seo = [
        'title'       =>'',
        'keywords'    =>'',
        'description' =>'',
    ];
    protected $cfg = [
        'owner'      =>'',
        'theme'      =>'simplex'
    ];

    protected function initialize(){
        parent::initialize();

        //设置程序版本
        $this->_assignVars();
        $this->_defined();
        session("?session_id");
        $this->session_id = session_id();
        if(empty($this->session_id)){
            throw  new Exception("Session 未初始化");
        }
    }


    public function assignVars($seo=['title'=>'标题','keywords'=>'关键词','description'=>'描述',],	$cfg=['owner'=>'杭州博也网络科技有限公司'])
    {
        $this->_assignVars($seo);
    }
        /*
         * Seo 配置
         * */
	public function _assignVars($seo=['title'=>'标题','keywords'=>'关键词','description'=>'描述',],	$cfg=['owner'=>'杭州博也网络科技有限公司']){
		$this->seo = array_merge($this->seo,$seo);
		$this->cfg = array_merge($this->cfg,$cfg);

		$this->assign("seo",$this->seo);
		$this->assign("cfg",$this->cfg);
	}
	/**
	 * 赋值页面标题值
	 */
	public function assignTitle($title){
		$this->seo = array_merge($this->seo,['title'=>$title]);
		$this->assign("seo",$this->seo);
	}

	protected function _defined(){
        !defined("CONTROLLER_NAME") && define("CONTROLLER_NAME", $this->request->controller());
        !defined("ACTION_NAME") && define("ACTION_NAME", $this->request->action());
        !defined("IS_AJAX") && define("IS_AJAX", $this->request->isAjax());
        !defined("IS_POST") && define("IS_POST", $this->request->isPost());
        !defined("IS_GET")  && define("IS_GET", $this->request->isGet());
    }

	/* 空操作，用于输出404页面 */
    protected function _empty() {
    	header('HTTP/1.1 404 Not Found');
		header("status: 404 Not Found");
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		if(!defined('DEBUG')){
			header('Location: '.__ROOT__. '/public/404.html');
		}else{
			echo '{"status": "404","msg": "resource not found!"}';
			exit();
		}
    }


    public function show($file=false,$theme='new'){
        if(!empty($file)){
            return $this->fetch($theme.'/'.$file);
        }else{
            return $this->fetch($theme."/". $this->request->controller().'/'.$this->request->action());
        }
    }


    public function boye_display($file=false,$theme='default'){
        if(!empty($file)){
            return $this->fetch($theme.'/'.$file);
        }else{
            return $this->fetch($theme."/". $this->request->controller().'/'.$this->request->action());
        }
    }
}