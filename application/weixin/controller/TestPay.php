<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 青 <99701759@qq.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// 微信用户表中的信息在$this->userinfo
// 用户newmember表的信息在$this->newmember中
// 用户角色表的信息在$this->role中
namespace app\weixin\controller;


use app\src\goods\logic\ProductLogic;
use app\src\user\logic\MemberConfigLogic;
use app\src\user\logic\UserMemberLogic;

use app\src\wallet\logic\WalletOrderLogicV2;
use app\src\wallet\model\WalletOrder;
use app\weixin\Api\Wxpay\JsApi;
use app\weixin\Api\Wxpay\WxPayUnifiedOrder;
use app\weixin\Api\Wxpay\WxPayApi;
use app\weixin\Logic\WxpayforLogic;
use think\Db;

class Testpay extends Home{
    /*
     * 启用HTML5进行支付
     * */
    /*微信支付HTML5的实现*/
    public function jsapi(){
        $order_code=input('order_code');

        $money=(new WalletOrderLogicV2())->getInfo(['order_code'=>$order_code]);

        $money=$money['money']/100;

        if(empty($money)) $this->error('读取不到充值的金额');

        $uid=$this->memberinfo['id'];
        $me=(new UserMemberLogic())->getInfo(['uid'=>$uid]);

        if(!$me['status']||empty($me['info'])) $this->error('读取信息有误');
        $role=$me['info']['group_id'];

        $my_config=(new MemberConfigLogic())->getInfo(['uid'=>$uid]);
        if(!$my_config['status']||empty($my_config['info'])) $this->error('读取信息有误');

        if($role==1) $role_info='天使用户';
        if($role==2) $role_info='精英用户';
        if($role==3) $role_info='领袖用户';        if($role==4) $role_info='注册会员';

        $body = $role_info.'充值了提现积分';


        //$fee = bcadd($orderres['info']['price'],$orderres['info']['post_price'],4)*100;
    //        $fee = bcadd($orderres['info']['price'],$orderres['info']['post_price'],2);
    //        $fee = bcsub($fee,$orderres['info']['pay_balance'],2);
    //        $money =  bcsub($fee,$orderres['info']['pay_score']/100,2);


        $this->assign('money',round($money,2));
        $fee = $money*100;
        $config = config("WXPAY_PAY_CONFIG");
;
        //①、获取用户openid
        $tools = new JsApi($config);

        $openId =$my_config['info']['wxopenid'];//获得用户的OPenid
        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->setConfig($config);
        $input->SetBody($body);
        $input->SetAttach("test2");
        $input->SetOut_trade_no($order_code);//随机生成订单号
        $input->SetTotal_fee($fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 36000));
        $input->SetNotify_url("http://dehong.8raw.com/weixin.php/weixin/Ajaxinform/ajaxinform");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);

         (new WxPayApi())->setConfig($config);

        $order=(new WxPayApi())->unifiedOrder($input);

        $jsApiParameters = $tools->GetJsApiParameters($order);

        $this->assign("jsApiParameters",$jsApiParameters);
        $this->assign('order_code',$order_code.'146635214');
        return $this->fetch();

    }


    /*
     * 确认纯储值支付
     *
     * */
    public function store_pay(){

    }


    public function test_pay_for(){
        $partner_trade_no=1234321231;
        $test=(new WxpayforLogic())->pay_for($partner_trade_no,'oivo9wUZ8YOYSUqIXofNXkNeaXWs','100','张健','接口测试');
        var_dump($test);exit;
    }



}