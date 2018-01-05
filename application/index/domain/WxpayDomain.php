<?php
/**
 * 周舟 hzboye010@163.com
 * addby sublime snippets
 */

namespace app\domain;

use app\src\repair\logic\RepairOrderLogicV2;
use app\src\order\logic\OrdersLogic;
use app\src\wallet\logic\WalletOrderLogicV2;
use app\src\order\logic\OrdersPaycodeLogic;
use app\src\order\model\OrdersPayCode;
use app\src\wxpay\service\WxpayService;

class WxpayDomain extends BaseDomain {

    /**
     * 微信预支付
     */
    public function prePay(){
      $this->checkVersion(100);

      $pay_code  = $this->_post('pay_code','',Llack('pay_code'));
      $is_worker  = (int) $this->_post('is_worker',0);
      $order_type = substr($pay_code,0,2);
      if($order_type == 'PA'){        //商品微信支付

        //? 未支付订单
        $r = (new OrdersPaycodeLogic())->getInfo(['pay_code'=>$pay_code,'pay_status'=>OrdersPaycode::PAY_STATUS_NOT_PAYED],false,'uid,pay_money,order_content');
        if(!$r['status'])     $this->apiReturnErr($r['info']);
        if(empty($r['info'])) $this->apiReturnErr(L('fail').' order_pay_code err : pay_code or pay_status');
        $order_code     = $r['info']['order_content'];
        $orderCodeArray = explode(',',$order_code);
        $tade_no   = $pay_code;
        $orderBody = '';
        $total_fee = 0; //分
        $logic = new OrdersLogic();
        foreach ($orderCodeArray as $order_code) {
          //查询订单
          $r = $logic->getInfo(['order_code'=>$order_code]);
          if(!$r['status'])     $this->apiReturnErr($r['info']);
          if(empty($r['info'])) $this->apiReturnErr(L('fail').' order err : order_code');
          $order_info = $r['info'];
          if($order_info['pay_status'] >0) $this->apiReturnErr(L('fail').' order err : pay_status');
          $orderBody .= $order_info['note'].'('.$order_code.')';
          //不用管已支付,订单总金额，单位为分
          $total_fee += intval($order_info['price']) - intval($order_info['score_pay']);
        }
        // $orderBody = $orderBody ? $orderBody: $tade_no;
        //写入pay_code
        // $result = apiCall(OrdersApi::SAVE,[['order_code'=>$order_code],['pay_code'=>$code]]);
        // echo $total_fee;exit;
      }elseif( $order_type=='WX' ){        // 维修订单微信支付

        //? 待支付维修订单
        $r = (new RepairOrderLogicV2())->getInfo(['pay_code'=>$pay_code,'pay_status'=>RepairOrderLogicV2::TOBE_PAY]);
        if(!$r) $this->apiReturnErr(Linvalid('pay_code or pay_status'));
        $total_fee = intval($r['money']) - intval($r['score_pay']);
        $orderBody = '维修订单'.$pay_code.' - 微信支付';
      }elseif( $order_type=='WC'){        // 余额充值微信支付

        //? 待支付余额充值订单
        $r = (new WalletOrderLogicV2())->getInfo(['pay_code'=>$pay_code,'pay_status'=>WalletOrderLogicV2::TOBE_PAY]);
        if(!$r) $this->apiReturnErr(Linvalid('pay_code or pay_status'));
        $total_fee = intval($r['money']);
        $orderBody = '余额充值订单'.$pay_code.' - 微信支付';
      }else{
        $this->apiReturnErr(Linvalid('pay_code'));
      }

      $r = (new WxpayService($is_worker))->getPrePayOrder($orderBody, $pay_code, $total_fee);
      $r = $this->parseWxReturn($r,$is_worker);
      $this->exitWhenError($r,true);
    }
    /**
     * 解析维修返回
     * @Author
     * @DateTime 2016-12-22T14:47:50+0800
     * @param    array                    $r [description]
     * @return   [type]                      [description]
     */
    private function parseWxReturn(array $r,$is_worker=false){
      if($r['return_code'] == 'FAIL'){
        return returnErr(L('fail').' 微信后台 : '.$r['return_msg']);
      }elseif($r['return_code'] == 'SUCCESS'){
        if($r['result_code'] == 'SUCCESS'){
          return returnSuc((new WxpayService($is_worker))->reSign($r['prepay_id']));
        }else{
          return returnErr(L('fail').' 微信后台 : '.$r['err_code_des']);
        }
      }else{
        return returnErr(L('fail').' 微信后台 : 返回未知错误');
      }
    }
}