<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 吃草的鱼 <783188184@qq.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\weixin\controller;
use app\src\user\logic\MemberConfigLogic;
use app\src\wallet\logic\WalletApplyLogic;
use app\src\wallet\logic\WalletOrderLogicV2;
use app\src\wallet\logic\WalletHisLogicV2;
use app\src\wallet\logic\WalletLogic;
use app\src\wxpay\logic\WxaccountLogic;
use Think\Controller;
use Weixin\Api\WxuserApi;
use Weixin\Api\WxaccountApi;
use Common\Api\WeixinApi;
use app\weixin\controller\Order;
use app\wxapp\controller\BaseController;
use app\weixin\controller\Income;
use think\Db;
class Alipaytest extends Base{
    public function test(){
        require_once         VENDOR_PATH.'AlipayApp/AopSdk.php';
        $FORMAT='json';
        $CHARSET='GBK';
        $SIGN_TYPE='RSA2';
        $c = new \AopClient;
        $c->gatewayUrl =$this->URL;
        $c->appId = $this->APP_ID;
        $c->rsaPrivateKey =$this->rsaPrivateKey;
        $c->format = $FORMAT;
        $c->charset= $CHARSET;
        $c->signType= $SIGN_TYPE;
        $c->alipayrsaPublicKey = $this->alipayrsaPublicKey;
        return $this->fetch();

    }

    public function pay(){
        $out_trade_no=input('out_trade_no');
        $wallet_order=(new WalletOrderLogicV2())->getInfo(['order_code'=>$out_trade_no]);
        if(empty($wallet_order)) $this->error('订单查询出错','');
        $total_amount=$wallet_order['money'];
        $total_amount=$total_amount/100;
        $post=[
            'WIDout_trade_no'=>$out_trade_no,
            'WIDsubject'=>'德弘东方-用户充值',
            'WIDtotal_amount'=>$total_amount,
            'WIDbody'=>'德弘积分充值',
        ];
        $this->assign('info',$post);
        $this->assign('post',json_encode($post));
        return $this->fetch();
    }

