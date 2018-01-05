<?php
/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/2/9
 * Time: 下午3:25
 */

namespace app\src\wxpay\action;

use app\src\base\action\BaseAction;
use app\src\hook\PaySuccessHook;
use app\src\order\enum\PayCurrency;
use app\src\order\enum\PayType;

require_once(VENDOR_PATH.'WxPayApiV3/lib/WxPay.Api.php');
require_once(VENDOR_PATH.'WxPayApiV3/lib/WxPay.Notify.php');

class WxPayJsNotifyAction extends \WxPayNotify
{
    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = \WxPayApi::orderQuery($input);
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
        $notfiyOutput = array();

        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }
        //支付成功
        $mch_id = $data['mch_id'];
        $cash_fee = $data['cash_fee'];
        $out_trade_no = $data['attach'];
        $trade_no = $data['out_trade_no'];
        $this->paySuccess($mch_id, $cash_fee, $out_trade_no, $trade_no);
        return true;
    }

    /**
     * 支付成功
     * @param $seller_id
     * @param $total_fee
     * @param $out_trade_no
     * @return array
     */
    private function paySuccess($seller_id,$total_fee,$out_trade_no,$trade_no){

        //1. 对支付宝默认其货币为: 人民币：元，所以 * 100 转换为分
        //2. 微信默认货币分 所以不 * 100
        $result =  (new PaySuccessHook())->success($seller_id,$total_fee,$out_trade_no,PayCurrency::RMB,$trade_no,PayType::WXPAY_MP);
        return $result;
    }
}