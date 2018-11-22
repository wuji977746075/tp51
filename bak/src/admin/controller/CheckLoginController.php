<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

namespace app\src\admin\controller;

use app\src\admin\helper\AdminSessionHelper;
use app\src\base\helper\SessionHelper;
use app\src\user\action\LoginAction;
use app\src\user\facade\DefaultUserFacade;

class CheckLoginController extends BaseController{
  protected $_uid = 0;
	//初始化
	protected function initialize(){

    parent::initialize(); // $this->session_id
    $this->_uid = (int) AdminSessionHelper::isLogin();
    if($this->_uid < 1 || ($this->session_id).$this->_uid != session('session_id')){
      //   $auto_login_code = AdminSessionHelper::getAutoLoginCode();
      //   $error = L('ERR_SESSION_TIMEOUT');
      //     if ($uid > 0 && !empty($auto_login_code)) {
      //         $result = (new LoginAction())->autoLogin($uid,$auto_login_code);
      //     if($result['status']){
      //         if(!defined("UID")) define('UID',$uid);
      //         //须return
      //         return;
      //     }else{
      //       $error = $result['info'];
      //     }
    	//   }
      if(IS_AJAX){
        return ajaxReturnErr('登陆已过期',url('index/login'));
      }else{
        $this->error('需要先登陆',url('index/login'),'',2);
      }
    }
	}
}
