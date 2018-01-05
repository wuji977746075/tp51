<?php
/**
 * 周舟 hzboye010@163.com
 * addby sublime snippets
 * User测试类
 */

namespace app\test\controller;

use app\extend\BoyeService;
use first\foo;
use xsocketlog\socketlog;

class User extends Ava {

    // //关注用户
    //  public function focus(){
    //    if(IS_AJAX){
    //      $data = [
    //        'uid'       => input('post.uid',''),
    //        'focus_uid' => input('post.focus_uid',''),
    //        'value'     => input('post.value',''),
    //        'alg'       => $this->alg,
    //        'type'      => 'BY_User_focus',
    //      ];

    //      $service = new BoyeService();
    //      $result  = $service->callRemote("",$data,false);
    //      return $this->parseResult($result);
    //    }else{
    //      $this->assign('type','BY_User_focus');
    //      $this->assign('field',[
    //        ['api_ver','100',LL('need-mark api version')],
    //        ['uid',42,LL('need-mark user ID')],
    //        ['focus_uid',50,LL('need-mark focus user ID')],
    //        ['value',1,LL('need-mark focus (1:yes[default],0:no)')],
    //      ]);
    //      return $this->fetch('ava/test');
    //    }
    //  }

    //  //天降横财
    //  public function testMoney(){
    //    if(IS_AJAX){
    //      $data = [
    // 			'uid'   => input('post.uid',''),
    // 			'money' => input('post.money',''),
    //        'api_ver'   => $this->api_ver,
    //        'notify_id' => $this->notify_id,
    //        'alg'       => $this->alg,
    //        'type'      => 'BY_User_testMoney',
    //      ];

    //      $service = new BoyeService();
    //      $result = $service->callRemote("",$data,false);
    //      return $this->parseResult($result);
    //    }else{
    //      $this->assign('type','BY_User_testMoney');
    //      $this->assign('field',[
    //        ['api_ver','100',LL('need-mark api version')],
    //        ['uid',143,LL('need-mark user ID')],
    //        ['money',50,LL('need-mark focus user ID')],
    //      ]);
    //      return $this->fetch('ava/test');
    //    }
    //  }

    //手机验证码登陆
    public function loginByCode() {
        if (IS_AJAX) {
            $data = [
                'mobile'  => input('post.mobile', 0),
                'code'    => input('post.code', 0),
                'api_ver' => $this->api_ver,
                'type'    => 'BY_User_loginByCode',
            ];

            $service = new BoyeService();
            $result  = $service->callRemote("", $data, false);
            return $this->parseResult($result);

        } else {
            $this->assign('type', 'BY_User_loginByCode');
            $this->assign('code_type', 5);//验证码类型
            $this->assign('field', [
                ['api_ver', $this->api_ver, LL('need-mark api version')],
                ['mobile', '17195862186', LL('need-mark mobile')],
                ['code', '', LL('need-mark validate-code')],
            ]);
            return $this->fetch('register');
        }
    }

    //手机换绑
    public function changePhone() {
        if (IS_AJAX) {
            $data = [
                'uid'      => input('post.uid', 0),
                'mobile'   => input('post.mobile', 0),
                'code'     => input('post.code', 0),
                'password' => input('post.password', 0),
                'api_ver'  => $this->api_ver,
                'type'     => 'BY_User_changePhone',
            ];

            $service = new BoyeService();
            $result  = $service->callRemote("", $data, false);
            return $this->parseResult($result);
        } else {
            $this->assign('type', 'BY_User_changePhone');
            $this->assign('code_type', 4);//验证码类型
            $this->assign('field', [
                ['api_ver', $this->api_ver, LL('need-mark api version')],
                ['uid', '', LL('need-mark user ID')],
                ['mobile', '17195862186', LL('need-mark mobile')],
                ['code', '', LL('need-mark validate-code')],
                ['password', '111111', LL('need-mark password')],
            ]);
            return $this->fetch('register');
        }
    }
    // //绑定提现账号 - 仅本站保存下
    //  public function bindWithdraw(){
    //    if(IS_AJAX){
    //        $data = [
    // 				'uid'          => input('post.uid',''),
    // 				'mobile'       => input('post.mobile',''),
    // 				'realname'     => input('post.realname',''),
    // 				'account_type' => input('post.account_type',''),
    // 				'code'         => input('post.code',''),
    // 				'account'      => input('post.account',''),
    // 				'api_ver'   =>$this->api_ver,
    // 				'notify_id' =>$this->notify_id,
    // 				'alg'       =>$this->alg,
    // 				'type'      =>'BY_User_bindWithdraw',
    //        ];

