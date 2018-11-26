<?php
namespace app\src\user\facade;


use app\src\alibaichuan\service\OpenIMUserService;
use app\src\base\enum\StatusEnum;
use app\src\base\facade\IAccount;
use app\src\base\helper\ConfigHelper;
use app\src\base\helper\ExceptionHelper;
use app\src\base\helper\ResultHelper;
use app\src\base\helper\SessionHelper;
use app\src\base\helper\ValidateHelper;
use app\src\powersystem\logic\AuthGroupAccessLogic;
use app\src\securitycode\logic\SecurityCodeLogic;
use app\src\securitycode\model\SecurityCode;
use app\src\session\logic\LoginSessionLogic;
use app\src\user\enum\RegFromEnum;
use app\src\user\enum\RoleEnum;
use app\src\user\logic\MemberConfigLogic;
use app\src\user\logic\MemberLogic;
use app\src\user\logic\UcenterMemberLogic;
use app\src\user\model\UcenterMember;
use think\Db;
use think\Exception;

class DefaultUserFacade implements IAccount
{
    /**
     * 注销用户
     * @param $uid
     * @param string $auto_login_code
     * @return string
     */
    function logout($uid, $auto_login_code="force_logout")
    {
        $update = ['expire_time'=>1];
        $map    = ['uid'=>$uid,'log_session_id'=>$auto_login_code];

        if($auto_login_code == "force_logout"){
            unset($map['log_session_id']);
        }

        $result = (new LoginSessionLogic())->save($map,$update);
        return $result;
    }

    /**
     * 自动登录
     * @param $uid
     * @param $login_session_id
     * @return mixed
     * @internal param $auth_code
     */
    public function autoLogin($uid,$login_session_id) {
        $result = SessionHelper::checkLoginSession($uid,$login_session_id);

        if($result['status']){
            $result['info'] = $this->getInfo($uid);
        }

        return $result;
    }

    /**
     * 获取用户信息
     * @param $id
     * @return mixed
     */
    function getInfo($id)
    {
        $logic = new UcenterMemberLogic();
        $result = $logic->getInfo(['id' => $id]);

        if (!$result['status'] || empty($result['info'])) {
            return ['status' => false, 'info' => lang('err_not_find')];
        }

        $userInfo = $result['info'];

        //获取用户信息
        return $this->getUserInfo($userInfo);
    }

    private function getUserInfo($userInfo)
    {
        $uid = $userInfo['id'];
        $map = ['uid' => $userInfo['id']];
        $memberLogic = new MemberLogic();
        $memberConfigLogic = new MemberConfigLogic();

        $result = $memberLogic->getInfo($map);

        if (!$result['status'] || empty($result['info'])) {
            $err_msg = empty($result['info']) ? "未知(-1)" : $result['info'];

            return ['status' => false, 'info' => $err_msg];
        }

        $userInfo = array_merge($userInfo, $result['info']);

        $result = $memberConfigLogic->getInfo($map);


        if (!$result['status']) {
            $err_msg = empty($result['info']) ? "未知(-2)" : $result['info'];
            return ['status' => false, 'info' => $err_msg];
        }
        $userInfo = array_merge($userInfo, $result['info'] ? $result['info'] : []);


        $userInfo['auth_code'] = think_ucenter_encrypt($userInfo['password'], $userInfo['id'], 900);

        $result = (new LoginSessionLogic())->getInfo(['uid' => $uid]);

        if (!$result['status']) {
            $err_msg = empty($result['info']) ? "未知(-3)" : $result['info'];
            return ['status' => false, 'info' => $err_msg];
        }

        $userInfo['auto_login_code'] = $result['info']['log_session_id'];
        //think_ucenter_encrypt($userInfo['password'],$userInfo['id'],15*24*3600);

        //获取用户角色信息
        $r = (new AuthGroupAccessLogic())->getRolesByUID($uid);
        if (!$r['status']) return $r;
        $userInfo['roles_info'] = $r['info'];

        return ['status' => true, 'info' => $userInfo];
    }

