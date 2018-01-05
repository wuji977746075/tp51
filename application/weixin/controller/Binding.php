<?php
/**
 * Created by PhpStorm.
 * User: boye
 * Date: 2017/3/1
 * Time: 9:23
 */

namespace app\weixin\controller;

use app\src\securitycode\action\SecurityCodeCreateAction;
use app\src\securitycode\action\SecurityCodeVerifyAction;
use app\src\user\logic\UcenterMemberLogic;
use app\src\user\logic\UserGroupLogic;
use app\src\user\logic\MemberLogic;
use app\src\user\logic\UserMemberLogic;
use app\src\user\logic\MemberConfigLogic;
use think\Db;


class Binding extends Home
{

    /*
     * 用户个人配置信息修改
     */
    //用户个人信息，用户积分信息，用户地址信息
    public function changebinding()
    {
        if (IS_GET) {
            $memberinfo=$this->memberinfo;
            $memberconfig=(new UcenterMemberLogic())->getInfo(array('id'=>$memberinfo['uid']));
            if(!$memberconfig['status']) $this->apiReturnErr('数据查询有误');
            if(!empty($memberconfig['info'])){
                $memberconfig['info']['mobile']=preg_match("/^1[34578]\d{9}$/",$memberconfig['info']['mobile'])?$memberconfig['info']['mobile']:"";
                $this->assign('memberconfig', $memberconfig['info']);
                $this->assignTitle(' 手机号綁定');
                return $this->fetch();
            }
        }

        $memberinfo=$this->memberinfo;
        $phone=input('mobile', '');
        $code = input('code','');
        $verify=$this->verify_code($phone,$code);
        if(!$verify) $this->error('验证码错误','');
        //input('code','');
        if($phone) {
            Db::startTrans();
            $entity = array(
                'mobile' => $phone
            );
            $has=(new UcenterMemberLogic())->getInfo($entity);
            if(!empty($has['info'])) {
                $this->error('该手机已被绑定','');
            }
            $flag = true;
            $result =  (new UcenterMemberLogic())->save(['id' => $memberinfo['uid']],$entity);
            if(!$result['status'])  $flag = false;
            $entity = array(
                'phone_validate' => 1
            );
            $result = (new MemberConfigLogic())->save(['uid' => $memberinfo['uid']],$entity);
            if(!$result['status'])  $flag = false;
            if($flag){
                Db::commit();
                $this->success('更新成功','index/index');
            }else{
                Db::rollback();
                $this->error('更新失败','');
            }
        }

    }



    //调用，创建并发送手机验证码
    public function send_sms(){
        $mobile=input('mobile','');
        //生成验证码
        $code=(new SecurityCodeCreateAction())->create('byweixin',$mobile,2);
        if(empty($mobile)) $this->error('手机号为空');
        if(!empty($code['info'])){
            $send=$this->send_message($mobile,$code['info']);
            if(empty($send))   $this->success('短信已发送','');
        }

       $this->error('发送失败','');
    }
    //发送手机验证码
    public function send_message($moblie,$code){
        //发送短信操作
        $account='d82e8q';
        $pswd='6MgEG71w';
        $mse='德弘乐活健康商城更新手机，验证码为：'.$code;
        $url = "http://send.18sms.com/msg/HttpBatchSendSM?account=".$account."&pswd=".$pswd."&mobile=".$moblie."&msg=".$mse."&needstatus=".true;
        //http://send.18sms.com/msg/HttpBatchSendSM?account=test01&pswd=123456&mobile=18900000000&msg=您的验证码：1234&needstatus=true&extno=1234
        $arr = json_decode($this->curlGet($url), true);

        return $arr;

    }


    //验证手机验证码
    public function verify_code($moblie,$code){
        $verify=(new SecurityCodeVerifyAction())->verify($moblie,2,$code,'byweixin');
        return($verify);
        if($verify['status']) return true;
        return false;

    }


    protected function curlGet($url) {
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $temp = curl_exec($ch);

        return $temp;
    }


}