    //        $service = new BoyeService();
    //        $result = $service->callRemote("",$data,false);
    //        return $this->parseResult($result);
    //    }else{
    //    	$this->assign('type','BY_User_bindWithdraw');
    //    	$this->assign('code_type',6);//验证码类型
    // 		$this->assign('field',[
    // 			['api_ver',$this->api_ver,LL('need-mark api version')],
    // 			['account',17195862185,LL('need-mark accout')],
    // 			['realname','',LL('need-mark realname')],
    // 			['code','',LL('need-mark validate-code')],
    // 			['mobile',17195862186,LL('need-mark phone-number')],
    // 			['uid',50,LL('need-mark user ID')],
    // 			['account_type',1,LL('need-mark accout-type')],
    // 		]);
    //    	return $this->fetch('register');
    //    }
    //  }
    //手机绑定并设置密码 - 微信登陆用
    public function bind() {
        if (IS_AJAX) {
            $data = [
                'uid'      => input('post.uid', 0),
                'mobile'   => input('post.mobile', 0),
                'code'     => input('post.code', 0),
                'password' => input('post.password', 0),
                'api_ver'  => $this->api_ver,
                'type'     => 'BY_User_bind',
            ];

            $service = new BoyeService();
            $result  = $service->callRemote("", $data, false);
            return $this->parseResult($result);
        } else {
            $this->assign('type', 'BY_User_bind');
            $this->assign('code_type', 3);//验证码类型
            $this->assign('field', [
                ['api_ver', $this->api_ver, LL('need-mark api version')],
                ['mobile', '17195862186', LL('need-mark phone-number')],
                ['uid', '42', LL('need-mark user ID')],
                ['password', '123456', LL('need-mark password')],
                ['code', '', LL('need-mark validate-code')],
            ]);
            return $this->fetch('register');
        }
    }

    //手机找回密码
    public function findPswByMobile() {
        if (IS_AJAX) {
            $data = [
                // 'uid'     => input('post.uid', 0),
                'mobile'  => input('post.mobile', ''),
                //'code'    => input('post.code', ''),
                'psw'     => input('post.psw', ''),
                'api_ver' => $this->api_ver,
                'type'    => 'BY_User_findPswByMobile',
            ];


            $service = new BoyeService();
            $result  = $service->callRemote("", $data, false);
            return $this->parseResult($result);
        } else {
            $this->assign('type', 'BY_User_findPswByMobile');
            $this->assign('code_type', 2);//验证码类型
            $this->assign('field', [
                ['api_ver', $this->api_ver, LL('need-mark api version')],
                //  ['uid', '', LL('need-mark user ID')],
                ['mobile', '17195862186', LL('need-mark mobile')],
                //  ['code', '', LL('need-mark validate-code')],
                ['psw', '111111', LL('need-mark new password')],
            ]);
            return $this->fetch('register');
        }
    }