    /**
     * @param $entity
     * @return mixed
     */
    function delete($entity)
    {
        $mobile = $entity['mobile'];
        $error = '';
        $flag = false;
        $ucenterUserLogic  = new UcenterMemberLogic();
        $memberLogic       = new MemberLogic();
        $memberConfigLogic = new MemberConfigLogic();
        $authGroupAccessLogic = new AuthGroupAccessLogic();

        $map = ['mobile'=>$mobile];
        $result = $ucenterUserLogic->getInfo($map);

        $userInfo = [];
        if(!$result['status'] || empty($result['info']) || !is_array($result['info'])){
            return ['status'=>false,'info'=>lang('tip_mobile_unregistered')];
        }

        $userInfo = $result['info'];
        $uid = $userInfo['id'];

        Db::startTrans();
        try{
            $result = $ucenterUserLogic->delete($map);

            if($result['status'] && intval($result['info']) == 1){
                $result = $memberLogic->delete(['uid'=>$uid]);
                if($result['status']){
                    $result = $memberConfigLogic->delete(['uid'=>$uid]);
                }
            }

            if(!$result['status']){
                $flag = true;
                $error = $result['info'];
            }

            if(!$flag){
                $result = $authGroupAccessLogic->delete(['uid'=>$uid]);
                if(!$result['status']){
                    $flag = true;
                    $error = $result['info'];
                }
            }

            if ($flag) {

                Db::rollback();

                return ['status' => false, 'info' => $error];
            } else {
                Db::commit();
                return ['status' => true, 'info' => lang('tip_success')];
            }
        }catch (Exception $ex){
            Db::rollback();
            return ['status' => false, 'info' =>  $ex->getMessage()];
        }
    }

    /**
     * @param $client_id
     * @param string $mobile
     * @param string $code
     * @param string $country
     * @param string $device_token
     * @param string $device_type
     * @param string $role
     * @return mixed
     */
    function loginByCode($client_id,$mobile, $code, $country,$device_token,$device_type,$role)
    {
        $api = new SecurityCodeLogic();
        $result = $api->isLegalCode($code,$country . $mobile,SecurityCode::TYPE_FOR_LOGIN,$client_id);

        if(!$result['status']){
            $this->loginFail();
            return ['status'=>false,'info'=> lang('err_invalid_code')];
        }

        $api = new UcenterMemberLogic();
        $result = $api->getInfo(['mobile'=>$mobile,'country_no'=>$country]);

        if($result['status'] && !empty($result['info']) && is_array($result['info'])){
            $userInfo = $result['info'];
        }else{
            $this->loginFail();
            return ['status'=>false,'info'=> lang('err_account_unregistered')];
        }

        if($userInfo['status'] != StatusEnum::NORMAL){
            return ['status'=>false,'info'=> lang('err_status_'.$userInfo['status'])];
        }

        //判断用户是否为该角色 role
        if(!$this->hasRole($userInfo['id'],$role)){
            $tipInfo = $this->tipLoginPermission($userInfo['id']);
            if(!$tipInfo['status']){
                return $tipInfo;
            }
            return ['status'=>false,'info'=> lang('err_account_no_permissions')];
        }

        //1. 记录session
        SessionHelper::addLoginSession($userInfo['id'],$device_token,$device_type);

        $result = $this->getUserInfo($userInfo);

        if($result['status']){
            $this->loginSuccess($result['info']);
            return $result;
        }else{
            $this->loginFail();
            return $result;
        }
    }

    /**
     * 登录角色无权限具体提示
     * @param $uid
     * @return array
     */
    private function tipLoginPermission($uid)
    {
        $result = (new AuthGroupAccessLogic())->getRolesByUID($uid);
        if($result['status'] && !empty($result['info'])){
            switch ($result['info'][0]['group_id']){
                case RoleEnum::getRoleIDBy(RoleEnum::ROLE_Driver):
                    return ResultHelper::error('该账号已注册虎头奔司机端');
                    break;
                case RoleEnum::getRoleIDBy(RoleEnum::ROLE_Skilled_worker):
                    return ResultHelper::error('该账号已注册虎头奔技工端');
                    break;
            }
        }
        return ResultHelper::success('');
    }

    /**
     * TODO: 可以用来限制登录失败次数
     */
    private function loginFail()
    {

    }

