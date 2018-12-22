<?php
namespace app\index\domain;

use src\user\user\UserLogic as Logic;

// use src\user\member\MemberConfig;
// use app\src\base\enum\ErrorCode;
// use app\src\base\helper\ConfigHelper;
// use app\src\base\helper\ValidateHelper;
// use app\src\i18n\helper\LangHelper;
// use app\src\securitycode\logic\SecurityCodeLogic;
// use app\src\securitycode\model\SecurityCode;
// use app\src\user\action\DeleteAction;
// use app\src\user\action\LoginAction;
// use app\src\user\action\RegisterAction;
// use app\src\user\action\UpdateAction;
// use app\src\user\enum\RegFromEnum;
// use app\src\user\enum\RoleEnum;
// use app\src\user\facade\DefaultUserFacade;
// use app\src\user\logic\MemberLogic;
// use app\src\user\logic\MemberConfigLogic;
// use app\src\user\logic\UcenterMemberLogic;
// use app\src\user\model\UcenterMember;

// use app\src\repairerApply\logic\RepairerApplyLogicV2;
// use app\src\wallet\logic\ScoreHisLogicV2;
// use think\Db;
/**
 * 用户个人资料相关
 * Class UserDomain
 * @author hebidu <email:346551990@qq.com>
 * @package app\domain
 */
final class UserDomain extends BaseDomain {
    protected function _init(){
      $this->logic = new Logic;
    }
    // 用户登录 : 返回 sid
    public function token() {
      $this->checkVersion();
      $params = $this->parsePost('username,password');
      extract($params);
      $uinfo = $this->logic->checkUser($username,$password);
      // $sid = $this->logic->login($uinfo['id']); //session
      $sid = $this->buildToken($uinfo['id']); //cache
      $this->suc($sid);
    }














    /**
     * 技工 - 注册申请
     * @Author
     * @DateTime 2017-01-05T14:22:39+0800
     * @return   [type]                   [description]
     */
    public function workerReg(){
        $mobile = $this->_post('mobile','',Llack('mobile'));
        $idcode = $this->_post('idcode','');
        // 邀请人
        if($idcode){
            $r = (new MemberConfigLogic)->getInfo(['idcode'=>$idcode]);
            $this->exitWhenError($r);
            empty($r['info']) && $this->apiReturnErr('未知邀请码');
            $invite_id = intval($r['info']['uid']);
        }else{
            $invite_id = 0;
        }
        $r = (new RepairerApplyLogicV2())->addApply($mobile,$invite_id);
        $this->exitWhenError($r,true);
    }
    /**
     * 自动登录接口 - 目前主要用于刷新
     * @author hebidu <email:346551990@qq.com>
     */
    public function autoLogin(){

       // $this->apiReturnErr("自动登录接口去除",ErrorCode::Api_Need_Update);
       $uid = $this->_post('uid','',lang('id_need'));
       $auto_login_code = $this->_post('auto_login_code','',lang('auto_login_code_need'));

       $r = (new DefaultUserFacade())->autoLogin($uid,$auto_login_code);
       if(!$r['status']){
         $this->apiReturnErr($r['info'],ErrorCode::Api_Need_Login);
       }
       $this->exitWhenError($r,true);
    }

    /**
     * 更新用户信息接口
     * @author hebidu <email:346551990@qq.com>
     */
    public function update(){
        $this->checkVersion("100");

        $uid = $this->_post('uid','',lang('lack_parameter',['param'=>'uid']));
        $nickname = $this->_post('nickname','');
        $sex   = $this->_post('sex','');
        $sign  = $this->_post('sign','');//个性签名
        $email = $this->_post('email','');//邮箱

        $loc_country = $this->_post('loc_country','');
        $loc_area    = $this->_post('loc_area','');
        $realname    = $this->_post('realname','');
        $idnumber    = $this->_post('idnumber','');

        $weixin    = $this->_post('weixin','');
        $job_title = $this->_post('job_title','');//职位
        $company   = $this->_post('company','');//公司
        // $head   = $this->_post('head','');//头像

        //1. member_config 表字段更新
        $member_config = [];

        if(!empty($job_title)){
            $member_config['job_title'] = $job_title;
        }
        if(!empty($company)){
            $member_config['company'] = $company;
        }
        if(!empty($weixin)){
            $member_config['weixin'] = $weixin;
        }

        if(!empty($loc_country)){
            $member_config['loc_country'] = $loc_country;
            $member_config['loc_area'] = $loc_area;
        }

        //1. common_member 表字段更新
        $user_entity = [];
        // if(!empty($head)){
        //     $user_entity['head'] = $head;
        // }
        if(!empty($idnumber)){
            $user_entity['idnumber'] = $idnumber;
        }
        if(!empty($realname)){
            $user_entity['realname'] = $realname;
        }
        if(!empty($nickname)){
            $user_entity['nickname'] = $nickname;
        }
        if(strlen($sex) > 0){
            $sex = intval($sex);
            if($sex == 0 || $sex == 1){
                $user_entity['sex'] = $sex;
            }
        }
        if(!empty($sign)){
            $user_entity['sign'] = $sign;
        }
        $ucenter_entity = [];
        if(!empty($email)){
            $ucenter_entity['email'] = $email;
        }

        $action = new UpdateAction();

        $entity = array(
            'ucenter_entity' =>$ucenter_entity,
            'user_entity'    =>$user_entity,
            'member_config'  =>$member_config,
            'uid'            =>$uid
        );
        $result = $action->update($entity);

        $this->exitWhenError($result,true);
    }