    //根据现密码修改密码
    public function updatePsw() {
        if (IS_AJAX) {
            $data = [
                'uid'     => input('post.uid', 0),
                'old_psw' => input('post.old_psw', 0),
                'psw'     => input('post.psw', 0),
                'api_ver' => $this->api_ver,
                'type'    => 'BY_User_updatePsw',
            ];

            $service = new BoyeService();
            $result  = $service->callRemote("", $data, false);
            return $this->parseResult($result);
        } else {
            $this->assign('type', 'BY_User_updatePsw');
            $this->assign('field', [
                ['api_ver', $this->api_ver, LL('need-mark api version')],
                ['uid', '', LL('need-mark user ID')],
                ['old_psw', '123456', LL('need-mark old password')],
                ['psw', '123456', LL('need-mark new password')],
            ]);
            return $this->fetch('ava/test');
        }
    }

    //用户注册
    public function register() {
        if (IS_AJAX) {
            $data = [
                'username' => input('post.username', ''),
                'password' => input('post.password', ''),
                // 'code'     => input('post.code', ''),
                'reg_type' => input('post.reg_type', 3, 'int'),
                // 'nickname'    => input('post.nickname',''),
                'from'     => input('post.from', 0, 'int'),
                'api_ver'  => $this->api_ver,
                'type'     => 'BY_User_register',
            ];
            $service = new BoyeService();
            $result  = $service->callRemote("", $data, true);
            return $this->parseResult($result);

        } else {
            $this->assign('type', 'BY_User_register');
            $this->assign('code_type', 1);//验证码类型
            $this->assign('field', [
                ['api_ver', $this->api_ver, LL('need-mark api version')],
                ['username', 'rainbow', LL('need-mark username reg username')],

                ['password', '123456', LL('need-mark password')],
                // ['code', '', LL('need-mark mobile validate-code reg-code')],
                // ['nickname','',L('nickname')],
                ['reg_type', 3, LL('need-mark reg-type')],
                ['from', 0, LL('need-mark reg-from www')],
            ]);
            return $this->fetch('ava/test');
        }

    }

    //用户资料更新
    public function update() {
        if (IS_AJAX) {
            $data = [
                'uid'      => input('post.uid', ''),
                // 'mobile'   => input('post.mobile', ''),
                'realname' => input('post.realname', ''),
                'email'    => input('post.email', ''),
                'birthday' => input('post.birthday', ''),
                'nickname' => input('post.nickname', ''),
                'sex'      => input('post.sex', ''),
                'qq'       => input('post.qq', ''),
                // 'alipay'    => input('post.alipay',''),
                // 'head'      => input('post.head',''),
                // 'idnumber' => input('post.idnumber', ''),
                'sign'     => input('post.sign', ''),
                'api_ver'  => $this->api_ver,
                'type'     => 'BY_User_update',
            ];

            $service = new BoyeService();
            $result  = $service->callRemote("", $data, false);
            return $this->parseResult($result);
        } else {
            $this->assign('type', 'BY_User_update');
            $this->assign('field', [
                ['api_ver', '100', LL('need-mark api version')],
                ['uid', 11, LL('need-mark user ID')],
                // ['mobile', '', L('mobile')],
                ['realname', '', L('realname')],
                ['email', '', L('email')],
                ['birthday', '', L('birthday')],
                ['nickname', '', L('nickname')],
                ['sex', '', L('reg-sex')],
                ['qq', '', L('qq')],
                // ['alipay','',L('alipay-account')],
                // ['head','',L('head')],
                // ['idnumber', '', L('idnumber')],
                ['sign', '', LL('user sign')],
            ]);
            return $this->fetch('ava/test');
        }
    }

    //用户登陆
    public function login() {
        if (IS_AJAX) {
            $data = [
                'username' => input('post.username', ''),
                "password" => input('post.password', ''),
                'api_ver'  => $this->api_ver,
                'type'     => 'BY_User_login',
            ];

            $service = new BoyeService();
            $result  = $service->callRemote("", $data, false);
            return $this->parseResult($result);
        } else {
            $this->assign('type', 'BY_User_login');
            $this->assign('field', [
                ['api_ver', '100', LL('need-mark api version')],
                ['username', '17195862186', LL('need-mark username or phone-number')],
                ['password', '123456', LL('need-mark password')],
            ]);
            return $this->fetch('ava/test');
        }
    }