    /**
     * 判定用户是否有指定角色
     * @param $uid
     * @param $role_code
     * @return bool
     * @internal param $role_id
     */
    private function hasRole($uid, $role_code)
    {
        if ($role_code == RoleEnum::ROLE_Admin) {
            return true;
        }
        return (new AuthGroupAccessLogic())->hasRole($uid, $role_code);
    }

    /**
     * 登录成功
     * @param $userInfo array 用户信息
     * @param $device_token
     * @param $device_type
     */
    private function loginSuccess($userInfo)
    {

        //1. 更新用户登录时间
        $logic = new UcenterMemberLogic();
        $logic->save(['id' => $userInfo['id']], ['last_login_time' => time()]);

        //3. 判断是否已经注册过百川
        if (empty($userInfo['alibaichuan_id'])) {
            $result = $this->syncBaichuanUser($userInfo['id'], $userInfo['password'], $userInfo['nickname']);
            if ($result['status']) {
                $alibaichuan_id = $result['info'];
                $logic = new MemberConfigLogic();
                $logic->save(['uid' => $userInfo['id']], ['alibaichuan_id' => $alibaichuan_id]);
            }
        }

    }

    /**
     * 同步百川用户信息
     * @param $uid
     * @param $password
     * @param $nickname
     * @return mixed
     */
    private function syncBaichuanUser($uid, $password, $nickname)
    {
        $service = new OpenIMUserService();
        $icon_url = ConfigHelper::avatar_url() . '?uid=' . $uid;
        $info = [
            'uid' => $uid,
            'pwd' => $password,
            'nickname' => $nickname,
            'icon_url' => $icon_url
        ];
        return $service->add($info);
    }

    /**
     * 通过手机号 + 验证码
     * @param $username
     * @param $password
     * @param $type
     * @param string $country
     * @param string $device_token
     * @param string $device_type
     * @param string $role
     * @return mixed
     */
    function login($username, $password, $type,$country,$device_token,$device_type,$role)
    {
        $salt = ConfigHelper::getPasswordSalt();
        $encrypt_pwd = think_ucenter_md5($password,$salt);
        $map = [
            'mobile'=>$username,
            "country_no"=>$country,
        ];
        $logic = new UcenterMemberLogic();
        $result = $logic->getInfo($map);

        if($result['status'] && empty($result['info'])){
            $map = [
                'username'=>$username
            ];
            $result = $logic->getInfo($map);
        }
        $login_suc = false;
        $info = "";
        $userInfo = [
            'id'=>0
        ];

        if($result['status'] && !empty($result['info']) && is_array($result['info'])){
            $userInfo = $result['info'];
            if(isset($userInfo['password']) && $userInfo['password'] ==  $encrypt_pwd){
                $login_suc = true;
            }else{
                $info = lang('err_password');
            }

        }else{
            $login_suc = false;
            $info = lang('err_account_unregistered');
        }

        if(!$login_suc){
            return ['status'=>false,'info'=> $info];
        }

        if($userInfo['status'] != StatusEnum::NORMAL){
            return ['status'=>false,'info'=> lang('err_status_'.$userInfo['status'])];
        }

        //判定用户是否具有该角色
        if(!$this->hasRole($userInfo['id'],$role)){
            $tipInfo = $this->tipLoginPermission($userInfo['id']);
            if(!$tipInfo['status']){
                return $tipInfo;
            }
            return ['status'=>false,'info'=> lang('err_account_no_permissions')];
        }

        //1. 记录session
        SessionHelper::addLoginSession($userInfo['id'],$device_token,$device_type);
        //2. 获取用户信息
        $result = $this->getUserInfo($userInfo);
        if($result['status']){
            $this->loginSuccess($result['info']);
            return $result;
        }else{
            $this->loginFail();
            return $result;
        }
    }

