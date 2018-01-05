<?php
/**
 * 周舟 hzboye010@163.com
 * addby sublime snippets
 * 微信测试类
 */

namespace app\test\controller;

use app\extend\BoyeService;

//test only
// use app\index\service\WeixinService;

class Weixin extends Ava{
    //微信登陆
    public function login(){
        if(IS_AJAX){
            $data = [
            'code'      => input('post.code',''),
            'api_ver'   => $this->api_ver,
            'type'      => 'BY_Weixin_login',
          ];

          $service = new BoyeService();
          $result  = $service->callRemote("",$data,false);
          return $this->parseResult($result);
        }else{
            $this->assign('type','BY_Weixin_login');
            $this->assign('field',[
                ['api_ver','100',LL('need-mark api version')],
                ['code','04138715114be21b573b4353d3591b1U',LL('need-mark weixin-code')],
            ]);
            return $this->fetch('ava/test');
        }
    }

    //微信绑定
    public function bind(){
        if(IS_AJAX){
            $data = [
            'code'      => input('post.code',''),
            'uid'       => input('post.uid',''),
            'api_ver'   => $this->api_ver,
            'type'      =>'BY_Weixin_bind',
          ];

          $service = new BoyeService();
          $result = $service->callRemote("",$data,false);
          return $this->parseResult($result);
        }else{
            $this->assign('type','BY_Weixin_bind');
            $this->assign('field',[
                ['api_ver','100',LL('need-mark api version')],
                ['uid','42',LL('need-mark user ID')],
                ['code','04138715114be21b573b4353d3591b1U',LL('need-mark weixin-code')],
            ]);
            return $this->fetch('ava/test');
        }
    }
//     //test only
//     public function index(){

//         $code      = "04138715114be21b573b4353d3591b1U";
//         $appid     = "wx0d259d7e9716d3dd";
//         $appsecret = "94124fb74284c8dae6f188c7e269a5a0";
//         $service   = new WeixinService($appid,$appsecret);

// //      $result = $service->getAccessTokenAndOpenid($code);
// //      dump($result);

//         $result = $service->getUserInfo($code);

//         dump($result);
//         exit();
//     }

//     //test only
//     public function test(){

//         $access_token = "OezXcEiiBSKSxW0eoylIeLL__w8mh9_H1IFEtNHG8D2z6hbexaHPFPMffNopjx5onqFQjX4jHlPEPc8GQus44d0931Ra874-icp1HpDHhoL3GE8OeoKW9OPoS7sOjlLKeOQUfLO7YzYNEGcmtvKGPw";
//         $openid  ="ooQDbsnArKx1iBCUp05EfFeOP8f0";
//         $unionid = "o_4WajjRYUsu6qM3Fn3NvnctZrg0";

//         $service = new WeixinService($appid,$appsecret);
//         $result  = $service->getUserInfoBy($access_token,$openid);

//         dump($result);
//     }

}