    /**
     * 找回密码-通过旧密码的形式
     * @author hebidu <email:346551990@qq.com>
     */
    public function updatePwdByOldPwd(){

        $this->checkVersion("100");

        $uid = $this->_post('uid','', lang('uid_need'));
        $password = $this->_post('password','', lang('password_need'));
        $new_password = $this->_post('new_password','', lang('password_need'));

        $salt = ConfigHelper::getPasswordSalt();
        $crypt_password = think_ucenter_md5($password,$salt);

        $logic = new UcenterMemberLogic();
        $result = $logic->getInfo(['id'=>$uid]);
        if(!$result['status'] || empty($result['info'])){
            $this->apiReturnErr(lang('invalid_parameter',['param'=>'uid']));
        }

        $userinfo = $result['info'];
        if($userinfo['password'] != $crypt_password){
            $this->apiReturnErr(lang('err_incorrect_password'));
        }


        //2. 调用修改密码
        $action = new UpdateAction();
        $result = $action->updatePwd(['id'=>$uid,'password'=>$crypt_password],$new_password);

        $this->exitWhenError($result,true);
    }

    /**
     * 找回密码-通过手机号+手机验证码的形式
     * @author hebidu <email:346551990@qq.com>
     */
    public function updatePwd(){

        $this->checkVersion("101");

        $country = $this->_post('country','', lang('country_tel_number_need'));
        $code = $this->_post('code','', lang('code_need'));
        $mobile = $this->_post('mobile','', lang('mobile_need'));
        $password = $this->_post('password','', lang('password_need'));

        if(!ValidateHelper::isMobile($mobile)) {
            $this->apiReturnErr(lang('invalid_parameter',['param'=>'mobile']));
        }

        //1. 校验验证码
        $securityCodeLogic = new SecurityCodeLogic();
        $result = $securityCodeLogic->isLegalCode($code,$country . $mobile,SecurityCode::TYPE_FOR_UPDATE_PSW,$this->client_id);
        $this->exitWhenError($result);

        //2. 调用修改密码
        $action = new UpdateAction();
        $result = $action->updatePwd(['country_no'=>$country,'mobile'=>$mobile],$password);

        $this->exitWhenError($result,true);
    }

    /**
     * 通过5分钟时效的授权码来修改密码
     * @author hebidu <email:346551990@qq.com>
     */
    public function updatePwdWithAuthCode(){

        $uid = $this->_post('uid','',lang('id_need'));
        $encryptPwd = $this->_post('password','',lang('password_need'));
        $auth_code = $this->_post('auth_code','',lang('invalid_parameter',['param'=>'auth_code']));
        $new_pwd  = $this->_post('new_pwd','',lang('invalid_parameter',['param'=>'new_pwd']));

        $logic  = new UcenterMemberLogic();

        $result = $logic->getInfo(['id'=>$uid,'password'=>$encryptPwd]);

        if(!$result['status']){
            $this->apiReturnErr($result['info']);
        }

        $userinfo = $result['info'];

        if(!is_array($userinfo)){
            $this->apiReturnErr(lang('err_modified'));
        }

        if($auth_code != "itboye" && empty(think_ucenter_decrypt($auth_code,$uid))){
            $this->apiReturnErr(lang('err_auth_code'));
        }

        $action = new UpdateAction();
        $result = $action->updatePwd(['id'=>$uid],$new_pwd);

        $this->exitWhenError($result,true);
    }

    /**
     * 登录接口
     * 101: 增加角色参数
     * 102: 返回角色信息
     * @author hebidu <email:346551990@qq.com>
     */
    public function loginByCode(){

        $this->checkVersion(["102"],"返回角色信息");
        $country = $this->_post('country','');
        $mobile = $this->_post('mobile','',lang('mobile_need'));
        $code = $this->_post('code','');
        $role = $this->_post('role','',lang('mobile_need'));

        $device_token = $this->_post('device_token','');
        $device_type  = $this->_post('device_type','');

        $action = new LoginAction();
        $result = $action->loginByCode($this->client_id,$mobile,$code,$country,$device_token,$device_type,$role);

        $this->exitWhenError($result,true);

    }

