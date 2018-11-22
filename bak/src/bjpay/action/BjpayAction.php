<?php

/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/2/17
 * Time: 下午1:50
 */

namespace app\src\bjpay;

use app\src\base\action\BaseAction;
use app\src\bjpay\po\BjLoginReqPo;
use app\src\bjpay\po\BjLogoutReqPo;
use app\src\bjpay\po\BjSinglePaymentTradeReqPo;
use app\src\bjpay\request\BjRequest;
use app\src\bjpay\utils\SerialNoUtils;

class BjpayAction extends BaseAction
{
    private $UserId = 'Cloud';
    private $UserPsw = '000000';

    /**
     * 登陆
     * @return array
     */
    public function login(){
        $serialNo = SerialNoUtils::generate(10);
        $po = new BjLoginReqPo();
        $po->setSerialNo($serialNo);
        $po->setUserId('Cloud');
        $po->setUserPwd('000000');
        $result = BjRequest::login($po);
        return $result;
    }

    /**
     * 退出
     * @return array
     */
    public function logOut(){
        $sessionId = BjRequest::getSessionID();
        if(!empty($sessionId)){
            $po = new BjLogoutReqPo();
            $po->setDseSessionId($sessionId);
            BjRequest::logout($po);
        }
        return $this->success('签退成功');
    }


    /**
     * 单笔实时代收付交易
     * @param $perAcc //银行卡号
     * @param $perName //户名
     * @param $certNum //身份证好
     * @param $payAmt //发生额 最大长度为12位无符号数字串,后两位表示小数
     * @param string $userRem 对公摘要 最大长度为50位
     * @return array
     */
    public function singlePaymentTrade($perAcc, $perName, $certNum, $payAmt, $userRem = ""){
        $sessionId = BjRequest::getSessionID();
        if(empty($sessionId)){
            $result = $this->login();
            if(!$result['status']){
                return $this->error($result['info']);
            }
        }
        $payAmt = intval($payAmt);
        if(strlen($userRem)>50) $userRem = strstr($userRem, 50);

        $serialNo = SerialNoUtils::generate(10);
        $po = new BjSinglePaymentTradeReqPo();
        $po->setDseSessionId(BjRequest::getSessionID());

        $po->setSerialNo($serialNo);
        $po->setDepSalaryNo('AMG'); //代理单位编码
        $po->setDepAcc('01090353700120101029904'); //北京创新诚仁科贸有限公司 AMG( 脱敏 )
        $po->setTranflag('1'); //0 代收1代付
        $po->setCurrencyType('01');
        $po->setBankBookSummary('11');
        $po->setPerAcc($perAcc);
        $po->setPerName($perName);
        $po->setCertType('1');
        $po->setCertNum($certNum);
        $po->setPayAmt($payAmt);
        $po->setUserRem($userRem);
        $po->setReqReserved1(' ');
        $po->setReqReserved2(' ');
        $po->setReqReserved3(' ');
        $result = BjRequest::singleTrade($po);
        return $result;
    }
}