    /**
     * @param $entity
     * @return mixed
     */
    function register($entity)
    {

        if (!isset($entity['username']) || !isset($entity['password'])) {
            return ['status' => false, 'info' => lang("lack_parameter",['param'=>"username or password"])];
        }
        $username = $entity['username'];

        if(strlen($username) < 6 || strlen($username) > 64){
            return ['status'=>false,'info'=> lang('tip_username_length')];
        }

        if(!preg_match("/^[a-zA-Z]{1}[a-zA-Z0-9_]{5,64}$/",$username)){
            return ['status'=>false,'info'=>lang('tip_username')];
        }

        $password = $entity['password'];

        $result = ValidateHelper::legalPwd($password);

        if(!$result['status']){
            return $result;
        }
        $country     = $entity['country'];
        $mobile      = $entity['mobile'];
        $reg_type    = $entity['reg_type'];
        $email       = !empty($entity['email']) ? $entity['email']:'';
        $reg_from    = !empty($entity['reg_from']) ? $entity['reg_from']:'';
        $realname    = !empty($entity['realname']) ? $entity['realname']:'';
        $nickname    = !empty($entity['nickname']) ? $entity['nickname']:'';

        if (strlen($mobile) > 4 && empty($nickname)) {
            $nickname = "nick" . substr($mobile, strlen($mobile) - 4, 4);
        } elseif(empty($nickname)) {
            $nickname = "nick" . rand(10000, 99999);
        }

        $birthday    = !empty($entity['birthday']) ? $entity['birthday']:'0';
        $idcode      = !empty($entity['idcode']) ? $entity['idcode']:'';
        $invite_id   = !empty($entity['invite_id']) ? $entity['invite_id'] : 0;
        $head        = !empty($entity['head']) ? $entity['head']:'';
        $sex         = !empty($entity['sex']) ? $entity['sex']: "0";
        $role_code   = !empty($entity['role_code']) ? $entity['role_code']: RoleEnum::ROLE_Driver;

        //微信的openid
        $wxopenid  = !empty($entity['wxopenid'])?$entity['wxopenid']:'';

        $ucenterUserLogic  = new UcenterMemberLogic();
        $memberLogic       = new MemberLogic();
        $memberConfigLogic = new MemberConfigLogic();
        $authGroupAccessLogic    = new AuthGroupAccessLogic();//用户角色

        //1. 检测是否存在用户名
        $result = $ucenterUserLogic->getInfo(array('username'=>$username));
        if($result['status'] && is_array($result['info']) && !empty($result['info'])){
            return array('status'=>false,'info'=>lang('tip_username_exist'));
        }

        $result = $ucenterUserLogic->getInfo(array('mobile'=>$mobile));

        if($result['status'] && is_array($result['info']) && !empty($result['info'])){
            return array('status'=>false,'info'=>lang('tip_mobile_exist'));
        }

        $password = think_ucenter_md5($password,ConfigHelper::getPasswordSalt());

        Db::startTrans();
        try{

            $error = "";
            $flag = false;
            //写入第一张表 UcenterMember
            $result = $ucenterUserLogic->register($username,$password,$email,$mobile,$country,RegFromEnum::getInstance($reg_from));
            $uid = 0;

            if ($result['status']) {
                $uid = $result['info'];
                $member = array(
                    'uid'         => $uid,
                    'realname'    => $realname,
                    'nickname'    => $nickname,
                    'idnumber'    => '',
                    'sex'         =>  $sex,
                    'birthday'    => $birthday,
                    'qq'          => '',
                    'head'        => $head,
                    'update_time' => NOW_TIME, //
                    'status'      => 1,        //
                    'score'       => 0,
                    'login'       => 0,
                );

                //写入第二张表 common_member
                $result = $memberLogic->add($member,"uid");

                if (!$result['status']) {
                    $flag = true;
                    $error = '[用户信息]'.$result['info'];
                }else{

                }
            } else {
                $flag = true;
                $error = '[用户账户]'.$result['info'];
            }

            //同步百川
            $result = $this->syncBaichuanUser($uid,$password,$nickname);
            $alibaichuan_id = 0;

            if(!$result['status']){
                $flag = true;
                $error = "BAICHUAN_".$result['info'];
            }else{
                $alibaichuan_id    = $result['info'];
            }

            if(!$flag){

                //插入到第三张表
                $map = array(
                    'uid'               =>$uid,
                    'phone_validate'    =>0,
                    'email_validate'    =>0,
                    'identity_validate' =>0,
                    'idcode'            =>$idcode,
                    'default_address'   =>0,
                    'exp'               =>0,
                    'invite_id'         => $invite_id,
                    'wxopenid'          => $wxopenid,
                    'alibaichuan_id'    => $alibaichuan_id
                );

                if($reg_type == UcenterMember::ACCOUNT_TYPE_MOBILE) {
                    $map['phone_validate'] = 1;
                }

                $result = $memberConfigLogic->add($map,"uid");
                if(!$result['status'] ){
                    $flag  = true;
                    $error = '[用户配置]'.$result['info'];
                }

            }


            if(!$flag){
                $role_id = RoleEnum::getRoleIDBy($role_code);
                $result  = $authGroupAccessLogic->add(['uid'=>$uid,'group_id'=>$role_id],"");

                if(!$result['status'] ){
                    $flag  = true;
                    $error = '[设置用户组]'.$result['info'];
                }
            }


            if ($flag) {

                Db::rollback();

                return ['status' => false, 'info' => $error];
            } else {
                Db::commit();
                /**
                 *
                 * 增加idcode 的处理，idcode ＝ 用户uid+100000的36进制表示
                 * @author hebidu <hebiduhebi@126.com>
                 * @date  15/11/29 17:11
                 * @copyright by itboye.com
                 */
                $idcode = get_36HEX(intval($uid)+100000);
                $result = $memberConfigLogic->save(['uid'=>$uid],['idcode'=>$idcode]);



                return ['status' => true, 'info' => $uid];
            }
        }catch (Exception $ex){
            Db::rollback();
            return ['status' => false, 'info' => ExceptionHelper::getErrorString($ex)];
        }
    }

