<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-23
 * Time: 00:28
 */
namespace  app\index\controller;

use app\src\base\helper\ResultHelper;
use app\src\sign\logic\UserSignLogic;
use app\src\user\action\LoginAction;
use app\src\user\action\RegisterAction;
use app\src\user\logic\MemberConfigLogic;
use app\src\wxapp\helper\LoginHelper;
use think\controller\Rest;
use think\Request;

class Sign extends Rest{

    private $appId = "wx0fb8ce657fa4b28c";
    private $appSecret = "d68f77d36c81eeb43377cc9b2da37e95";

    /**
     * 小程序登录
     */
    public function login(){
        $code = $this->_param('code','','缺少code参数');
        $result = LoginHelper::getSessionKey($this->appId,$this->appSecret,$code);
        $msg = "";
        if($result['status']){
            $openId = $result['info']['openid'];
            $session_key = $result['info']['session_key'];
            $username = 'wxapp_'.md5($openId);
            $password = substr($openId,0,8);
            $result = (new MemberConfigLogic())->getInfo(['wxapp_openid'=>$openId]);
            if($result['status'] && empty($result['info'])) {
                $entity = ['wxapp_openid' => $openId, 'username' => $username, 'password' => $password];
                $entity['country'] = "+86";
                $entity['mobile'] = 'xx'.md5($username);
                $entity['reg_type'] = "";

                $result = (new RegisterAction())->register($entity);
                if(!$result['status']){
                    return $this->jsonReturnErr($result['info']);
                }
            }

            $result = (new LoginAction())->login($username,$password,"+86","","wxapp","");
            if($result['status'] && is_array($result['info'])){
                $userInfo = $result['info'];
                $userInfo['wxapp_session_key'] = $session_key;
                //登录成功
                return $this->jsonReturnSuc($userInfo);
            }

        }

        return $this->jsonReturnErr($result['info']);
    }

    /**
     * 签到接口
     */
    public function signin(){
        $uid = $this->_param('uid',0,'缺少UID');
        $latitude = $this->_param('latitude',0,'缺少latitude');
        $longitude = $this->_param('longitude',0,'缺少longitude');
        $context = $this->_param('context','');

        $entity = [
            'uid'=>$uid,
            'latitude'=>$latitude,
            'longitude'=>$longitude,
            'create_time'=>time(),
            'sign_time'=>time(),
            'context'=>$context,//上下文环境，分析签到使用的设备信息
        ];
        $map = [
            'uid'=>$uid,
        ];
        $logic = new UserSignLogic();
        //TODO: 每日签到次数限制
//        $logic->count($map)
//        $map['sign_time'] = [''];
//        $logic->getInfo($map)

        $result = $logic->add($entity);
        if($result['status']){
            return $this->jsonReturnSuc('签到成功');
        }else{
            return $this->jsonReturnErr('签到失败');
        }

    }


    /**
     * 签到接口
     */
    public function history(){
        $uid = $this->_param('uid',0,'缺少UID');

        $map = [
            'uid'=>$uid,
        ];
        $order = "create_time desc";

        $result = (new UserSignLogic())->queryNoPaging($map,$order);
        if($result['status']){
            $list = $this->process($result['info']);
            return $this->jsonReturnSuc($list);
        }else{
            return $this->jsonReturnErr([]);
        }

    }

    private function process($list){
        foreach ($list as &$item){
            $item['create_time'] = date("Y-m-d H:i:s",$item['create_time']);
            $item['sign_time'] = date("Y-m-d H:i:s",$item['sign_time']);
        }
        return $list;
    }

    private function _param($key,$default='',$emptyErrMsg=''){
        $value = Request::instance()->param($key,$default);

        if($default == $value && !empty($emptyErrMsg)){
            $this->jsonReturnErr($emptyErrMsg);
        }
        return $value;
    }

    private function jsonReturnSuc($msg){
        return $this->jsonReturn(0,$msg);
    }

    private function jsonReturnErr($msg){
        return $this->jsonReturn(-1,$msg);
    }

    private function jsonReturn($code,$msg=''){
        $data = ['code'=>$code,'msg'=>$msg];
        $response = $this->response($data, "json",200);
        $response->header("X-Powered-By",POWER)->send();
        exit(0);
    }
}