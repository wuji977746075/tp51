<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-10
 * Time: 22:17
 */

namespace app\src\hook;

use app\src\base\helper\ValidateHelper;
use think\Db;
use think\Exception;

use app\src\message\enum\MessageType;
use app\src\message\facade\MessageFacade;

use app\src\repair\logic\RepairLogicV2;
use app\src\wallet\logic\WalletLogic;
use app\src\order\logic\OrdersLogic;
// use app\src\order\logic\OrdersPaycodeLogic;
// use app\src\order\model\OrdersPaycode;

/**
 * 支付成功钩子
 * Class PaySuccessHook
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\afterpay\logic
 */
class PaySuccessHook
{
    /**
     * 支付完成回调
     * @param $seller_id
     * @param $total_fee
     * @param $out_trade_no
     */
    public function finished($seller_id,$total_fee,$out_trade_no,$trade_no){

    }

    /**
     * 支付成功回调
     * @param $seller_id
     * @param $total_fee
     * @param $out_trade_no
     * @param $currency string 货币单位
     * @return \app\src\base\logic\status|array|bool|void
     * @internal param $seller_id
     */
    public function success($seller_id,$total_fee,$out_trade_no,$currency,$trade_no,$payType){

        $pay_code   = $out_trade_no;
        $order_type = substr($pay_code,0,2);
        Db::startTrans();
        if($order_type == 'PA'){         //商城订单
          $r = (new OrdersLogic())->paySuccessCall($pay_code,$total_fee,$payType,$trade_no);
        }elseif($order_type == 'WX'){    //维修订单
          $r = (new RepairLogicV2())->paySuccess($pay_code,$total_fee,$payType,$trade_no);
        }elseif($order_type == 'WC'){    //余额充值
          $r = (new WalletLogic())->paySuccess($pay_code,$total_fee,$payType,$trade_no);
        }else{
          return returnErr('未知支付码['.$pay_code.']',true);
        }
        if(!$r['status']) return returnErr($r['info'],true);
        $this->sendNotification($r['info']['uid'],$r['info']['msg']);
        Db::commit();
        return returnSuc(lang('success'));

        // $payCodeLogic = new OrdersPaycodeLogic();
        // $result = $payCodeLogic->getInfo(['pay_code'=>$out_trade_no],false,false,false,true);
        // if(ValidateHelper::legalArrayResult($result)){

        //     $payInfo = $result['info'];

        //     addLog("alipay/notify",$payInfo['pay_money'],$total_fee,"支付金额对比");
        //     $b_status = 1;
        //     if($payInfo['pay_money'] != $total_fee){
        //         //TODO： 2. 金额不一致时 订单进入风控环节
        //         $b_status = 2;
        //     }

        //     if(strtolower($payInfo['pay_currency']) != strtolower($currency)){
        //         //TODO： 3. 货币单位不一致时 订单进入风控环节
        //         $b_status = 3;
        //     }

        //     if($payInfo['pay_status'] != OrdersPaycode::PAY_STATUS_PAYED){

        //         Db::startTrans();
        //         try{

        //             $result = $payCodeLogic->paySuccess($trade_no,$out_trade_no,$payType,$b_status,$total_fee);

        //             addLog("ALIPAY_NOTIFY_ACTION",$trade_no."信息:".json_encode($result),$out_trade_no,"paySuccess");

        //             //更新成功
        //             if($result['status'] && intval($result['info']) == 1){
        //                 addLog("ALIPAY_NOTIFY_ACTION","支付信息更新成功，接下来更新订单状态",$out_trade_no,"paySuccess");

        //                 //1. 更新paycode表
        //                 //2. 更新order表 订单编号 逗号隔开的
        //                 $orderCodeArr = explode(",",$payInfo['order_content']);
        //                 $pay_info = [
        //                     'pay_type'=>$payType,
        //                     'pay_balance'=>0,
        //                     'pay_code'=>$out_trade_no
        //                 ];

        //                 $ordersLogic = new OrdersLogic();
        //                 foreach ($orderCodeArr as $order_code){
        //                     if(empty($order_code)) continue;

        //                     $result  = $ordersLogic->getInfo(['order_code'=>$order_code]);
        //                     if(!$result['status'] || empty($result['info'])){

        //                         addLog("ALIPAY_NOTIFY_ACTION","订单信息获取失败为空".$order_code,$out_trade_no,"paySuccess");

        //                         Db::rollback();
        //                         return $result;
        //                     }

        //                     $result = $ordersLogic->paySuccess($result['info'],$pay_info);

        //                     if(!$result['status']){

        //                         addLog("ALIPAY_NOTIFY_ACTION","订单信息修改为支付状态失败".$order_code,$out_trade_no,"paySuccess");

        //                         Db::rollback();
        //                         return $result;
        //                     }
        //                 }

        //                $this->sendNotification($payInfo['uid'],$payInfo['order_content']);

        //                 addLog("ALIPAY_NOTIFY_ACTION","订单状态更新成功",$out_trade_no,"paySuccess");

        //                 Db::commit();
        //                 return ['status'=>true,'info'=>lang('success')];

        //             }else{

        //                 addLog("ALIPAY_NOTIFY_ACTION","支付信息更新失败","trade_no:".$trade_no.',out_trade_no:'.$out_trade_no,"paySuccess");
        //                 Db::rollback();
        //                 return $result;
        //             }
        //         }catch (Exception $ex){
        //             //发生异常也回滚
        //             Db::rollback();
        //             addLog("ALIPAY_NOTIFY_ACTION","支付更新失败","发生异常:".$ex->getMessage(),"paySuccess");

        //             return ['status'=>false,'info'=>$ex->getMessage()];
        //         }
        //     }
        //     else{

        //         addLog("ALIPAY_NOTIFY_ACTION","支付更新失败","该订单已经支付成功","paySuccess");

        //         return ['status'=>false,'info'=>lang('err_hook_pay_payed')];
        //     }
        // }else{
        //     addLog("ALIPAY_NOTIFY_ACTION","支付更新失败","不存在该订单","paySuccess");
        //     return ['status'=>false,'info'=>lang('err_hook_pay_no_trade_info')];
        // }

    }