    /**
     * @param $uid
     * @param $entity
     * @return mixed
     */
    function update($uid, $entity)
    {

    }

    /**
     *
     * @param $map
     * @param $new_pwd
     * @return array
     */
    function updatePwd($map,$new_pwd){
        $result = ValidateHelper::legalPwd($new_pwd);
        if(!$result['status']){
            return $result;
        }
        $logic  = new UcenterMemberLogic();

        $result = $logic->getInfo($map);
        if(!ValidateHelper::legalArrayResult($result)){
            return ['status'=>false,'info'=>lang('err_modified')];
        }
        $userInfo = $result['info'];
        $uid = $userInfo['id'];

        $memberConfigLogic = new MemberConfigLogic();

        $result = $memberConfigLogic->getInfo(['uid'=>$uid]);

        if(!ValidateHelper::legalArrayResult($result)){
            return ['status'=>false,'info'=>lang('err_modified')];
        }

        $alibaichuan_id = $result['info']['alibaichuan_id'];
        Db::startTrans();
        try {
            $flag = true;
            //        $error = "";

            $salt = ConfigHelper::getPasswordSalt();
            $new_pwd = think_ucenter_md5($new_pwd, $salt);
            $result = $logic->save($map, ['password' => $new_pwd]);

            if (!$result['status']) {
                $flag = false;
                //            $error = $result['info'];
            }

            //百川更新密码
            $service = new OpenIMUserService();
            $result = $service->update($alibaichuan_id, false, $new_pwd);

            if (!$result['status']) {
                $flag = false;
                //            $error = $result['info'];
            }

            if ($flag) {

                Db::commit();

                return ['status' => true, 'info' => lang('suc_modified')];
            } else {

                Db::rollback();
                return ['status' => false, 'info' => lang('err_modified')];
            }
        }catch (Exception $ex){

            Db::rollback();
            return ['status' => false, 'info' => $ex->getMessage()];
        }
    }


    function updatePayPwd($map,$newPwd){
        $result = ValidateHelper::legalPayPwd($newPwd);
        if(!$result['status']){
            return $result;
        }
        $logic = new UcenterMemberLogic();
        $result = $logic->getInfo($map);
        if(!ValidateHelper::legalArrayResult($result)){
            return ['status'=>false,'info'=>lang('err_modified')];
        }
        $userInfo = $result['info'];
        $uid = $userInfo['id'];

        $memberConfigLogic = new MemberConfigLogic();

        $result = $memberConfigLogic->getInfo(['uid'=>$uid]);

        if(!ValidateHelper::legalArrayResult($result)){
            return ['status'=>false,'info'=>lang('err_modified')];
        }

        $pay_secret = md5($newPwd);
        $result = $memberConfigLogic->save(['uid'=>$uid], ['pay_secret' => $pay_secret]);
        if(!$result['status']){
            return ['status' => true, 'info' => $result['info']];
        }
        return ['status' => true, 'info' => lang('suc_modified')];

    }

}