    public function pay_done(){
        $out_trade_no=input('WIDout_trade_no');
        $wallet_order=(new WalletOrderLogicV2())->getInfo(['order_code'=>$out_trade_no]);

        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            $info=[
                'WIDout_trade_no'=>$out_trade_no,
                'WIDsubject'=>'德弘东方-用户充值',
                'WIDtotal_amount'=>$wallet_order['money']/100,
                'WIDbody'=>'德弘积分充值',
            ];
            $this->assign('info',$info);
            return $this->fetch();
        }else{

        require_once VENDOR_PATH.'AlipayApp/wappay/service/AlipayTradeService.php';
        require_once VENDOR_PATH.'AlipayApp/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php';
        $out_trade_no=input('WIDout_trade_no');
        $wallet_order=(new WalletOrderLogicV2())->getInfo(['order_code'=>$out_trade_no]);

        $post=[
            'WIDout_trade_no'=>input('WIDout_trade_no'),
            'WIDsubject'=>'德弘东方-用户充值',
            'WIDtotal_amount'=>$wallet_order['money']/100,
            'WIDbody'=>'德弘积分充值',
        ];
        if (!empty($post['WIDout_trade_no'])&& trim($post['WIDout_trade_no'])!=""){
            //商户订单号，商户网站订单系统中唯一订单号，必填
            $out_trade_no = $post['WIDout_trade_no'];
            //订单名称，必填
            $subject = $post['WIDsubject'];
            //付款金额，必填
            $total_amount = $post['WIDtotal_amount'];
            //商品描述，可空
            $body = $post['WIDbody'];
            $config=config('alipay_config');

            //超时时间
            $timeout_express="1m";
            $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
            $payRequestBuilder->setBody($body);
            $payRequestBuilder->setSubject($subject);
            $payRequestBuilder->setOutTradeNo($out_trade_no);
            $payRequestBuilder->setTotalAmount($total_amount);
            $payRequestBuilder->setTimeExpress($timeout_express);
            $payResponse = new \AlipayTradeService($config);
            $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
            return ;
        }
        }
    }

    public function shuchu(){
        $uid=(new WalletOrderLogicV2())->getInfo(['order_code'=>'WX196I324']);
        $uid=$uid['uid'];
        dump($uid);


    }

    public function notify_url(){
        $config=config('alipay_config');
        require_once VENDOR_PATH.'AlipayApp/wappay/service/AlipayTradeService.php';
        $arr=$_POST;
        $alipaySevice = new \AlipayTradeService($config);
        $alipaySevice->writeLog(var_export($_POST,true));
        $result = $alipaySevice->check($arr);

        if($result) {
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];
            $money=$_POST['total_amount']*100;
            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                $wallet_order=(new WalletOrderLogicV2())->getInfo(['order_code'=>$out_trade_no]);
                    if(!empty($wallet_order)){
                        if($wallet_order['pay_status']==0){
                            if($wallet_order['money']!=$money){echo "fail";exit;}
                            //若订单处于未处理状态
                            Db::startTrans();
                            $flag = false;
                            $map['order_code'] = $out_trade_no;//返回订单号
                            $save['pay_type'] = '3';
                            $save['pay_status'] = '1';
                            $save['pay_code'] = $trade_no;//微信支付订单号
                            $order_wall=(new WalletOrderLogicV2())->save($map,$save);
                            $uid=(new WalletOrderLogicV2())->getInfo($map);
                            $uid=$uid['info']['uid'];
                            if($order_wall){
                                $orderres = (new MemberConfigLogic())->getInfo(['uid'=>$uid]);
                                if($orderres['status']){
                                    $money=$money;
                                    $note='用户充值加提现积分-后续处理';
                                    $wallet=$this->wallet_update($note,$money,'1','0',$uid);
                                    if($wallet){
                                    }else{
                                        $flag = true;
                                    }
                                }else{
                                    $flag = true;
                                }
                            }else{
                                $flag = true;
                            }
                            if($flag){
                                Db::rollback();
                            }else{
                                Db::commit();
                            }
                        }
                    }

            }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                Db::startTrans();
                $flag = false;
                $wallet_order=(new WalletOrderLogicV2())->getInfo(['order_code'=>$out_trade_no]);
                if(empty($wallet_order)){ echo "fail";exit;}
                if($wallet_order['money']!=$money){ echo "fail";exit;}
                if($wallet_order['pay_status']!=0){ echo "fail";exit;}
                $map['order_code'] = $out_trade_no;//返回订单号
                $save['pay_type'] = '3';
                $save['pay_status'] = '1';
                $save['pay_code'] = $trade_no;//微信支付订单号
                $order_wall=(new WalletOrderLogicV2())->save($map,$save);
                $mem=(new WalletOrderLogicV2())->getInfo($map);
                $uid=$mem['uid'];
                if($order_wall){
                    $orderres = (new MemberConfigLogic())->getInfo(['uid'=>$uid]);
                    if($orderres['status']){
                        $note='用户充值加提现积分-支付宝';
                        $wallet=$this->wallet_update($note,$money,'1','0',$uid);
                        if($wallet){
                        }else{
                            $flag = true;
                        }
                    }else{
                        $flag = true;
                    }
                }else{
                    $flag = true;
                }
                if($flag){
                    Db::rollback();
                    exit;
                }else{
                    Db::commit();
                }
            }
            echo "success";		//请不要修改或删除
        }else {
            //验证失败
            echo "fail";	//请不要修改或删除
        }
    }

    public function return_url(){
        $config=config('alipay_config');
        require_once VENDOR_PATH.'AlipayApp/wappay/service/AlipayTradeService.php';
        $arr=$_GET;
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($arr);
        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        if($result) {//验证成功
            //请在这里加上商户的业务逻辑程序代码
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
            //商户订单号
            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);
            //支付宝交易号
            $trade_no = htmlspecialchars($_GET['trade_no']);

            $wallet_order=(new WalletOrderLogicV2())->getInfo(['order_code'=>$out_trade_no]);
            $this->assign('status','1');
            $this->assign('wallet',$wallet_order);

            return $this->fetch();
        }
        else {
            //验证失败
            $this->assign('status','0');
            return $this->fetch();
        }

    }



    public function wallet_update($note,$money,$type,$status,$uid){

        $wallet=(new WalletLogic())->getInfo(['uid'=>$uid]);

        //扣除积分
        $wallet=(new WalletLogic())->getInfo(['uid'=>$uid]);
        $update_time=time();
        if($type==0){
            if($status==0){
                $after_money=$wallet['info']['stock_points']+$money;

                $wallet_enity=['stock_points'=>$after_money,'update_time'=>$update_time];
            }
            if($status==1){
                $after_money=$wallet['info']['stock_points']-$money;
                $wallet_enity=['stock_points'=>$after_money,'update_time'=>$update_time];
            }
        }elseif($type==1){
            if($status==0){
                $after_money=$wallet['info']['cash_points']+$money;
                $wallet_enity=['cash_points'=>$after_money,'update_time'=>$update_time];
            }
            if($status==1){
                $after_money=$wallet['info']['cash_points']-$money;
                $wallet_enity=['cash_points'=>$after_money,'update_time'=>$update_time];
            }
        }

        $miu=(new WalletLogic())->save(['uid'=>$uid],$wallet_enity);

        if($type==0) {
            $miu_log_info = [
                'uid'           => $uid,
                'before_points' => $wallet['info']['stock_points'],
                'points_type'   => '0',
                'after_money'   => $after_money,
                'create_time'   => time(),
                'reason'        => $note,
                'dtree_type'    => 0,
                'wallet_type'   => '0',
                'order'=>'-1'
            ];
            if($status==0) {$miu_log_info['plus']=$money;$miu_log_info['minus']='0';}
            if($status==1) {$miu_log_info['plus']='0';$miu_log_info['minus']=$money;}

        }else{
            $miu_log_info = [
                'uid'           => $uid,
                'before_points' => $wallet['info']['cash_points'],
                'points_type'   => '1',
                'after_money'   => $after_money,
                'create_time'   => time(),
                'reason'        => $note,
                'dtree_type'    => 0,
                'wallet_type'   => '0',
                'order'=>'-1'
            ];
            if($status==0) {$miu_log_info['plus']=$money;$miu_log_info['minus']='0';}
            if($status==1) {$miu_log_info['plus']='0';$miu_log_info['minus']=$money;}
        }

        $miu_log=(new WalletHisLogicV2())->add($miu_log_info,'id');

        if($miu['status']&&$miu_log){
            return true;
        }
    }
}
