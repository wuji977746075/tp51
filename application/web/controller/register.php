<?php
/**
 * Created by PhpStorm.
 * User: Zhoujinda
 * Date: 2017/1/4
 * Time: 16:21
 */

namespace app\web\controller;

use app\src\admin\helper\ByApiHelper;
use app\src\base\helper\ResultHelper;
use app\src\repairerApply\logic\RepairerApplyLogicV2;
use app\src\securitycode\enum\CodeTypeEnum;
use app\src\securitycode\logic\SecurityCodeLogic;
use app\src\securitycode\model\SecurityCode;
use app\src\user\action\RegisterAction;
use app\src\user\enum\RoleEnum;
use app\src\user\model\UcenterMember;
use think\response\View;

class Register {

    /**
     * 维修师傅注册
     */
    public function repairer(){
        $view = new View();
        $view->assign('title','注册');
        return $view;
    }

    /**
     * 维修师傅注册手机验证码
     */
    public function repairer_verify_code(){
        $mobile = input('post.mobile');
        $data = [
            'type'      => 'By_SecurityCode_send',
            'api_ver'   => '101',
            'notify_id' => time(),
            'country'   => '+86',
            'mobile'    => $mobile,
            'code_type' => SecurityCode::TYPE_FOR_REGISTER,
            'send_type' => CodeTypeEnum::Sms
        ];

        return ByApiHelper::getInstance()->callRemote($data);
    }

    /**
     * 维修师傅注册手机验证码验证
     */
    public function repairer_verify(){
        $mobile = input('post.mobile');
        $code = input('post.code');
        $data = [
            'type'      => 'By_SecurityCode_verify',
            'api_ver'   => '101',
            'notify_id' => time(),
            'country'   => '+86',
            'mobile'    => $mobile,
            'code'      => $code,
            'code_type' => SecurityCode::TYPE_FOR_REGISTER
        ];

        $result = ByApiHelper::getInstance()->callRemote($data);

        if($result['status']){
            $logic = new RepairerApplyLogicV2;
            $result = $logic->addApply($mobile);


            if($result['status']){
                return ResultHelper::success($result['info']);
            }else{
                return ResultHelper::error($result['info']);
            }


        }else{
            return ResultHelper::error($result['info']);
        }

    }

    /**
     * 维修师傅注册手机验证码验证
     */
    public function repairer_verify_V2(){
        $mobile = input('post.mobile');
        $code = input('post.code');
        $psw = input('post.psw');
        $country = '+86';
        
        $username = 'm_' . $mobile;

        $email = '';
        if(empty($email)){
            $email = $username.'@itboye.com';
        }

        $entity = array(
            'nickname'=>$mobile,
            'username'=>$username,
            'password'=>$psw,
            'mobile'=>$mobile,
            'email'=>$email,
            'country'=>$country,
            'reg_from'=>0,
            'reg_type'=>UcenterMember::ACCOUNT_TYPE_MOBILE,
            'role_code'=>RoleEnum::ROLE_Skilled_worker
        );

        //1. 校验验证码
        $securityCodeLogic = new SecurityCodeLogic();
        $result = $securityCodeLogic->isLegalCode($code,$country . $mobile,SecurityCode::TYPE_FOR_REGISTER,config('by_api_config.client_id'));

        if(!$result['status']){
            return ResultHelper::error('验证码错误');
        }

        //2. 调用注册操作
        $action = new RegisterAction();
        $result = $action->register($entity);


        if($result['status'] && intval($result['info']) > 0){
            return ResultHelper::success('注册成功');
        }

        if(is_string($result['info'])){
            return  ResultHelper::error($result['info']);
        }

        return ResultHelper::error('操作失败');

    }
    
}