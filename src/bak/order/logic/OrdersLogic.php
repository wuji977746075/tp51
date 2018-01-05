<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-09
 * Time: 20:54
 */

namespace app\src\order\logic;


use app\src\base\helper\ResultHelper;
use app\src\base\helper\ValidateHelper;
use app\src\base\logic\BaseLogic;
use app\src\goods\logic\ProductLogic;
use app\src\goods\logic\ProductSkuLogic;
use app\src\order\model\Orders;
use think\Db;
use think\Exception;
use think\exception\DbException;
use app\src\order\enum\PayType;

use app\src\order\model\OrdersContactinfo;
use app\src\order\model\OrdersExpress;
use app\src\order\model\OrdersItem;
use app\src\order\logic\OrdersPaycodeLogic;
use app\src\order\logic\OrderStatusHistoryLogic;

use app\src\wallet\logic\ScoreHisLogicV2;

class OrdersLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new Orders());
    }

    /**
     * 订单确认操作
     * @param Orders $orders
     * @return \app\src\base\logic\status|array|bool
     */
    public function confirm(Orders $orders){

        $result = $this->getInfo(['order_code'=>$orders->getOrderCode()]);

        if(!ValidateHelper::legalArrayResult($result)){
            return $this->apiReturnErr(lang("err_order_code"));
        }

        $order_info = $result['info'];

        //不是已发货
        if($order_info['order_status'] != Orders::ORDER_TOBE_CONFIRMED){
            return $this->apiReturnErr(lang("err_order_status"));
        }

        //不是已支付
        if($order_info['pay_status'] != Orders::ORDER_PAID){
            return $this->apiReturnErr('必须是已支付订单才能确认');
        }


        $map    = ['uid'=>$order_info['uid'],'order_code'=>$orders->getOrderCode()];
        $update = ['order_status'=>Orders::ORDER_TOBE_SHIPPED,'updatetime'=>time()];

        $result = $this->save($map,$update);

        return $result;

    }


    /**
     * 订单多次快递发货操作 2017-08-31 16:45:08
     * @author rainbow
     * @param Orders $orders
     * @param array  $exps
     * @return \app\src\base\logic\status|array|bool
     */
    public function multiShipped(Orders $orders,array $exps){

      $r = $this->getInfo(['order_code'=>$orders->getOrderCode()]);
      if(!ValidateHelper::legalArrayResult($r)){
          return $this->apiReturnErr(lang("err_order_code"));
      }
      $order_info = $r['info'];

      //不是已发货
      if($order_info['order_status'] != Orders::ORDER_TOBE_SHIPPED){
          return $this->apiReturnErr(lang("err_order_status"));
      }
      //不是已支付
      if($order_info['pay_status'] != Orders::ORDER_PAID){
          return $this->apiReturnErr(lang("err_pay_status"));
      }

      //1. 开启事务
      Db::startTrans();
      $hasError  = false;
      $error  = "";
      $map    = ['uid'=>$orders->getUid(),'order_code'=>$orders->getOrderCode()];
      $update = ['order_status'=>Orders::ORDER_SHIPPED,'updatetime'=>time()];
      // 修改订单状态
      $r = $this->save($map,$update);
      if(!$r['status']){
        $hasError = true;
        $error = $r['info'];
      }else{
        // 添加 快递 信息
        foreach ($exps as $v) {
          $ordersExpressLogic = (new OrdersExpressLogic());
          // $r = $ordersExpressLogic->getInfo(['order_code'=>$v['order_code']]);
          // if(ValidateHelper::legalArrayResult($r)) {
          //   unset($v['order_code']);
          //   unset($v['uid']);
          //   $r = $ordersExpressLogic->saveByID($r['info']['id'],$v);
          // }else{
            $r = $ordersExpressLogic->add($v);
          // }
          if(!$r['status']) {
            $hasError = true;
            $error = $r['info'];
          }
        }
      }
      if($hasError){
        Db::rollback();
        return ['status'=>false,'info'=>$error];
      }else{
        Db::commit();
        return ['status'=>true,'info'=>lang('success')];
      }

    }
    /**
     * 订单发货操作
     * @author hebidu <email:346551990@qq.com>
     * @param Orders $orders
     * @param OrdersExpress $ordersExpress
     * @return \app\src\base\logic\status|array|bool
     */
    public function shipped(Orders $orders,OrdersExpress $ordersExpress){

        $result = $this->getInfo(['order_code'=>$orders->getOrderCode()]);

        if(!ValidateHelper::legalArrayResult($result)){
            return $this->apiReturnErr(lang("err_order_code"));
        }

        $order_info = $result['info'];

        //不是已发货
        if($order_info['order_status'] != Orders::ORDER_TOBE_SHIPPED){
            return $this->apiReturnErr(lang("err_order_status"));
        }

        //不是已支付
        if($order_info['pay_status'] != Orders::ORDER_PAID){
            return $this->apiReturnErr(lang("err_pay_status"));
        }

        //1. 开启事务
        Db::startTrans();
        $hasError  = false;
        $error  = "";
        $map    = ['uid'=>$orders->getUid(),'order_code'=>$orders->getOrderCode()];
        $update = ['order_status'=>Orders::ORDER_SHIPPED,'updatetime'=>time()];

        $result = $this->save($map,$update);

        if(!$result['status']){
            $hasError = true;
            $error = $result['info'];
        }else{

            $entity = $ordersExpress->getPoArray();
            $ordersExpressLogic = (new OrdersExpressLogic());
            $result = $ordersExpressLogic->getInfo(['order_code'=>$ordersExpress->getOrderCode()]);

            if(ValidateHelper::legalArrayResult($result)) {
                unset($entity['order_code']);
                unset($entity['uid']);
                $result = $ordersExpressLogic->saveByID($result['info']['id'],$entity);
            }else{
                $result = $ordersExpressLogic->add($entity);
            }

            if(!$result['status']) {
                $hasError = true;
                $error = $result['info'];
            }
        }

        if($hasError){
            Db::rollback();
            return ['status'=>false,'info'=>$error];
        }else{
            Db::commit();
            return ['status'=>true,'info'=>lang('success')];
        }

    }

    /**
     * 确认收货订单
     * @author hebidu <email:346551990@qq.com>
     * @param Orders $orders
     * @return \app\src\base\logic\status|array|bool
     */
    public function receiveGoods(Orders $orders){

        $result = $this->getInfo(['order_code'=>$orders->getOrderCode()]);

        if(!ValidateHelper::legalArrayResult($result)){
            return $this->apiReturnErr(lang("err_order_code"));
        }

        $order_info = $result['info'];

        //不是已发货
        if($order_info['order_status'] != Orders::ORDER_SHIPPED){
            return $this->apiReturnErr(lang("err_order_status"));
        }

        //不是已支付
        if($order_info['pay_status'] != Orders::ORDER_PAID){
            return $this->apiReturnErr(lang("err_pay_status"));
        }

        $map    = ['uid'=>$orders->getUid(),'order_code'=>$orders->getOrderCode()];
        $update = ['order_status'=>Orders::ORDER_RECEIPT_OF_GOODS,'updatetime'=>time()];

        $result = $this->save($map,$update);

        return $result;
    }


    /**
     * 商城订单支付成功 - 回调处理 - 外部请加事务
     * 已知支持 : 微信 余额
     * @Author
     * @DateTime 2016-12-29T15:25:01+0800
     * @param    string     $pay_code  [支付编码]
     * @param    int        $pay_money [支付金额,分]
     * @param    int        $pay_type  [支付类型]
     * @param    string     $trade_no  [暂未使用]
     * @return   apiReturn  [处理结果 一般包含订单 - 写入到第三方支付日志]
     */
    public function paySuccessCall($pay_code='',$pay_money=0,$pay_type,$trade_no=''){
      $now   = time();
      $logic = new OrdersPaycodeLogic();
      //? 支付码
      $r = $logic->getInfo(['pay_code'=>$pay_code]);
      if($r['status'] && $r['info']){
        $r = $r['info'];
        $uid           = $r['uid'];
        $pay_status    = $r['pay_status'];
        $order_content = $r['order_content'];
        if(!$order_content) return returnErr('未知订单[order_content null]');
        //? 处理过
        if(!$pay_status){
          $orders = explode(',', $order_content);

          //业务开始
          foreach ($orders as $v) {
            //订单状态修改
            $this->save(['order_code'=>$v],['pay_status'=>1,'updatetime'=>$now,'pay_type'=>$pay_type,'pay_code'=>$pay_code,'order_status'=>Orders::ORDER_TOBE_SHIPPED]);

            //写入订单历史
            $map = [
              'reason'      =>'支付订单',
              'create_time' =>$now,
              'isauto'      =>0,
              'order_code'  =>$v,
              'cur_status'  =>Orders::ORDER_TOBE_CONFIRMED,
              'next_status' =>Orders::ORDER_TOBE_SHIPPED,
              'status_type' =>'PAY',
              'operator'    =>$uid,
            ];
            $r = (new OrderStatusHistoryLogic())->add($map);
            if(!$r['status']) return returnErr($r['info']);
          }
          // 支付信息修改
          $logic->save(['pay_code'=>$pay_code],['pay_type'=>$pay_type,'true_pay_money'=>$pay_money,'pay_status'=>1,'trade_no'=>$trade_no,'update_time'=>$now]);
          // 非积分支付 返现积分
          $r = $this->rebateScore($pay_money,$pay_type,$uid);
          if(!$r['status']) return returnErr($r['info
            ']);
          //库存减少放到订单创建 库存增加放到订单取消关闭
            //$this->minusQuantity($order_content);
          return returnSuc(['uid'=>$uid,'msg'=>$order_content]);
        }else{
          return returnErr('重复支付['.$order_content.']');
        }
      }
      return returnErr('未知支付码['.$pay_code.']');
    }
    /**
     * 非积分支付返现
     * pay_money : 分，实际支付
     * pay_type  : 支付类型
     */
    public function rebateScore($pay_money,$pay_type,$uid){
      $score = 0;
      if(intval($pay_type) != PayType::SCORE){
        // 查询返现比例
        $r = (new ScoreHisLogicV2)->getOpScore(ScoreHisLogicV2::PAY_ADD);
        if(!$r['status']) return $r;
        $score = floor(floatval($r['info']) * intval($pay_money)/100);
      }
      if($score){
        // 用户+积分
        $r = (new ScoreHisLogicV2)->addScore($uid,$score,ScoreHisLogicV2::PAY_ADD,'购物返积分');
        if(!$r['status']) return $r;
      }
      return returnSuc($score);
    }
    /**
     * 商品减库存操作
     * @param $order_content
     * @internal param $order_code
     */
    private function minusQuantity($order_content){
        //减库存操作
        $result = (new OrdersItemLogic())->queryNoPaging(['order_code'=>['in',$order_content]]);
        $skuLogic = new ProductSkuLogic();
        $orderItems = $result['info'];
        foreach ($orderItems as $item){
            $cnt = $item['count'];
            $psku_id = $item['psku_id'];
            $skuLogic->setDec(['id'=>$psku_id],"quantity",$cnt);
        }

    }

    /**
     * 订单创建商品减少相应库存
     * @param $orderItems
     */
    private function minusQuantityByItems($orderItems)
    {
        $skuLogic = new ProductSkuLogic();
        foreach ($orderItems as $item){
            $cnt = $item['count'];
            $psku_id = $item['psku_id'];
            $result = $skuLogic->setDec(['id'=>$psku_id],'quantity',$cnt);
            if(!$result['status']) return ResultHelper::error('fail');
        }
        return ResultHelper::success('success');

    }


    /**
     * 订单取消关闭恢复库存
     * @param $orderItems
     */
    public function recoverQuantity($order_code)
    {
        $result = (new OrdersItemLogic)->queryNoPaging(['order_code'=>$order_code]);
        if(!$result['status']){
            return ResultHelper::error($result['info']);
        }
        $orderItems = $result['info'];
        $skuLogic = new ProductSkuLogic();

        foreach ($orderItems as $item){
            $cnt = $item['count'];
            $psku_id = $item['psku_id'];
            $result = $skuLogic->setInc(['id'=>$psku_id],'quantity',$cnt);
            if(!$result['status']) return ResultHelper::error('fail');
        }
        return ResultHelper::success('success');

    }

    /**
     * 订单支付成功
     * @param $order_info
     * @param $pay_info
     */
    public function paySuccess($order_info,$pay_info){
        //1. 订单已支付，则返回
        if($order_info['pay_status'] == Orders::ORDER_PAID){
            return $this->apiReturnErr('payed');
        }

        $update = [
            'pay_status'=>Orders::ORDER_PAID,
            'pay_type'=>$pay_info['pay_type'],
            'pay_code'=>$pay_info['pay_code'],
            'pay_balance'=>$pay_info['pay_balance'],
            'updatetime'=>time()
        ];

        $map = [
            'uid'=>$order_info['uid'],
            'order_code'=>$order_info['order_code']
        ];

        return $this->save($map,$update);

    }


    /**
     * 完成订单
     * @param Orders $orders
     * @return \app\src\base\logic\status|array|bool
     */
    public function autoCompleteOrder($orders){

        if(!isset($orders['uid']) || !isset($orders['order_code'])){
            return false;
        }

        if($orders['order_status'] != Orders::ORDER_RECEIPT_OF_GOODS){
            return $this->apiReturnErr(lang("err_order_status"));
        }

        if($orders['pay_status'] != Orders::ORDER_PAID){
            return $this->apiReturnErr(lang("err_pay_status"));
        }
        $map = ['uid'=>$orders['uid'],'order_code'=>$orders['order_code']];

        $result = $this->save($map,['order_status'=>Orders::ORDER_COMPLETED,'updatetime'=>time()]);

        return $result;
    }
    /**
     * 自动关闭订单
     * @param Orders $orders
     * @return \app\src\base\logic\status|array|bool
     */
    public function autoCloseOrder($orders){
        return $this->cancel($orders);
    }

    /**
     * 取消订单
     * 同时退回积分 2017-07-26 15:48:28
     * @author hebidu <email:346551990@qq.com>
     * @param Orders $orders
     * @return \app\src\base\logic\status|array|bool
     */
    public function cancel(Orders $orders){
        Db::startTrans();

        $order_code   = $orders->getOrderCode();
        $order_status = (int) $orders->getOrderStatus();
        $pay_status   = (int) $orders->getPayStatus();
        $score        = (int) $orders->getScore();
        $uid          = (int) $orders->getUid();

        if($order_status != Orders::ORDER_TOBE_CONFIRMED){
            return returnErr(L("err_order_status"),true);
        }
        if($pay_status != Orders::ORDER_TOBE_PAID){
            return returnErr(L("err_pay_status"),true);
        }
        $save = ['order_status'=>Orders::ORDER_CANCEL,'updatetime'=>time()];
        // 恢复积分
        if($score>0){
          $save['score']     = 0;
          $save['score_pay'] = 0;
          // + 用户积分
          $r = (new ScoreHisLogicV2)->addScore($uid,$score,ScoreHisLogicV2::PAY_CUT,'取消订单退回积分');
          if(!$r['status']) return returnErr($r['info'],true);
        }
        // 修改订单
        $map = ['uid'=>$uid,'order_code'=>$order_code];
        $r = $this->save($map,$save);
        if(!$r['status']) return returnErr($r['info'],true);
        //恢复库存
        $r = $this->recoverQuantity($order_code);
        if(!$r['status']) return returnErr($r['info'],true);

        Db::commit();
        return $r;
    }

    /**
     * 退回订单
     * 同时退回积分 2017-07-26 15:56:03
     * @param Orders $orders
     * @return array|string
     */
    public function backOrder(Orders $orders){
        $order_code   = $orders->getOrderCode();
        $order_status = (int) $orders->getOrderStatus();
        $pay_status   = (int) $orders->getPayStatus();
        $score        = (int) $orders->getScore();
        $uid          = (int) $orders->getUid();

        if($order_status != Orders::ORDER_TOBE_CONFIRMED){
            return $this->apiReturnErr(lang("err_order_status"));
        }

        $map  = ['uid'=>$uid,'order_code'=>$order_code];
        $save = ['order_status'=>Orders::ORDER_BACK,'updatetime'=>time()];
        Db::startTrans();
        // 恢复积分
        if($score>0){
          $save['score']     = 0;
          $save['score_pay'] = 0;
          // + 用户积分
          $r = (new ScoreHisLogicV2)->addScore($uid,$score,ScoreHisLogicV2::PAY_CUT,'退回订单退回积分');
          if(!$r['status']) return returnErr($r['info'],true);
        }
        $r = $this->save($map,$save);
        if(!$r['status']) return returnErr($r['info'],true);
        //恢复库存
        $r = $this->recoverQuantity($orders->getOrderCode());
        if(!$r['status']) return returnErr($r['info'],true);

        Db::commit();
        return $r;
    }

    /**
     * 添加订单信息
     * @author hebidu <email:346551990@qq.com>
     * @param $items
     * @param Orders $orders
     * @param OrdersContactinfo $contactInfo
     * @return array
     * @throws \Exception
     */
    public function addOrder($items,Orders $orders,OrdersContactinfo $contactInfo){

        Db::startTrans();
        $flag = true;
        $info = "";
        $result = $this->add($orders->getModelArray());

        if(!$result['status']){
            $flag = false;
            $info = empty($result['info'])? lang('err_add_order_info_fail'):$result['info'];
        }
        $info = $result['info'];

        $result = $contactInfo -> data($contactInfo->getModelArray()) ->isUpdate(false) -> save();

        if ($result === false) {
            $flag = false;
            $info = $contactInfo -> getError();
        }

        $order_items = [];
        foreach ($items as $item){
            if($item instanceof OrdersItem){
                array_push($order_items ,  $item->getModelArray() );
            }
        }

        $ordersItemModel = new OrdersItem();

        $result = $ordersItemModel->saveAll($order_items,true);

        if ($result === false) {
            $flag = false;
            $info = $ordersItemModel -> getError();
        }

        //库存减少
        $result = $this->minusQuantityByItems($order_items);
        if ($result === false) {
            $flag = false;
            $info = $ordersItemModel -> getError();
        }

        if($flag){
            Db::commit();
            return $this->apiReturnSuc($info);
        }else{
            Db::rollback();
            return $this->apiReturnErr($info);
        }
    }

    /**
     * 获取订单信息包含订单拥有者昵称
     * @param $map
     * @return array
     */
    public function getInfoWithPublisherName($map){

        try{

            $result = Db::table("itboye_orders")->alias("orders")
                ->field("oc.city,oc.postal_code,oc.id_card,oc.detailinfo,oc.mobile,oc.area,oc.province,oc.contactname,oc.country,orders.*,m.nickname as publisher_name")
                ->join(["itboye_orders_contactinfo"=>"oc"],"oc.order_code = orders.order_code","LEFT")
            ->join(["itboye_store"=>"store"],"store.id = orders.storeid","LEFT")
            ->join(["common_member"=>"m"],"m.uid = store.uid","LEFT")
                ->where($map)
            ->find();

            return $this->apiReturnSuc($result);
        }catch (DbException $ex){
            return $this->apiReturnErr($ex->getMessage());
        }
    }
}