<?php
/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/2/14
 * Time: 上午10:43
 */

namespace app\src\bjpay\po;


class BjSinglePaymentTradeReqPo extends BjNormalReqPo
{

    public function __construct()
    {
        parent::__construct();
        $this->setOpName('CebankDSDFSingleOp');
    }

    private $DepSalaryNo;
    private $DepAcc;
    private $tranflag;
    private $currencyType;
    private $bankBookSummary;
    private $PerAcc;
    private $PerName;
    private $certType;
    private $certNum;
    private $payAmt;
    private $userRem;
    private $reqReserved1;
    private $reqReserved2;
    private $reqReserved3;

    /**
     * @param $DepSalaryNo //代理单位编码
     */
    public function setDepSalaryNo($DepSalaryNo)
    {
        $this->DepSalaryNo = $DepSalaryNo;
    }

    /**
     * @return mixed
     */
    public function getDepSalaryNo()
    {
        return $this->DepSalaryNo;
    }

    /**
     * @param $DepAcc //对公账号 23位北京银行企业帐号
     */
    public function setDepAcc($DepAcc)
    {
        $this->DepAcc = $DepAcc;
    }

    /**
     * @return mixed
     */
    public function getDepAcc()
    {
        return $this->DepAcc;
    }

    /**
     * @param $tranflag //代收代付标志 0 代收1代付
     */
    public function setTranflag($tranflag)
    {
        $this->tranflag = $tranflag;
    }

    /**
     * @return mixed
     */
    public function getTranflag()
    {
        return $this->tranflag;
    }

    /**
     * @param $currencyType //币种 01代表人民币，目前只支持人民币
     */
    public function setCurrencyType($currencyType)
    {
        $this->currencyType = $currencyType;
    }

    /**
     * @return mixed
     */
    public function getCurrencyType()
    {
        return $this->currencyType;
    }

    /**
     * @param $bankBookSummary //个人摘要 最大长度为2个汉字
     */
    public function setBankBookSummary($bankBookSummary)
    {
        $this->bankBookSummary = $bankBookSummary;
    }

    /**
     * @return mixed
     */
    public function getBankBookSummary()
    {
        return $this->bankBookSummary;
    }

    /**
     * @param $PerAcc //个人账号 13位或者16位北京银行个人帐号
     */
    public function setPerAcc($PerAcc)
    {
        $this->PerAcc = $PerAcc;
    }

    /**
     * @return mixed
     */
    public function getPerAcc()
    {
        return $this->PerAcc;
    }

    /**
     * @param $PerName //户名 最大长度为16位字符串（8个汉字）
     */
    public function setPerName($PerName)
    {
        $this->PerName = $PerName;
    }

    /**
     * @return mixed
     */
    public function getPerName()
    {
        return $this->PerName;
    }

    /**
     * @param $certType //证件类型 输入1至7之间的一个值
        代表的含义如下：
        1身份证
        2户口薄
        3护照
        4军人证
        5港澳台居民身份证
        6武警身份证
        7边民出入境通行证
     */
    public function setCertType($certType)
    {
        $this->certType = $certType;
    }

    /**
     * @return mixed
     */
    public function getCertType()
    {
        return $this->certType;
    }

    /**
     * @param $certNum //证件号码 最大长度为20位字符串
     */
    public function setCertNum($certNum)
    {
        $this->certNum = $certNum;
    }

    /**
     * @return mixed
     */
    public function getCertNum()
    {
        return $this->certNum;
    }

    /**
     * @param $certNum //发生额 最大长度为12位无符号数字串,后两位表示小数（如：1601表示16.01）
     */
    public function setPayAmt($payAmt)
    {
        $this->payAmt = $payAmt;
    }

    /**
     * @return mixed
     */
    public function getPayAmt()
    {
        return $this->payAmt;
    }

    /**
     * @param $userRem //对公摘要 最大长度为50位
     */
    public function setUserRem($userRem)
    {
        $this->userRem = $userRem;
    }

    /**
     * @return mixed
     */
    public function getUserRem()
    {
        return $this->userRem;
    }

    /**
     * @param $userRem //请求包备用字段1 此域值为空即可
     */
    public function setReqReserved1($reqReserved1)
    {
        $this->reqReserved1 = $reqReserved1;
    }

    /**
     * @return mixed
     */
    public function getReqReserved1()
    {
        return $this->reqReserved1;
    }

    /**
     * @param $userRem //请求包备用字段2 此域值为空即可
     */
    public function setReqReserved2($reqReserved2)
    {
        $this->reqReserved2 = $reqReserved2;
    }

    /**
     * @return mixed
     */
    public function getReqReserved2()
    {
        return $this->reqReserved2;
    }

    /**
     * @param $userRem //请求包备用字段3 此域值为空即可
     */
    public function setReqReserved3($reqReserved3)
    {
        $this->reqReserved3 = $reqReserved3;
    }

    /**
     * @return mixed
     */
    public function getReqReserved3()
    {
        return $this->reqReserved3;
    }

    public function toXML()
    {
        $this->addParam('DepSalaryNo',$this->DepSalaryNo);
        $this->addParam('DepAcc',$this->DepAcc);
        $this->addParam('tranflag',$this->tranflag);
        $this->addParam('currencyType',$this->currencyType);
        $this->addParam('BankbookSummary',$this->bankBookSummary);
        $this->addParam('PerAcc',$this->PerAcc);
        $this->addParam('PerName',$this->PerName);
        $this->addParam('certType',$this->certType  );
        $this->addParam('certNum',$this->certNum);
        $this->addParam('payAmt',$this->payAmt);
        $this->addParam('userRem',$this->userRem);
        $this->addParam('reqReserved1',$this->reqReserved1);
        $this->addParam('reqReserved2',$this->reqReserved2);
        $this->addParam('reqReserved3',$this->reqReserved3);

        return parent::toXML(); // TODO: Change the autogenerated stub
    }
}