    /**
     * 登录接口
     * 103: 支持分角色登录
     * 104: 返回角色信息
     * @author hebidu <email:346551990@qq.com>
     */
    public function login(){
        $this->checkVersion(["104"],"返回角色信息");

        $country = $this->_post('country','');
        $role = $this->_post('role','',LangHelper::lackParameter("role"));
        $username = $this->_post('username','',LangHelper::lackParameter("username"));
        $password = $this->_post('password','',LangHelper::lackParameter("password"));

        $device_token = $this->_post('device_token','');
        $device_type  = $this->_post('device_type','');

        $action = new LoginAction();
        $result = $action->login($username,$password,$country,$device_token,$device_type,$role);

        $this->exitWhenError($result,true);
    }

    /**
     * 注册接口
     * @author hebidu <email:346551990@qq.com>
     */
    public function register(){

        $this->checkVersion("102");

        $nickname = $this->_post('nickname','');
        $country = $this->_post('country','');
        $code = $this->_post('code','', lang('code_need'));

        $username = $this->_post('username','', lang('username_need'));
        $password = $this->_post('password','', lang('password_need'));
        $email = $this->_post('email', '');
        $reg_from = $this->_post('reg_from',RegFromEnum::SYSTEM);
        $idcode = $this->_post('idcode', '');

        $reg_type = UcenterMember::ACCOUNT_TYPE_USERNAME;
        $mobile = "";


        if(ValidateHelper::isMobile($username)) {
            if(empty($country)){
                $this->apiReturnErr(lang('country_tel_number_need'));
            }

            $mobile = $username;
            $username = 'm_' . $mobile;
            $reg_type = UcenterMember::ACCOUNT_TYPE_MOBILE;
        }

        //mobile必需
        if(empty($mobile)){
            $this->apiReturnErr(lang('mobile_need'));
        }
        if(empty($email)){
            $email = $username.'@itboye.com';
        }

        $entity = [
            'nickname'  =>$nickname,
            'username'  =>$username,
            'password'  =>$password,
            'mobile'    =>$mobile,
            'email'     =>$email,
            'country'   =>$country,
            'reg_from'  =>$reg_from,
            'reg_type'  =>$reg_type,
            'role_code' =>RoleEnum::ROLE_Driver
        ];

        // 邀请人
        if($idcode){
            $r = (new MemberConfigLogic)->getInfo(['idcode'=>$idcode]);
            $this->exitWhenError($r);
            empty($r['info']) && $this->apiReturnErr('未知邀请码');
            $invite_id = intval($r['info']['uid']);
            $entity['invite_id'] = $invite_id;
        }else{
            $invite_id = 0;
        }
        //1. 校验验证码
        $securityCodeLogic = new SecurityCodeLogic();
        $r = $securityCodeLogic->isLegalCode($code,$country . $mobile,SecurityCode::TYPE_FOR_REGISTER,$this->client_id);
        $this->exitWhenError($r);

        //2. 调用注册操作
        $action = new RegisterAction();
        Db::startTrans();
        $r = $action->register($entity);
        if(!$r['status']){
            Db::rollback();
            $this->apiReturnErr($r['info']);
        }
        $uid = intval($r['info']);
        if($uid > 0){
            // 注册添加积分
            if($invite_id){ // 被邀请注册 添加邀请积分
                $type = ScoreHisLogicV2::REG_INVITED_ADD;
                // 邀请人加积分
                $r = (new ScoreHisLogicV2)->changeScoreByRule($invite_id,ScoreHisLogicV2::REG_INVITER_ADD,true);
                if(!$r['status']){
                    Db::rollback();
                    $this->apiReturnErr($r['info']);
                }
            }else{ // 普通注册
                $type = ScoreHisLogicV2::REG_ADD;
            }
            $r = (new ScoreHisLogicV2)->changeScoreByRule($uid,$type,true);
            if(!$r['status']){
                Db::rollback();
                $this->apiReturnErr($r['info']);
            }

            Db::commit();
            $this->apiReturnSuc(lang("success"));
        }else{
            Db::rollback();
            $this->apiReturnErr(lang("fail"));
        }
    }

    /**
     * 删除
     * @author hebidu <email:346551990@qq.com>
     */
    public function delete(){
        $this->checkVersion("100");
        $mobile = $this->_post('mobile','',lang('mobile_need'));

        $logic = new DeleteAction();
        $result = $logic->delete($mobile);
        $this->exitWhenError($result,true);
    }

    /**
     * 更新用户的经纬度
     * 100:
     */
    public function updateLatLng(){
        $id = $this->_post('id','',LangHelper::lackParameter('id'));
        //维度、经度
        $lat = $this->_post('lat','',LangHelper::lackParameter('lat'));
        $lng = $this->_post('lng','',LangHelper::lackParameter('lng'));

        $result = (new MemberLogic())->save(['uid'=>$id],['lat'=>$lat,'lng'=>$lng]);

        $this->returnResult($result);
    }
}