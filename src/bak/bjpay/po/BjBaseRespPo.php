<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-22
 * Time: 11:37
 */

namespace app\src\bjpay\po;


/**
 * <?xml version="1.0" encoding = "GB2312"?>
<BCCBEBankData>
<opRep>                                     <!--  交易响应数据 -->
<opName> AccountDetailQuery </opName>    <!-- 交易名称-->
<serialNo>1235454</serialNo>               <!-- 交易序列号 -->
<retCode>返回码</retCode>
<errMsg>错误描述</errMsg>
<opResult>                               <!--  交易结果  -->
……                                <!--  各个交易结果参数 -->
</opResult>
<opResultSet>                           <!-- 交易结果集 -->
<opResult>                          <!-- 交易结果1  -->
……
</opResult>
<opResult>                          <!-- 交易结果2  -->
……
</opResult>
</opResultSet>
</opRep>
</BCCBEBankData>
 * Class BjBaseRespPo
 * @package app\src\bjpay\po
 */
class BjBaseRespPo
{

    private $opName;//交易名称
    private $serialNo;//交易序列号
    private $retCode;//返回码
    private $errMsg;//错误描述
    private $opResult;

    public function hasResultKey($key){
        return isset($this->opResult[$key]);
    }

    /**
     *
     * @param $key
     * @return bool|int false的时候表示没有读取到信息
     */
    public function getParamResult($key){

        if($this->hasResultKey($key)){
            $value = $this->opResult[$key];
            //防止值存在false的情况下，出问题
            if($value === false) return 0;
            return $value;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getOpResult()
    {
        return $this->opResult;
    }

    /**
     * @param mixed $opResult
     */
    public function setOpResult($opResult)
    {
        $this->opResult = $opResult;
    }//返回数据



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
    public function getRetCode()
    {
        return $this->retCode;
    }

    /**
     * @param mixed $retCode
     */
    public function setRetCode($retCode)
    {
        $this->retCode = $retCode;
    }

    /**
     * @return mixed
     */
    public function getErrMsg()
    {
        return $this->errMsg;
    }

    /**
     * @param mixed $errMsg
     */
    public function setErrMsg($errMsg)
    {
        $this->errMsg = $errMsg;
    }



    public function loadXml($xml){
        try{

            $dom = new \DOMDocument();
            $dom->loadXML($xml);
            $domNodeList = $dom->getElementsByTagName('opName');
            if($domNodeList->length < 1) return ['code'=>-1,'msg'=>'opName节点缺失'];
            $opNameNode = $domNodeList[0];
            $this->setOpName($opNameNode->textContent);
            $domNodeList = $dom->getElementsByTagName('serialNo');
            if($domNodeList->length < 1) {
                //return ['code'=>-1,'msg'=>'serialNo 节点缺失'];
            }else{
                $serialNo = $domNodeList[0];
                $this->setSerialNo($serialNo->textContent);
            }
            $domNodeList = $dom->getElementsByTagName('retCode');
            if($domNodeList->length < 1) return ['code'=>-1,'msg'=>'retCode 节点缺失'];
            $retCode = $domNodeList[0];
            $this->setRetCode($retCode->textContent);
            $domNodeList = $dom->getElementsByTagName('errMsg');
            if($domNodeList->length < 1) return ['code'=>-1,'msg'=>'errMsg 节点缺失'];
            $errMsg = $domNodeList[0];
            $this->setErrMsg($errMsg->textContent);
            $this->opResult = [];
            $domNodeList = $dom->getElementsByTagName('opResult');
            if($domNodeList->length < 1) return ['code'=>-1,'msg'=>'opResult 节点缺失'];
            if($domNodeList[0]->hasChildNodes()){
                foreach ($domNodeList[0]->childNodes as $childNode)
                {
                    if ($childNode->nodeType != XML_TEXT_NODE){
                        $this->opResult[$childNode->nodeName] = $childNode->nodeValue;
                    }
                }
            }
            return ['code'=>0,'msg'=>'解析成功'];
        }catch (\Exception $e){
            return ['code'=>-2,'msg'=>$e->getMessage()];
        }
    }

    function __toString()
    {

        return 'opname='.$this->opName.';serialNo='.$this->serialNo.'retCode='.$this->retCode.';errMsg='.$this->errMsg;
    }

    /**
     * 交易返回是否成功
     * @return bool
     */
    public function isSuccess(){
        return $this->getRetCode() == "0000";
    }


}