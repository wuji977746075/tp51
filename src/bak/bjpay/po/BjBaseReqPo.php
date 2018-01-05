<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-22
 * Time: 11:12
 */

namespace app\src\bjpay\po;


use app\src\bjpay\utils\SerialNoUtils;

class BjBaseReqPo
{
    private $opName;//交易名称
    private $serialNo;//交易序列号
    private $reqTime;//客户端请求时间

    private $reqParam;//交易请求参数实体

    public function __construct()
    {
        $this->reqParam = [];
        $this->serialNo = (SerialNoUtils::generate(0));
        $this->setReqTime(time());
    }

    /**
     * 添加请求参数
     * @param $key
     * @param $value
     */
    public function addParam($key,$value){
        if(empty($this->reqParam)){
            $this->reqParam = [];
        }

        $this->reqParam[$key] = $value;
    }

    /**
     * @return mixed
     */
    public function getOpName()
    {
        return $this->opName;
    }

    /**
     * @param mixed $opName
     */
    public function setOpName($opName)
    {
        $this->opName = $opName;
    }

    /**
     * @return mixed
     */
    public function getSerialNo()
    {
        return $this->serialNo;
    }

    /**
     * @param mixed $serialNo
     */
    public function setSerialNo($serialNo)
    {
        $this->serialNo = $serialNo;
    }

    /**
     * @return mixed
     */
    public function getReqTime()
    {
        return $this->reqTime;
    }

    /**
     * @param mixed $reqTime
     */
    public function setReqTime($reqTime)
    {
        $this->reqTime = $reqTime;
//        $this->reqTime = date('Ymd',$this->reqTime);
    }



    public function toXML(){

        $xml =  <<<XML
<?xml version="1.0" encoding="GB2312"?><BCCBEBankData><opReq><opName>$this->opName</opName><serialNo>$this->serialNo</serialNo><reqTime>$this->reqTime</reqTime><ReqParam></ReqParam></opReq></BCCBEBankData>
XML;
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        $domNodeList = $dom->getElementsByTagName("ReqParam");
        if(!empty($this->reqParam)){
            $reqParamDom = $domNodeList[0];
            foreach ($this->reqParam as $key=>$vo){
                $node = $dom->createElement($key,$vo);
                $reqParamDom->appendChild($node);
            }
        }

        return $dom->saveXML();
    }
}