<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

namespace  app\admin\controller;

use app\src\admin\api\po\SecurityCodePo;
use app\src\admin\api\SecurityCodeApi;
use app\src\admin\api\UserApi;
use app\src\admin\controller\BaseController;
use app\src\admin\helper\AdminConfigHelper;
use app\src\admin\helper\AdminFunctionHelper;
use app\src\admin\helper\AdminSessionHelper;
use app\src\securitycode\model\SecurityCode;
use app\src\user\action\UserLogoutAction;
use app\src\user\enum\LoginDeviceType;
use think\Request;
use app\src\base\helper\ConfigHelper;
use app\src\user\logic\VUserInfoLogic;
/**
 * Class PublicController
 * @package Admin\Controller
 */
class Index extends BaseController {

    /**
     * 测试账号
     */
    private $test_account = [
        'itboye'   =>['pwd'=>'123456','roledesc'=>'总管理员']
    ];

	/**
	 * 注销\登出
	 */
	public function logout() {

        // $uid = AdminSessionHelper::getUserId();
        // $auto_login_code = AdminSessionHelper::getAutoLoginCode();
        // (new UserLogoutAction())->logout($uid,$auto_login_code);
        if(IS_AJAX){
            //会话
            AdminSessionHelper::logout();
            return ajaxReturnSuc('退出成功',url('index/login'));
        }else if(IS_GET){
            //会话
            AdminSessionHelper::logout();
            $this -> redirect(url('index/login'));
        }

	}

	/**
	 * 登录检测
	 */
	public function checkLogin() {
	    $IS_DEBUG = true;//AdminConfigHelper::getValue("app_debug");

        $verify = trim(input('post.verify', ''));
        if(!$IS_DEBUG){
            $result = $this -> check_verify($verify);
            if(!$result['status']){
                if(IS_AJAX){
                    return ajaxReturnErr(L('ERR_VERIFY'),url('index/login'));
                }else{
                    $this->error(L('ERR_VERIFY'),url('index/login'));
                }
            }
        }
        $uname = input('post.uname', '');
        $upass = input('post.upass', '');
        if($IS_DEBUG && isset($this->test_account[$uname])){
            $upass = $this->test_account[$uname]['pwd'];
        }

        //TODO: 根据浏览器信息生成一个设备标识
        // $result = UserApi::login($uname, $upass, $this->session_id, LoginDeviceType::PC, "+86");
        $salt = ConfigHelper::getPasswordSalt();
        $pwd  = think_ucenter_md5($upass,$salt);
        $map = ["country_no"=>"+86",'username'=>$uname];//,'password'=>$pwd];
        $logic = new VUserInfoLogic;
        $r = $logic->getInfo($map);
        //调用成功
        if ($r['status'] && is_array($r['info'])) {
            $user = $r['info'];
            $user['_username'] = $uname;

            AdminSessionHelper::setLoginUserInfo($user);
            session('session_id',$this->session_id . $user['id']);
            if(IS_AJAX){
                return ajaxReturnSuc('登陆成功',url('manager/index',['_top_mid'=>164,'_left_mid'=>0]));
            }else{
                $this->redirect(url('admin/manager/index',['_top_mid'=>164,'_left_mid'=>0]));
            }
        } else {
            if(IS_AJAX){
              return ajaxReturnErr($r['info'],url('index/login'));
            }else{
              $this -> error($r['info'],url('index/login'));
            }
        }
	}

    private function check_verify($code)
    {
        $acceptor = AdminSessionHelper::getSessionId();
        $po = new SecurityCodePo();
        $po->setAcceptor($acceptor);
        $po->setCodeType(SecurityCode::TYPE_FOR_LOGIN);
        $po->setCode($code);
        $api = new SecurityCodeApi();
        return $api->check($po);
    }

	/**
	 * GET 登录
	 * POST 登录验证
	 */
	public function login() {
		$this -> assignTitle("账号-登录");
        return $this->show();
	}

	/**
	 * 注册页面
	 *
	 * @return 注册页面
	 * @author beibei hebiduhebi@126.com
	 */
	public function register() {
		$this -> assignTitle("账号-注册");
        return $this->show();
	}

	/**
	 * 找回密码
	 * @author beibei hebiduhebi@126.com
	 */
	public function forgotPassword() {
		$this -> assignTitle("账号-忘记密码");
		$this -> error("Not implement!");
	}

    protected function initialize() {
        parent::initialize();
        //获取数据库全部配置
        AdminConfigHelper::init();

        $seo = [
            'title'       => AdminConfigHelper::getValue('WEBSITE_TITLE'),
            'keywords'    => AdminConfigHelper::getValue('WEBSITE_KEYWORDS'),
            'description' => AdminConfigHelper::getValue('WEBSITE_DESCRIPTION')
        ];
        $cfg = [
            'owner' => AdminConfigHelper::getValue('WEBSITE_OWNER'),
            'statisticalcode' => AdminConfigHelper::getValue('WEBSITE_STATISTICAL_CODE'),
            'theme' => AdminFunctionHelper::getSkin(AdminConfigHelper::getValue('DEFAULT_SKIN'))
        ];

        if (!defined("APP_VERSION")) {
            //定义版本
            if (defined("APP_DEBUG") && APP_DEBUG) {
                define("APP_VERSION", time());
            } else {
                define("APP_VERSION", AdminConfigHelper::getValue('APP_VERSION'));
            }
        }

        //
        $this->assignVars($seo, $cfg);
    }
}