    //实名认证 - 列表
    public function verifyList(){
        if (IS_AJAX) {
            $data = [
                'type'     => 'AM_User_verifyList',
                'api_ver'  => $this->api_ver,
                'aid'      => input('post.aid',''),
                'kword'    => input('post.kword',''),
                'order'    => input('post.order',''),
                'current_page' => input('post.current_page', ''),
                'per_page'     => input('post.per_page', ''),
            ];

            $service = new BoyeService();
            $result  = $service->callRemote("", $data, false);
            return $this->parseResult($result);
        } else {
            $this->assign('type', 'AM_User_verifyList');
            $this->assign('field', [
                ['api_ver','100', LL('need-mark api version')],
                ['aid',11,LL('need-mark admin_login_uid')],
                ['current_page',1, L('page-number')],
                ['kword','', L('key-word')],
                ['order','create_time asc', L('order')],
                ['per_page',10, L('page-size')],
            ]);
            return $this->fetch('ava/test');
        }
    }
    //经纪人认证 - 列表
    public function verifyListBroker(){
        if (IS_AJAX) {
            $data = [
                'type'     => 'AM_User_verifyListBroker',
                'api_ver'  => $this->api_ver,
                'aid'      => input('post.aid',''),
                'kword'    => input('post.kword',''),
                'order'    => input('post.order',''),
                'current_page' => input('post.current_page', ''),
                'per_page'     => input('post.per_page', ''),
            ];

            $service = new BoyeService();
            $result  = $service->callRemote("", $data, false);
            return $this->parseResult($result);
        } else {
            $this->assign('type', 'AM_User_verifyListBroker');
            $this->assign('field', [
                ['api_ver','100', LL('need-mark api version')],
                ['aid',11,LL('need-mark admin_login_uid')],
                ['current_page',1, L('page-number')],
                ['kword','', L('key-word')],
                ['order','create_time asc', L('order')],
                ['per_page',10, L('page-size')],
            ]);
            return $this->fetch('ava/test');
        }
    }
    //经纪人认证 - 审核/驳回
    public function verifyBroker(){
        if (IS_AJAX) {
            $data = [
                'type'     => 'AM_User_verifyBroker',
                'api_ver'  => $this->api_ver,
                'aid'      => input('post.aid',''),
                'uid'      => input('post.uid',''),
                'pass'     => input('post.pass',''),
                'msg'      => input('post.msg',''),
            ];

            $service = new BoyeService();
            $result  = $service->callRemote("", $data, false);
            return $this->parseResult($result);
        } else {
            $this->assign('type', 'AM_User_verifyBroker');
            // $this->assign('code_type', 7);
            $this->assign('field', [
                ['api_ver','100', LL('need-mark api version')],
                ['aid',11,LL('need-mark admin_login_uid')],
                ['uid',11,LL('need-mark to_verify_uid')],
                ['pass','1',LL('need-mark house-code')],
                ['msg','not-pass-msg',L('not-pass-message')],
            ]);
            return $this->fetch('ava/test');
        }
    }
    //实名认证 - 审核/驳回
    public function verify(){
        if (IS_AJAX) {
            $data = [
                'type'     => 'AM_User_verify',
                'api_ver'  => $this->api_ver,
                'aid'      => input('post.aid',''),
                'uid'      => input('post.uid',''),
                'pass'     => input('post.pass',''),
                'msg'      => input('post.msg',''),
            ];

            $service = new BoyeService();
            $result  = $service->callRemote("", $data, false);
            return $this->parseResult($result);
        } else {
            $this->assign('type', 'AM_User_verify');
            // $this->assign('code_type', 7);
            $this->assign('field', [
                ['api_ver','100', LL('need-mark api version')],
                ['aid',11,LL('need-mark admin_login_uid')],
                ['uid',11,LL('need-mark to_verify_uid')],
                ['pass','1',LL('need-mark if-pass')],
                ['msg','not-pass-msg',L('not-pass-message')],
            ]);
            return $this->fetch('ava/test');
        }
    }
    //实名认证 - 申请
    public function verifyApply(){
        if (IS_AJAX) {
            $data = [
                'api_ver'  => $this->api_ver,
                'type'     => 'BY_User_verifyApply',
                'uid'      => input('post.uid',''),
                'realname' => input('post.realname', ''),
                'idnumber' => input('post.idnumber', ''),
                'bank_no'  => input('post.bank_no', ''),
                'bank_phone' => input('post.bank_phone', ''),
                // 'code'     => input('post.code', ''),
                // 'imgs'     => input('post.imgs', ''),
            ];

            $service = new BoyeService();
            $result  = $service->callRemote("", $data, false);
            return $this->parseResult($result);
        } else {
            $this->assign('type', 'BY_User_verifyApply');
            // $this->assign('code_type', 7);
            $this->assign('field', [
                ['api_ver','100', LL('need-mark api version')],
                ['uid',11,LL('need-mark uid')],
                ['bank_no','',LL('need-mark bank_no')],
                ['bank_phone','',LL('need-mark bank_phone')],
                ['realname','rainbow', LL('need-mark realname')],
                ['idnumber','340888199008096666', LL('need-mark idnumber')],
                // ['code','666',LL('need-mark verify-code')],
                // ['imgs','1387,1388', LL('need-mark imgs')],//单或多张
            ]);
            return $this->fetch('ava/test');
        }
    }

