<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-09
 * Time: 20:53
 */

namespace app\src\order\model;


use think\Model;

class Orders extends Model
{

    protected $uid;
    protected $orderCode;
    protected $price;
    protected $postPrice;
    protected $note;
    protected $status;
    protected $payStatus;
    protected $orderStatus;
    protected $csStatus;
    protected $createTime;
    protected $updateTime;
    protected $statusNote;
    protected $commentStatus;
    protected $from;
    protected $discountMoney;
    protected $storeid;
    protected $goodsAmount;
    protected $payType;
    protected $payCode;
    protected $payBalance;
    protected $scorePay;
    protected $score;
    protected $billType;
    protected $billTitle;
    protected $billCode;


    /**
     * 订单退回
     */
    const ORDER_BACK = 12;
    /**
     * 待确认
     */
    const ORDER_TOBE_CONFIRMED = 2;
    /**
     * 待发货
     */
    const ORDER_TOBE_SHIPPED = 3;
    /**
     * 已发货
     */
    const ORDER_SHIPPED = 4;
    /**
     * 已收货
     */
    const ORDER_RECEIPT_OF_GOODS = 5;
    /**
     * 已退货
     */
    const ORDER_RETURNED = 6;
    /**
     * 已完成
     */
    const ORDER_COMPLETED = 7;
    /**
     * 取消或交易关闭
     */
    const ORDER_CANCEL = 8;
    /**
     * 正在退款
     */
    const ORDER_RESENDS = 9;

    //订单出库状态


    //订单支付状态
    /**
     * 待支付
     */
    const ORDER_TOBE_PAID = 0;
    /**
     * 支付中
     */
    const ORDER_PAY_ING = 3;
    /**
     * 货到付款
     */
    const ORDER_CASH_ON_DELIVERY = 5;
    /**
     * 已支付
     */
    const ORDER_PAID = 1;
    /**
     * 已退款
     */
    const ORDER_REFUND = 2;

    //订单评论状态


    /**
     * 待评论
     */
    const ORDER_TOBE_EVALUATE = 0;
    /**
     * 已评论
     */
    const ORDER_HUMAN_EVALUATED = 1;
    /**
     * 超时、系统自动评论
     */
    const ORDER_SYSTEM_EVALUATED = 2;

    //**************订单来源*****************
    /**
     * 来源PC网站
     */
    const COME_FROM_PC = 1;

    /**
     * 来源Android
     */
    const COME_FROM_ANDROID = 2;

    /**
     * 来源IOS
     */
    const COME_FROM_IOS = 3;

    /**
     * 其它
     */
    const COME_FROM_OTHER = 4;

    //**************订单支付类型*****************

    /**
     * 支付宝
     */
    const PAY_TYPE_ALIPAY = 1;

    //**************订单售后状态*****************

    /**
     * 初始状态
     */
    const CS_DEFAULT = 0;

    /**
     * 待处理
     */
    const CS_PENDING = 2;

    /**
     * 已处理
     */
    const CS_PROCESSED = 3;

