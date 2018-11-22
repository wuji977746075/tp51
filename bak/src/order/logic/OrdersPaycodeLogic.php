<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-10
 * Time: 21:26
 */

namespace app\src\order\logic;


use app\pc\controller\Order;
use app\src\base\helper\ConfigHelper;
use app\src\base\helper\ValidateHelper;
use app\src\base\logic\BaseLogic;
use app\src\base\utils\CodeGenerateUtils;
use app\src\order\model\OrdersPaycode;
use app\src\order\model\Orders;
use think\Db;

class OrdersPaycodeLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new OrdersPaycode());
    }

    /**
     * 设置为支付中...
     * @param $uid
     * @param $trade_no
     * @param $pay_code
     * @param $total
     * @param $currency
     * @param $pay_type
     * @return \app\src\base\logic\status|array|bool
     */
    public function paying($uid,$trade_no,$pay_code,$total,$currency,$pay_type){

        if($total <= 0){
            return $this->apiReturnErr(lang('err_pay_code_illegal_money'));
        }

        //TODO: 检查pay_type,currency 是否合法
        $map = ['uid'=>$uid,'pay_code'=>$pay_code];

        $result = (new OrdersPaycodeLogic())->getInfo($map,false,false,false,true);

        if(ValidateHelper::legalArrayResult($result)) {
            //检查状态
            $payInfo = $result['info'];

            if($payInfo['pay_status'] == OrdersPaycode::PAY_STATUS_PAYED){
                return $this->apiReturnErr(lang('err_pay_code_payed'));
            }

            if($payInfo['pay_status'] == OrdersPaycode::PAY_STATUS_PAYING){
                return $this->apiReturnErr(lang('err_pay_code_paying'));
            }

            $update = [
                'trade_no' => $trade_no,
                'pay_type'=>$pay_type,
                'pay_currency' => $currency,
                'pay_money' => $total,
                'update_time'=>time(),
                'pay_status'=>OrdersPaycode::PAY_STATUS_PAYING //正在支付中...
            ];

            $result = $this->save($map, $update);
            return $result;
        }else{
            return $this->apiReturnErr(lang('fail'));
        }
    }

    /**
     * 设置支付成功
     * @param $trade_no
     * @param $out_trade_no
     * @param $pay_type
     * @param $b_status
     * @param $total_fee
     * @return \app\src\base\logic\status|bool
     */
    public function paySuccess($trade_no,$out_trade_no,$pay_type,$b_status,$total_fee){

        $updateEntity = [
            'pay_type'=>$pay_type,
            'pay_status'=>OrdersPaycode::PAY_STATUS_PAYED,
            'update_time'=>time(),
            'trade_no'=>$trade_no,
            'b_status'=>$b_status,
            'true_pay_money'=>$total_fee
        ];
        $map = ['pay_code'=>$out_trade_no];

        $result = $this->save($map,$updateEntity);

        return $result;
    }

    /**
     * 获取支付信息 - 发起支付/重支付
     * @param $uid         int    用户id
     * @param $orderCodes  array  订单编号数组
     * @param $payMoney    int    支付额,分
     * @param $currency    string 币种
     * @param $discount    int    支付信息校正,分
     * @return array
     */
    public function getPayInfo($uid,$orderCodes,$payMoney,$currency,$discount=0){
        if(!$orderCodes) return $this->apiReturnErr(lang('err_order_code').'1');
        //查询第一个订单时间
        $r = (new Orders())->where(['order_code'=>$orderCodes[0]])->find();
        if(!$r) return $this->apiReturnErr(lang('err_order_code').'2');

        if($r['pay_status'] != Orders::ORDER_TOBE_PAID)  {
           return $this->apiReturnErr('该订单支付状态非法，无法重新支付');
        }

        $order_ctime = $r['createtime'];

        $now   = time();
        $utils = new CodeGenerateUtils();
        $order_content = implode(",",$orderCodes);
        $payCode = $utils->getAppPayCode($uid);
        $entity = [
            'order_content' =>$order_content,
            'createtime'    =>$now,
            'update_time'   =>$now,
            'uid'           =>$uid,
            'pay_type'      =>0,
            'trade_no'      =>'',
            'pay_money'     =>$payMoney,
            'pay_code'      =>$payCode,
            'pay_balance'   =>0,
            'pay_status'    =>0,
            'pay_currency'  =>$currency
        ];

        $result = $this->add($entity);
        if($result['status'] && $result['info'] > 0){
            // $payInfo = ['pay_money'=>$payMoney ,'pay_code'=> $payCode];
            // $sign = $this->sign($uid,$payMoney,$payCode);
            // $payInfo['sign'] = $sign;
            // $payInfo['pay_money_usd'] = number_format($payMoney  * ConfigHelper::getUsdRate(),0,"","");

            return $this->apiReturnSuc($utils->getPayInfo($uid,$payCode,$payMoney-$discount,$order_ctime));

        }

        return $this->apiReturnErr(lang('err_get_pay_info'));
    }

}