<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-20
 * Time: 9:26
 */

namespace src\goods\model;
use src\base\BaseModel as Model;

class ProductAttr extends Model {
    protected $hasReceipt;//是否有发票
    protected $underGuaranty;//是否售后
    protected $supportReplace;//是否支持退换货
    protected $totalSales;//总销量
    protected $buyLimit;//购买数量限制
    protected $viewCnt;//查看次数
    protected $pid;//商品id
    protected $isSecond; //是否二手物品
    protected $consignmentTime;//发货时间
    protected $contactName;//联系人姓名
    protected $contactWay;//联系方式
    protected $expireTime;//商品过期时间
    protected $favoriteCnt;//商品收藏数量

    /**
     * @return mixed
     */
    public function getHasReceipt()
    {
        return $this->hasReceipt;
    }

    /**
     * @param mixed $hasReceipt
     */
    public function setHasReceipt($hasReceipt)
    {
        $this->hasReceipt = $hasReceipt;
    }

    /**
     * @return mixed
     */
    public function getUnderGuaranty()
    {
        return $this->underGuaranty;
    }

    /**
     * @param mixed $underGuaranty
     */
    public function setUnderGuaranty($underGuaranty)
    {
        $this->underGuaranty = $underGuaranty;
    }

    /**
     * @return mixed
     */
    public function getSupportReplace()
    {
        return $this->supportReplace;
    }

    /**
     * @param mixed $supportReplace
     */
    public function setSupportReplace($supportReplace)
    {
        $this->supportReplace = $supportReplace;
    }

    /**
     * @return mixed
     */
    public function getTotalSales()
    {
        return $this->totalSales;
    }

    /**
     * @param mixed $totalSales
     */
    public function setTotalSales($totalSales)
    {
        $this->totalSales = $totalSales;
    }

    /**
     * @return mixed
     */
    public function getBuyLimit()
    {
        return $this->buyLimit;
    }

    /**
     * @param mixed $buyLimit
     */
    public function setBuyLimit($buyLimit)
    {
        $this->buyLimit = $buyLimit;
    }

    /**
     * @return mixed
     */
    public function getViewCnt()
    {
        return $this->viewCnt;
    }

    /**
     * @param mixed $viewCnt
     */
    public function setViewCnt($viewCnt)
    {
        $this->viewCnt = $viewCnt;
    }

    /**
     * @return mixed
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @param mixed $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * @return mixed
     */
    public function getIsSecond()
    {
        return $this->isSecond;
    }

    /**
     * @param mixed $isSecond
     */
    public function setIsSecond($isSecond)
    {
        $this->isSecond = $isSecond;
    }

    /**
     * @return mixed
     */
    public function getConsignmentTime()
    {
        return $this->consignmentTime;
    }

    /**
     * @param mixed $consignmentTime
     */
    public function setConsignmentTime($consignmentTime)
    {
        $this->consignmentTime = $consignmentTime;
    }

    /**
     * @return mixed
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * @param mixed $contactName
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;
    }

    /**
     * @return mixed
     */
    public function getContactWay()
    {
        return $this->contactWay;
    }

    /**
     * @param mixed $contactWay
     */
    public function setContactWay($contactWay)
    {
        $this->contactWay = $contactWay;
    }

    /**
     * @return mixed
     */
    public function getExpireTime()
    {
        return $this->expireTime;
    }

    /**
     * @param mixed $expireTime
     */
    public function setExpireTime($expireTime)
    {
        $this->expireTime = $expireTime;
    }

    /**
     * @return mixed
     */
    public function getFavoriteCnt()
    {
        return $this->favoriteCnt;
    }

    /**
     * @param mixed $favoriteCnt
     */
    public function setFavoriteCnt($favoriteCnt)
    {
        $this->favoriteCnt = $favoriteCnt;
    }


}