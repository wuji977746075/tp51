<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-09
 * Time: 20:52
 */

namespace app\src\order\logic;


use app\src\base\logic\BaseLogic;
use app\src\order\model\Orders;
use app\src\order\model\OrderStatusHistory;
use think\Db;

/**
 * TODO: 订单状态变更
 * Class OrderStatusHistoryLogic
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\order\logic
 */
class OrderStatusHistoryLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new OrderStatusHistory());
    }

    /**
     * 订单发货操作
     * @param Orders $orders
     * @param $uid
     * @return array
     * @internal param $order_code
     * @internal param $id
     * @internal param $isauto
     */
    public function shipped(Orders $orders,$uid,$isAuto=0){

        if(empty($orders->getOrderCode()) || empty($orders->getOrderStatus())){
            return $this->apiReturnErr('缺少order_code或order_status');
        }

        if($orders->getOrderStatus() != Orders::ORDER_TOBE_SHIPPED){
            return $this->apiReturnErr("当前订单状态无法变更！");
        }

        $entity = array(
            'reason'=>"订单发货操作!",
            'order_code'=>$orders->getOrderCode(),
            'operator'=>$uid,
            'status_type'=>'ORDER',
            'isauto'=>$isAuto,
            'cur_status'=>$orders->getOrderStatus(),
            'next_status'=> Orders::ORDER_SHIPPED,
        );

        return $this->add($entity);
    }

    /**
     * 订单关闭操作
     * @param Orders $orders
     * @param $uid
     * @param integer $isAuto
     * @return array
     * @internal param $id
     */
    public function closeOrder(Orders $orders,$uid,$isAuto=0){
        if(empty($orders->getOrderCode()) || empty($orders->getOrderStatus())){
            return $this->apiReturnErr('缺少order_code或order_status');
        }

        $entity = array(
            'reason'      =>"订单已关闭!",
            'order_code'  =>$orders->getOrderCode(),//['order_code'],
            'operator'    =>$uid,
            'status_type' =>'ORDER',
            'isauto'      =>$isAuto,
            'cur_status'  =>$orders->getOrderStatus(), //$orders->getOrderStatus(),
            'next_status' =>Orders::ORDER_CANCEL,
        );

        return $this->add($entity);
    }


    /**
     * 订单完成操作
     * @param Orders $orders
     * @param $uid
     * @param integer $isAuto
     * @return array
     * @internal param $id
     */
    public function completeOrder(Orders $orders,$uid,$isAuto=0){
        if(empty($orders->getOrderCode()) || empty($orders->getOrderStatus())){
            return $this->apiReturnErr('缺少order_code或order_status');
        }

        $entity = array(
            'reason'      =>"订单完成",
            'order_code'  =>$orders->getOrderCode(),//['order_code'],
            'operator'    =>$uid,
            'status_type' =>'ORDER',
            'isauto'      =>$isAuto,
            'cur_status'  =>$orders->getOrderStatus(), //$orders->getOrderStatus(),
            'next_status' =>Orders::ORDER_COMPLETED,
        );

        return $this->add($entity);
    }


    /**
     * 订单确认
     * @param Orders $orders
     * @param $uid
     * @param integer $isAuto
     * @return array
     */
    public function confirmOrder(Orders $orders,$uid,$isAuto=0){
        
        if(empty($orders->getOrderCode()) || empty($orders->getOrderStatus())){
            return $this->apiReturnErr('缺少order_code或order_status');
        }

        if($orders->getOrderStatus() != Orders::ORDER_TOBE_CONFIRMED){
            return $this->apiReturnErr("当前订单状态无法变更！");
        }

        $entity = array(
            'reason'=>"订单确认操作!",
            'order_code'=>$orders->getOrderCode(),
            'operator'=>$uid,
            'status_type'=>'ORDER',
            'isauto'=>$isAuto,
            'cur_status'=>$orders->getOrderStatus(),
            'next_status'=> Orders::ORDER_TOBE_SHIPPED,
        );

        return $this->add($entity);
    }

    /**
     * 退回订单
     * @param Orders $orders
     * @param $reason
     * @param $uid
     * @param int $isAuto
     * @return array
     * @internal param $order_code
     * @internal param $isauto
     */
    public function backOrder(Orders $orders,$reason,$uid,$isAuto=0){
        dump("backOrder");
        if(empty($orders->getOrderCode()) || empty($orders->getOrderStatus())){
            return $this->apiReturnErr('缺少order_code或order_status');
        }

        if($orders->getOrderStatus() != Orders::ORDER_TOBE_CONFIRMED){
            return $this->apiReturnErr("当前订单状态无法变更！");
        }

        $entity = array(
            'reason'=>$reason,
            'order_code'=>$orders->getOrderCode(),
            'operator'=>$uid,
            'status_type'=>'ORDER',
            'isauto'=>$isAuto,
            'cur_status'=>$orders->getOrderStatus(),
            'next_status'=> Orders::ORDER_BACK,
        );
        return $this->add($entity);
    }

    /**
     * 确认收货操作
     * @param $order_code
     * @param $isauto
     * @param $uid
     * @return array
     */
    public function confirmReceive(Orders $orders,$uid,$isAuto=0){

        if(empty($orders->getOrderCode()) || empty($orders->getOrderStatus())){
            return $this->apiReturnErr('缺少order_code或order_status');
        }

        if($orders->getOrderStatus() != Orders::ORDER_SHIPPED){
            return $this->apiReturnErr("当前订单状态出错!");
        }

        $entity = array(
            'reason'=>"确认收货操作!",
            'order_code'=>$orders->getOrderCode(),
            'operator'=>$uid,
            'isauto'=>$isAuto,
            'status_type'=>'ORDER',
            'cur_status'=>$orders->getOrderStatus(),
            'next_status'=>Orders::ORDER_RECEIPT_OF_GOODS,
        );

        return $this->add($entity);
    }

    /**
     * 退货操作
     * @param $order_code
     * @param $isauto
     * @param $uid
     * @return array
     * @internal param $id
     */
    public function returned(Orders $orders,$uid,$isAuto=0){

        /*if($orders->getOrderStatus() == Orders::ORDER_RECEIPT_OF_GOODS ){
            return $this->returnErr("当前订单状态出错!");
        }*/

        $entity = array(
            'reason'=>"订单退货操作!",
            'order_code'=>$orders->getOrderCode(),
            'operator'=>$uid,
            'status_type'=>'ORDER',
            'isauto'=>$isAuto,
            'cur_status'=>$orders->getOrderStatus(),
            'next_status'=>Orders::ORDER_RETURNED,
        );

        return $this->add($entity);
    }

    /**
     *
     * 订单取消操作
     * @param Orders $orders
     * @param $isAuto
     * @param $uid
     * @return array
     * @internal param $id
     */
    public function cancelOrder(Orders $orders,$uid,$isAuto){

        if(empty($orders->getOrderCode()) || empty($orders->getOrderStatus())){
            return $this->apiReturnErr('缺少order_code或order_status');
        }

        if($orders->getPayStatus() != Orders::ORDER_TOBE_PAID ){
            return $this->apiReturnErr(lang("err_cant_cancel_payed_order"));
        }

        if($orders->getOrderStatus() != Orders::ORDER_TOBE_CONFIRMED){
            return $this->apiReturnErr(lang("err_order_status"));
        }

        $entity = array(
            'reason'      => "用户取消了订单!",
            'order_code'  =>$orders->getOrderCode(),
            'operator'    =>$uid,
            'status_type' =>'ORDER',
            'isauto'      =>$isAuto,
            'cur_status'  =>$orders->getOrderStatus(),
            'next_status' =>Orders::ORDER_CANCEL,
        );

        if($isAuto){
            $entity['reason'] = "系统自动关闭了订单";
        }

        return $this->add($entity);
    }

    /**
     * 退款
     * @param  [type]  $order_code [description]
     * @param  [type]  $isauto     [description]
     * @param  integer $thid       [description]
     * @param  string  $note       [description]
     * @param  [type]  $money      [description]
     * @param  boolean $goodsOff   [description]
     * @return [type]              [description]
     */
    public function refunded($order_code,$isauto,$thid=0,$note='',$money){
//        $money = (float)$money;
//        if($money<=0) return $this->returnErr('需要退款金额');
//        //检查订单
//        $r = $this->getModel()->where(array('order_code'=>$order_code))->find();
//        if(false === $r) return $this->returnErr($this->getModel()->getDbError());
//        if(!$r)          return $this->returnErr('订单order_code错误');
//        $uid          = $r['uid']; //消费者UID
//        $pay_code     = $r['pay_code'];
//        $pay_balance  = (float)$r['price'];
//        $pay_type     = $r['pay_type'];
//        $order_status = $r['order_status'];
//        if($r['cs_status'] != Orders::CS_PENDING ){
//            //订单售后状态为 待处理
//            return $this->returnErr("非售后订单!");
//        }
//        $this->getModel()->startTrans();
//        //修改订单支付状态 为已退款
//        $r = $this->getModel()->where(array('order_code'=>$order_code))->save(array('pay_status'=>Orders::ORDER_REFUND));
//        if($r === false){
//            $this->getModel()->rollback();
//            return $this->returnErr($this->getModel()->getDbError());
//        }
//        // if($r == 0){
//        //     $this->getModel()->rollback();
//        //     return $this->returnErr('订单已退款!');
//        // }
//
//        //添加订单历史变动记录
//        $entity = array(
//            'reason'      =>"订单退款成功!",
//            'order_code'  =>$order_code,
//            'operator'    =>UID,
//            'status_type' =>'PAY',
//            'isauto'      =>$isauto,
//            'cur_status'  =>$order_status,
//            'next_status' =>Orders::ORDER_REFUND,
//        );
//        $orderHisModel = new OrderStatusHistoryModel();
//        if($orderHisModel->create($entity,1)){
//            if($orderHisModel->add() === false){
//                return $this->returnErr($orderHisModel->getDbError());
//            }
//        }else{
//            return $this->returnErr($orderHisModel->getError());
//        }
//
//        //订单售后记录通过审核
//        $entity = array(
//            'valid_status' =>1,
//            'reply_msg'    =>$note,
//            'order_code'   =>$order_code,
//        );
//        $result = apiCall(OrderRefundApi::SAVE_BY_ID,array($thid,$entity));
//        if(!$result['status']){
//            $this->getModel()->rollback();
//            $this->returnErr('操作失败');
//        }
//        if($money>0){
//            //需要退款
//            //检查支付金额
//            $batch_no = date('Ymd').$thid.time();
//            //查找交易号
//            $pay_thid = 0; //第三方支付金额
//            if($pay_code && in_array($type,array(1,2))){
//                if($type === 1){
//                    //支付宝
//                    $r = apiCall(AlipayNotifyApi::GET_INFO,array(array('out_trade_no'=>$pay_code)));
//                    // $url = 'Api/AlipayRefund/submit';
//                }elseif($type === 2){
//                    //微信
//                    // $url = 'Api/WxpayApp/refundSubmit';
//                    $r = apiCall(WxpayNotifyApi::GET_INFO,array(array('out_trade_no'=>$pay_code)));
//                }
//                if(!$r['status'] ){
//                    // $trade_no = $r['info']['trade_no'];
//                    LogRecord('INFO:' . $r['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
//                    $this->getModel()->rollback();
//                    $this->returnErr('第三方交易记录查询失败');
//                }
//                if(empty($r['info'])){
//                    $this->getModel()->rollback();
//                    $this->returnErr('没有第三方交易记录');
//                }
//                $pay_thid = (float)$r['info']['total_fee'];
//            }
//            if($money>($pay_thid + $pay_balance)){
//                $this->getModel()->rollback();
//                $this->returnErr('退款金额超过了订单实际支付金额！');
//            }
//        }
//
//        $data = $trade_no.'^'.$money.'^'.'协商退款';
//        $entity = array(
//            'order_code'     => $order_code,
//            'reason'         => $Refund_info['reason'],
//            'result'         => 0,
//            'result_data'    => '',
//            'data'           => $data,
//            'money'          => $money,
//            'currency'       => 'RMB',
//            'refund_channel' => $type,
//            'batch_no'       => $batch_no,
//        );
//        $r = apiCall(OrderRefundMoneyApi::ADD,array($entity));
//        if(!$r['status']){
//            $this->getModel()->rollback();
//            $this->returnErr($r['info']);
//        }elseif($r['info']<=0){
//            // dump($r);exit;
//            $this->getModel()->rollback();
//            $this->returnErr('操作失败:ORDER_REFUND_MONEY:ADD');
//        }
//        //修改订单售后状态为已通过
//        $r = apiCall(OrderRefundApi::CHANGE_SERVICE_STATUS,array($order_code,Orders::CS_PROCESSED));
//        if(!$r['status']){
//            $this->getModel()->rollback();
//            $this->returnErr($r['info']);
//        }elseif($r['info']<=0){
//            $this->getModel()->rollback();
//            $this->returnErr('操作失败:ORDER_REFUND:CHANGE_SERVICE_STATUS');
//        }
//        //余额增加 + 余额变动记录
//        $r = apiCall(WalletApi::PLUS,array($uid,$money,getDatatree('WALLET_AFTERSALE'),'订单'.$order_code.'售后退款'));
//        if(!$r['status']){
//            $model ->rollback();
//            $this->returnErr($r['info']);
//        }elseif($r['info']<=0){
//            $this->getModel()->rollback();
//            $this->returnErr('操作失败:WALLET:PLUS');
//        }
//// $this->getModel()->commit();
//        // 推送订单消息
//        $text = '您的订单('.$order_code.')获得'.$money.'元的退款,请查看余额';
//        $entity = array(
//            'from_id' =>0,
//            'title'   =>MessageModel::MESSAGE_ORDER,
//            'content' =>$text,
//            'summary' =>'退款申请已通过',
//            'extra'   =>'', //消息记录中的
//        );
//        $after_open = array('type'=>'go_activity','param'=>MessageModel::MESSAGE_ORDER_ACTIVITY,'extra'=>array('order_code'=>$order_code)); //推送参数
//        $r = apiCall(MessageApi::PUSH_MESSAGE_WITH_TYPE,array(MessageModel::MESSAGE_ORDER,$entity,$uid,false,$after_open));
//        if(!$r['status']) return $this->returnSuc($r['info']);
//        else return $this->returnSuc('退款成功');
    }


    /**
     * 订单评价操作
     * @param $id
     * @param $isauto
     * @param $uid
     * @return array
     */
    public function evaluation($order_code,$isauto,$uid){
//        $orderHisModel = new OrderStatusHistoryModel();
//        $result = $this->getModel()->where(array('order_code'=>$order_code))->find();
//
//        if($result == false){
//            return $this->returnErr($this->getModel()->getDbError());
//        }
//
//        if(is_null($result)){
//            return $this->returnErr("订单ID错误!");
//        }
//
//        if($orders->getOrderStatus() != Orders::ORDER_RECEIPT_OF_GOODS ){
//            return $this->returnErr("当前订单状态出错!");
//        }
//
//        $entity = array(
//            'reason'=>"订单评价操作!",
//            'order_code'=>$orders->getOrderCode(),
//            'operator'=>$uid,
//            'status_type'=>'COMMENT',
//            'isauto'=>0,
//            'cur_status'=>$orders->getOrderStatus(),
//            'next_status'=>Orders::ORDER_COMPLETED,
//        );
//
//        $this->getModel()->startTrans();
//        $flag = true;
//        $return = "";
//
//        $result = $this->getModel()->where(array('order_code'=>$order_code))->save(array('comment_status'=>Orders::ORDER_HUMAN_EVALUATED,'order_status'=>Orders::ORDER_COMPLETED));
//        if($result === false){
//            $flag = false;
//            $return = $this->getModel()->getDbError();
//        }
//
//        if($result == 0){
//            $flag = false;
//            $return = "订单ID有问题!";
//        }
//
//        if($orderHisModel->create($entity,1)){
//            $result = $orderHisModel->add();
//            if($result === false){
//                $flag = false;
//                $return = $orderHisModel->getDbError();
//            }
//        }else{
//            $flag = false;
//            $return = $orderHisModel->getError();
//        }
//
//
//
//        if($flag){
//            $this->getModel()->commit();
//            return $this->returnSuc($return);
//        }else{
//            $this->getModel()->rollback();
//            return $this->returnErr($return);
//        }
    }


    /**
     * 支付状态变更
     * 未支付-》已支付
     * @param $trade_no  交易号（本地商户）
     * @param $order_code 订单编号
     * @param $uid  用户ID
     * @param $pay_type
     * @return array|mixed
     */
    public function payOrder($trade_no,$order_code,$uid,$pay_type){
        if(empty($order_code)){
            return $this->returnErr("订单ID不能为空!");
        }

        $map=array(
            'order_code'=>$order_code,
        );

        $result = apiCall(OrdersApi::GET_INFO,array($map));

        if(!$result['status']){
            return $result;
        }
        if(is_null($result['info'])){
            LogRecord("订单ID非法",__FILE__.__LINE__,'INFO');
            return $this->returnErr("订单ID非法!");
        }
        //0. 检测订单状态，防止重复修改
        $order = $result['info'];
        // 只有订单的支付状态为 未支付时才能继续执行，否则返回
        if($order['pay_status'] != Orders::ORDER_TOBE_PAID){
            return $this->returnErr("已处理!");
        }
        $orderHisModel = new OrderStatusHistoryModel();
        $this->getModel()->startTrans();
        $flag = true;
        $return = "";

        $entity = array(
            'reason'=>"订单支付操作!",
            'order_code'=>$order_code,
            'operator'=>$uid,
            'status_type'=>'PAY',
            'isauto'=>0,
            'cur_status'=>$order['pay_status'],
            'next_status'=>Orders::ORDER_PAID,
        );
        //1. 保存本地交易号，与修改订单状态
        $result = $this->getModel()->where($map)->save(array('pay_type'=>$pay_type,'pay_code'=>$trade_no,'pay_status'=>Orders::ORDER_PAID));

        if($result === false){
            $flag = false;
            $return = $this->getModel()->getDbError();
        }

        if($result == 0){
            $flag = false;
            $return = "订单ID有问题!";
        }

        if($orderHisModel->create($entity,1)){
            $result = $orderHisModel->add();
            if($result === false){
                $flag = false;
                $return = $orderHisModel->getDbError();
            }
        }else{
            $flag = false;
            $return = $orderHisModel->getError();
        }


        if($flag){
            Db::commit();
            return $this->returnSuc($return);
        }else{
            Db::rollback();
            return $this->returnErr($return);
        }


    }

}