<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-05
 * Time: 16:37
 */
namespace app\index\controller;

use app\src\alipay\action\AlipayNotifyAction;
use app\src\alipay\po\AlipayNotifyPo;
use think\Controller;

/**
 * 支付宝
 * Class Alipay
 * @author hebidu <email:346551990@qq.com>
 * @package app\index\controller
 */
class Alipay extends Controller{


    public function notify(){

      $arr   = $_POST;
      $debug = false;
      // $arr = ["total_amount"=>"0.10","buyer_id"=>"2088702863925242","body"=>"\u6613\u5fae\u542c","trade_no"=>"2017061321001004240274916480","notify_time"=>"2017-06-13 09:17:42","subject"=>"PA1716309173466220254B","sign_type"=>"RSA2","buyer_logon_id"=>"102***@qq.com","auth_app_id"=>"2017060607433248","charset"=>"utf-8","notify_type"=>"trade_status_sync","invoice_amount"=>"0.10","out_trade_no"=>"PA1716309173466220254B","trade_status"=>"TRADE_SUCCESS","gmt_payment"=>"2017-06-13 09:17:41","version"=>"1.0","point_amount"=>"0.00","sign"=>"covfc1OISdzpqeOWH56D1vCZDhEsUCaX7y52tAt\/W4XbmFAbNEP5TEj+dHYtCo98CU+kb3+aI444BLvp36h54RknXqK8dINYBxC42BKQgsG7yWSHF49SvpmOz3AdkrXO1f3J8hZIM8tZTvsZ5Cj1TowER5YOwEukfc2bGeAMuuQFPUROP51pE6dJhR5KP1uM1l3Zsf598EmeqMtudjkCJBe0weflGIWkqsG\/7RjY6GPLUALUJvRxyqNkBS5c\/2Yv5xEjQ0dZppWJqQWO47BUIRCgse7asD6Wvax7XqWRxHCoUcPgWGogbFdr8+eFGaW1h6dJvzmvvMf3Azjm2twb\/Q==","gmt_create"=>"2017-06-13 09:17:41","buyer_pay_amount"=>"0.10","receipt_amount"=>"0.10","fund_bill_list"=>"[{\"amount\":\"0.10\",\"fundChannel\":\"ALIPAYACCOUNT\"}]","app_id"=>"2017060607433248","seller_id"=>"2088721193827275","notify_id"=>"e96b8c3e72ba5f9ba1f9eff2c51863ahuq","seller_email"=>"2674584365@qq.com"];
      addLog("Alipay_notify",$_GET,$arr,"支付宝异步通知");

      $alipayNotfiyPo = new AlipayNotifyPo();
      $action = new AlipayNotifyAction($alipayNotfiyPo);
      $alipayNotfiyPo->init($arr); //对象做成
      // $result =  $action->notify($debug);
      $result =  $action->notify($arr,$debug);

      echo $result['info'];
    }


}