    /**
     * @return mixed
     */
    public function getUid()
    {
        if(is_null($this->uid)){
            $this->uid = $this->getData('uid');
        }
        return $this->uid;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return mixed
     */
    public function getOrderCode()
    {
        if(is_null($this->orderCode)){
            $this->orderCode = $this->getData('order_code');
        }
        return $this->orderCode;
    }

    /**
     * @param mixed $orderCode
     */
    public function setOrderCode($orderCode)
    {
        $this->orderCode = $orderCode;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        if(is_null($this->price)){
            $this->price = $this->getData('price');
        }
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPostPrice()
    {
        if(is_null($this->postPrice)){
            $this->postPrice = $this->getData('post_price');
        }
        return $this->postPrice;
    }

    /**
     * @param mixed $postPrice
     */
    public function setPostPrice($postPrice)
    {
        $this->postPrice = $postPrice;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        if(is_null($this->note)){
            $this->note = $this->getData('note');
        }
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        if(is_null($this->status)){
            $this->status = $this->getData('status');
        }
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getPayStatus()
    {
        if(is_null($this->payStatus)){
            $this->payStatus = $this->getData('pay_status');
        }
        return $this->payStatus;
    }

    /**
     * @param mixed $payStatus
     */
    public function setPayStatus($payStatus)
    {
        $this->payStatus = $payStatus;
    }

    /**
     * @return mixed
     */
    public function getOrderStatus()
    {
        if(is_null($this->orderStatus)){
            $this->orderStatus = $this->getData('order_status');
        }
        return $this->orderStatus;
    }

    /**
     * @param mixed $orderStatus
     */
    public function setOrderStatus($orderStatus)
    {
        $this->orderStatus = $orderStatus;
    }

    /**
     * @return mixed
     */
    public function getCsStatus()
    {
        if(is_null($this->csStatus)){
            $this->csStatus = $this->getData('cs_status');
        }
        return $this->csStatus;
    }

    /**
     * @param mixed $csStatus
     */
    public function setCsStatus($csStatus)
    {
        $this->csStatus = $csStatus;
    }

    /**
     * @return mixed
     */
    public function getCreateTime()
    {
        if(is_null($this->createTime)){
            $this->createTime = $this->getData('create_time');
        }
        return $this->createTime;
    }

    /**
     * @param mixed $createTime
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
    }

    /**
     * @return mixed
     */
    public function getUpdateTime()
    {
        if(is_null($this->updateTime)){
            $this->updateTime = $this->getData('update_time');
        }
        return $this->updateTime;
    }

    /**
     * @param mixed $updateTime
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;
    }

    /**
     * @return mixed
     */
    public function getStatusNote()
    {
        if(is_null($this->statusNote)){
            $this->statusNote = $this->getData('status_note');
        }
        return $this->statusNote;
    }

    /**
     * @param mixed $statusNote
     */
    public function setStatusNote($statusNote)
    {
        $this->statusNote = $statusNote;
    }

    /**
     * @return mixed
     */
    public function getCommentStatus()
    {
        if(is_null($this->commentStatus)){
            $this->commentStatus = $this->getData('comment_status');
        }
        return $this->commentStatus;
    }

    /**
     * @param mixed $commentStatus
     */
    public function setCommentStatus($commentStatus)
    {
        $this->commentStatus = $commentStatus;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        if(is_null($this->from)){
            $this->from = $this->getData('from');
        }
        return $this->from;
    }

    /**
     * @param mixed $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return mixed
     */
    public function getDiscountMoney()
    {
        if(is_null($this->discountMoney)){
            $this->discountMoney = $this->getData('discount_money');
        }
        return $this->discountMoney;
    }

    /**
     * @param mixed $discountMoney
     */
    public function setDiscountMoney($discountMoney)
    {
        $this->discountMoney = $discountMoney;
    }

    /**
     * @return mixed
     */
    public function getStoreid()
    {
        if(is_null($this->storeid)){
            $this->storeid = $this->getData('storeid');
        }
        return $this->storeid;
    }

    /**
     * @param mixed $storeid
     */
    public function setStoreid($storeid)
    {
        $this->storeid = $storeid;
    }

    /**
     * @return mixed
     */
    public function getGoodsAmount()
    {
        if(is_null($this->goodsAmount)){
            $this->goodsAmount = $this->getData('goods_amount');
        }
        return $this->goodsAmount;
    }

    /**
     * @param mixed $goodsAmount
     */
    public function setGoodsAmount($goodsAmount)
    {
        $this->goodsAmount = $goodsAmount;
    }

    /**
     * @return mixed
     */
    public function getPayType()
    {
        if(is_null($this->payType)){
            $this->payType = $this->getData('pay_type');
        }
        return $this->payType;
    }

    /**
     * @param mixed $payType
     */
    public function setPayType($payType)
    {
        $this->payType = $payType;
    }

    /**
     * @return mixed
     */
    public function getPayCode()
    {
        if(is_null($this->payCode)){
            $this->payCode = $this->getData('pay_code');
        }
        return $this->payCode;
    }

    /**
     * @param mixed $payCode
     */
    public function setPayCode($payCode)
    {
        $this->payCode = $payCode;
    }

    /**
     * @return mixed
     */
    public function getPayBalance()
    {
        if(is_null($this->payBalance)){
            $this->payBalance = $this->getData('pay_balance');
        }
        return $this->payBalance;
    }

    /**
     * @param mixed $payBalance
     */
    public function setPayBalance($payBalance)
    {
        $this->payBalance = $payBalance;
    }

    /**
     * @return mixed
     */
    public function getScorePay()
    {
        if(is_null($this->scorePay)){
            $this->scorePay = $this->getData('score_pay');
        }
        return $this->scorePay;
    }
    /**
     * @param mixed $scorePay
     */
    public function setScorePay($scorePay)
    {
        $this->scorePay = $scorePay;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        if(is_null($this->score)){
            $this->score = $this->getData('score');
        }
        return $this->score;
    }
    /**
     * @param mixed $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * @return mixed
     */
    public function getBillType()
    {
        if(is_null($this->billType)){
            $this->billType = $this->getData('bill_type');
        }
        return $this->billType;
    }
    /**
     * @param mixed $billType
     */
    public function setBillType($billType)
    {
        $this->billType = $billType;
    }

    /**
     * @return mixed
     */
    public function getBillTitle()
    {
        if(is_null($this->billTitle)){
            $this->billTitle = $this->getData('bill_title');
        }
        return $this->billTitle;
    }
    /**
     * @param mixed $billTitle
     */
    public function setBillTitle($billTitle)
    {
        $this->billTitle = $billTitle;
    }

    /**
     * @return mixed
     */
    public function getBillCode()
    {
        if(is_null($this->billCode)){
            $this->billCode = $this->getData('bill_code');
        }
        return $this->billCode;
    }
    /**
     * @param mixed $billCode
     */
    public function setBillCode($billCode)
    {
        $this->billCode = $billCode;
    }


    /**
     * 获取模型数组
     * @author hebidu <email:346551990@qq.com>
     */
    public function getModelArray(){
        return [
            'uid'            =>$this->getUid(),
            'order_code'     =>$this->getOrderCode(),
            'price'          =>$this->getPrice(),
            'post_price'     =>$this->getPostPrice(),
            'note'           =>$this->getNote(),
            'status'         =>$this->getStatus(),
            'pay_status'     =>$this->getPayStatus(),
            'order_status'   =>$this->getOrderStatus(),
            'cs_status'      =>$this->getCsStatus(),
            'createtime'     =>$this->getCreateTime(),
            'updatetime'     =>$this->getUpdateTime(),
            'comment_status' =>$this->getCommentStatus(),
            'from'           =>$this->getFrom(),
            'discount_money' =>$this->getDiscountMoney(),
            'storeid'        =>$this->getStoreid(),
            'goods_amount'   =>$this->getGoodsAmount(),
            'pay_type'       =>$this->getPayType(),
            'pay_code'       =>$this->getPayCode(),
            'pay_balance'    =>$this->getPayBalance(),
            'score_pay'      =>$this->getScorePay(),
            'score'          =>$this->getScore(),
            'bill_type'      =>$this->getBillType(),
            'bill_title'     =>$this->getBillTitle(),
            'bill_code'      =>$this->getBillCode(),
        ];
    }
}