<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2016-12-23 09:29:37
 * Description : [支付回调处理]
 */

namespace app\index\controller;

use app\src\order\logic\OrdersLogic;
use app\src\repair\logic\RepairLogicV2;
use app\src\wallet\logic\WalletLogic;
use app\src\wxpay\logic\WxpayNotifyLogicV2;
use app\src\wxpay\service\WxpayService;
use think\Controller;
use think\Db;

/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\
 * @example
 */
class Payback extends Controller{
  const WEIXIN = 3;
  const ALIPAY = 1;

  //模拟支付完成 - test only
  public function test(){
    $result  = array();
    $now     = time();
    $service = new WxpayService();

    //test only
    $pay_type = isset($_GET['pay_type']) ? (int) $_GET['pay_type'] : $this->exits('pay_type');
    $pay_code = isset($_GET['pay_code']) ? $_GET['pay_code'] : $this->exits('pay_code');
    $fee      = isset($_GET['fee']) ? max(intval($_GET['fee']),1) : 1;
    if(!in_array($pay_type,[self::WEIXIN,self::ALIPAY])) $this->exits('invalid pay_type');

    $data = $this->getTestData($pay_type,$pay_code,$fee);
    if($pay_type === self::WEIXIN){
        $this->exits('微信支付已接入,请使用1分钱真实支付');
        // $trade_status = $data['result_code'];//交易状态
        // addLog('Payback/wxpay','parsed data',$data,'微信支付回调:模拟');
        // if ($trade_status == 'SUCCESS') {
        //   //支付成功
        //   Db::startTrans();
        //   $order = $this->paySuccess($data);
        //   $this->addWeixinNotify($data,$order);
        //   Db::commit();
        //   $result['return_code'] = "SUCCESS : ".$order;
        // }
        // exit(dump($result));
    }elseif($pay_type === self::ALIPAY){
        $this->exits('暂未实现或不在此处');
    }else{
        $this->exits('unsupported pay_type');
    }
  }
  //获取 微信支付回调 模拟数据 - test only

    private function exits($str = '')
    {
        exit('nothing to say : ' . $str);
    }

    //微信支付回调

  private function getTestData($pay_type=0,$pay_code='',$fee = 1){
      if (empty($pay_code)) $pay_code = 'WX163561533568875476V';
      //PA1635615280173426951  - 商城
      //WX163561533568875476V  - 维修
      //WC16363112222557575201 - 余额充值
    if($pay_type === self::WEIXIN){
      return [
        "appid"          =>"wx28fe69f36a61056b",
        "bank_type"      =>"CFT",
        "cash_fee"       =>"".$fee,
        "fee_type"       =>"CNY",
        "is_subscribe"   =>"N",
        "mch_id"         =>"1294387501",
        "nonce_str"      =>"yn7LWRWZRooP2wBNJBHXOl4vPneUX1VW",
        "openid"         =>"oBuUFwSyN0j2yNZDHVKsXTHW7tFI",
        "out_trade_no"   =>$pay_code,
        "result_code"    =>"SUCCESS",
        "return_code"    =>"SUCCESS",
        "sign"           =>"78C4204ED01AA0161FD344EE6A250FB9",
        "time_end"       =>"20160702093808",
        "total_fee"      =>"".$fee,
        "trade_type"     =>"APP",
        "transaction_id" =>"4010162001201607028225413779"
      ];
    }elseif($pay_type === self::ALIPAY){
      return [];
    }else{
      return [];
    }
  }

  //支付宝回调 - 未使用
  public function alipay(){

  }

  //微信回调
  public function wxpay(){
      $result  = array();
      $now     = time();
      $service = new WxpayService();

      $test = false;// ? 测试 & 验证签名
      if($test){
          $data = $this->getTestData(self::WEIXIN, '');
      }else{
        //获取回调信息
        $streamData = isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if(empty($streamData)) exit('nothing to say');
        $data = $service->xmlstr_to_array($streamData);
      }
      addLog('Payback/wxpay','parsed data',$data,'微信支付回调');
      if($test || $service->checkSign($data)) {
        $trade_status = $data['result_code'];//交易状态
        //签名验证成功
        if ($trade_status == 'SUCCESS') {
          //支付成功
          Db::startTrans();
          //订单支付成功
          $order = $this->paySuccess($data);
          //写入第三方支付信息
          $this->addWeixinNotify($data,$order);
          Db::commit();
          $result['return_code'] = "SUCCESS";
        }
      } else {
        addLog('Payback/wxpay','签名验证失败',$sign,'微信支付回调');
        //验证失败
        $result['return_code'] = "FAIL";
        $result['return_msg']  = '签名失败';
        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
      }
      exit($service->arrayToXml($result));
  }

    //写入微信回调记录

  /**
   * 各类订单 - 微信支付成功处理 - 外部请加事务
   * @Author
   * @DateTime 2016-12-29T15:31:55+0800
   * @param    array                    $data [description]
   * @return   [type]                         [description]
   */
  private function paySuccess(array $data){
    $now = time();

    $out_trade_no = $data['out_trade_no'];
    $pay_code     = $out_trade_no;//本地支付单号
    $trade_no     = $data['transaction_id'];
    $total_fee    = $data['total_fee'];//

    $order_type = substr($pay_code,0,2);
    if($order_type == 'PA'){         //商城订单
      $r = (new OrdersLogic())->paySuccessCall($pay_code,$total_fee,self::WEIXIN,$trade_no);
    }elseif($order_type == 'WX'){    //维修订单
      $r = (new RepairLogicV2())->paySuccess($pay_code,$total_fee,self::WEIXIN,$trade_no);
    }elseif($order_type == 'WC'){    //余额充值
      $r = (new WalletLogic())->paySuccess($pay_code,$total_fee,self::WEIXIN,$trade_no);
    }else{
      return '未知支付码['.$pay_code.']';
    }
    return $r['status'] ? $r['info']['msg'] : $r['info'];
  }

    //private log

  private function addWeixinNotify($data,$order){
    $his = [
      'sign'           => $data['sign'],
      'openid'         => $data['openid'],
      'trade_type'     => $data['trade_type'],
      'bank_type'      => $data['bank_type'],
      'total_fee'      => $data['total_fee'],
      'cash_fee'       => $data['cash_fee'],
      'transaction_id' => $data['transaction_id'],
      'out_trade_no'   => $data['out_trade_no'],
      'time_end'       => $data['time_end'],
      'extra'          => $order.';支付成功',
    ];
    //写入微信回调记录
    (new WxpayNotifyLogicV2())->add($his);
  }
}