    /**
     * 银联手机控件支付成功
     */
    public function successUapcp($total_fee,$pay_code,$currency,$trade_no,$payType){
        $order_type = substr($pay_code,0,2);
        Db::startTrans();
        if($order_type == 'PA'){         //商城订单
            $r = (new OrdersLogic())->paySuccessCall($pay_code,$total_fee,$payType,$trade_no);
        }elseif($order_type == 'WX'){    //维修订单
            $r = (new RepairLogicV2())->paySuccess($pay_code,$total_fee,$payType,$trade_no);
        }elseif($order_type == 'WC'){    //余额充值
            $r = (new WalletLogic())->paySuccess($pay_code,$total_fee,$payType,$trade_no);
        }else{
            return returnErr('未知支付码['.$pay_code.']',true);
        }
        if(!$r['status']) return returnErr($r['info'],true);
        $this->sendNotification($r['info']['uid'],$r['info']['msg']);
        Db::commit();
        return returnSuc(lang('success'));
    }

    public function fail(){

    }

    /**
     * 发送通知
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @param $orderContent
     */
    private function sendNotification($uid,$orderContent){
        //记入消息表
        $facade = new MessageFacade();
        $entity = [
            'uid'=> 0,
            'to_uid'=>$uid,
            'content'=> lang('tip_hook_pay_success_content',['content'=>$orderContent]),
            'title'=> lang('tip_hook_pay_success'),
            'summary'=> lang('tip_hook_pay_success_summary',['content'=>$orderContent]),
            'extra'=> '',
            'msg_type'=> MessageType::ORDER
        ];

        $facade->addMsg($entity);
        //TODO: 发短信

    }

}