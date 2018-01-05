<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-10
 * Time: 19:04
 */

namespace app\src\base\utils;

/**
 * Class CodeGenerateUtils
 * 系统用来生成唯一码或者其它编码用的工具
 * @package app\src\base\utils
 */
class CodeGenerateUtils
{
    /**
     * 获取订单编号 uid
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @return string
     */
    public function getOrderCode($uid){
        $rand = mt_rand(1000000, 9999999);
        $orderID = date("yzHis",time());
        return "T".$orderID.$rand.get_36HEX($uid);
    }

    /**
     * 获取维修订单编号 uid
     * @param $uid
     * @return string
     */
    public function getRepairOrderCode($uid){
        $rand = mt_rand(1000000, 9999999);
        $orderID = date("yzHis",time());
        return "W".$orderID.$rand.get_36HEX($uid);
    }

    /**
     * 获取余额充值订单编号 uid
     * @param $uid
     * @return string
     */
    public function getWalletOrderCode($uid){
        $rand = mt_rand(1000000, 9999999);
        $orderID = date("yzHis",time());
        return "C".$orderID.$rand.get_36HEX($uid);
    }

    /**
     * 获取支付编号
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @return string
     */
    public function getAppPayCode($uid){
        $rand = mt_rand(1000000, 9999999);
        $orderID = date("yzHis",time());

        return "PA".$orderID.$rand.get_36HEX($uid);
    }

    /**
     * 获取维修支付编号
     * @param $uid
     * @return string
     */
    public function getRepairPayCode($uid){
        $rand = mt_rand(1000000, 9999999);
        $orderID = date("yzHis",time());

        return "WX".$orderID.$rand.get_36HEX($uid);
    }

    /**
     * 获取余额充值支付编号
     * @param $uid
     * @return string
     */
    public function getWalletPayCode($uid){
        $rand = mt_rand(1000000, 9999999);
        $orderID = date("yzHis",time());

        return "WC".$orderID.$rand.get_36HEX($uid);
    }

    /**
     * 组装支付信息
     * @Author
     * @DateTime 2016-12-22T15:09:09+0800
     * @param    [type]                   $uid      [description]
     * @param    [type]                   $code     [支付编号]
     * @param    [type]                   $price    [description]
     * @param    [type]                   $time     [description]
     * @return   [type]                             [description]
     */
    public function getPayInfo($uid,$code,$price,$time){
      $r = [
        // 'uid'        => $uid,
        'pay_money'  => $price,
        'pay_code'   => $code,
        'create_time'=> $time,
        'sign'       => md5(strval($uid).strval($price).$code.strval($time)),
      ];
      return $r;
    }
}