    public function info(){
        if (IS_AJAX) {
            $data = [
                'uid' => input('post.uid',''),
                'api_ver'  => $this->api_ver,
                'type'     => 'BY_User_info',
            ];
            $service = new BoyeService();
            $result  = $service->callRemote("", $data, false);
            return $this->parseResult($result);
        }else{
            $this->assign('type', 'BY_User_info');
            $this->assign('field', [
              ['api_ver','100', LL('need-mark api version')],
              ['uid',11,LL('need-mark uid')],
            ]);
            return $this->fetch('ava/test');
        }
        //实名认证信息
        //用户角色信息
    }

    public function getCode() {
        $data = [
            'mobile'    => input('mobile', ''),
            'code_type' => input('type', ''),
            'api_ver'   => 100,
            'type'      => 'BY_Message_send_sms',
        ];

        $service = new BoyeService();
        $result  = $service->callRemote("", $data, false);
        return $this->parseResult($result);
    }

    public function infoByMobile(){

        if(IS_AJAX){
            $data = [
                'api_ver' => $this->api_ver,
                'type'    => 'BY_User_infoByMobile',
            ];
            $filter = [
                ['mobile']
            ];

            $data = array_merge($data, $this->getPostParams($filter));
            $service = new BoyeService();
            $result = $service->callRemote("",$data,false);
            $this->parseResult($result);
        }else{
            $this->assign('type','BY_User_infoByMobile');
            $this->assign('field',[
                ['api_ver',100,LL('need-mark api version')],
                ['mobile','18768125386',LL('need-mark')]
            ]);
            return $this->fetch('ava/test');
        }

    }

    //意见反馈
    public function suggest(){
        if(IS_AJAX){
            $data = [
                'api_ver' => $this->api_ver,
                'type'    => 'BY_User_suggest',
            ];
            $filter = [
                ['uid'],
                ['content']
            ];
            $data = array_merge($data, $this->getPostParams($filter));
            $service = new BoyeService();
            $result = $service->callRemote("",$data,false);
            $this->parseResult($result);
        }else{
            $this->assign('type','BY_User_suggest');
            $this->assign('field',[
                ['api_ver',100,LL('need-mark api version')],
                ['uid','97', LL('need-mark')],
                ['content','今天天气真好',LL('need-mark')]

            ]);
            return $this->fetch('ava/test');
        }
    }
}