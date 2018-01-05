<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\weixin\controller;
use think\Controller;
use think\Response;
use think\Request;
abstract class Base extends Controller{
		
	protected $seo = array(
			'title'=>'',
			'keywords'=>'',
			'description'=>'',
	);
	
	protected $cfg = array(
			'owner'=>'',
			'theme'=>'simplex'
	);
	/*
	 * Seo 配置
	 * */
	public function assignVars($seo=array('title'=>'标题','keywords'=>'关键词','description'=>'描述',),	$cfg=array('owner'=>'杭州博也网络科技有限公司')){
		$this->seo = array_merge($this->seo,$seo);
		$this->cfg = array_merge($this->cfg,$cfg);
		
		$this->assign("seo",$this->seo);
		$this->assign("cfg",$this->cfg);
	}
	/**
	 * 赋值页面标题值
	 */
	public function assignTitle($title){
		$this->seo = array_merge($this->seo,array('title'=>$title));
		$this->assign("seo",$this->seo);
	}
	
	//初始化
	protected function _initialize(){
		//设置程序版本

		$this->assignVars();
        $this->_defined();
	}
		
	/* 空操作，用于输出404页面 */
    protected function _empty() {
    	header('HTTP/1.1 404 Not Found'); 
		header("status: 404 Not Found");     	
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		if(!defined('DEBUG')){
			header('Location: '.__ROOT__. '/Public/404.html');
		}else{
			echo '{"status": "404","msg": "resource not found!"}';
			exit();
		}
    }

    protected function _defined(){
        if(!defined("CONTROLLER_NAME")){
            define("CONTROLLER_NAME", Request::instance()->controller());
        }

        if(!defined("ACTION_NAME")){
            define("ACTION_NAME", Request::instance()->action());
        }

        if(!defined("IS_POST")){
            define("IS_POST", Request::instance()->isPost());
        }

        if(!defined("IS_GET")){
            define("IS_GET", Request::instance()->isGet());
        }

        if(!defined("IS_AJAX")){
            define("IS_AJAX", Request::instance()->isAjax());
        }
    }
    /**
     * ajax返回
     * @param $data
     * @internal param $i
     */
    protected function apiReturnSuc($data){

        $this->ajaxReturn(array('status'=>true,'info'=>$data),"json");
    }

    /**
     * ajax返回，并自动写入token返回
     * @param $data
     * @param int $code
     * @internal param $i
     */
    protected function apiReturnErr($data){
        $this->ajaxReturn(array('status'=>false,'info'=>$data),"json");
    }


	private function ajaxReturn($data){
		$response =  Response::create($data, "json")->code(200);
		$response->header("X-Powered-By","WWW.ITBOYE.COM")->send();
		exit;
	}
}
