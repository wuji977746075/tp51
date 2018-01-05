<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-11
 * Time: 9:25
 */

namespace app\src\order\model;


use think\Model;

class OrdersExpress extends Model
{
    protected $autoWriteTimestamp = true;
    public function __construct($data=[])
    {
        parent::__construct($data);
    }

    public function getPoArray(){
        return [
            'expresscode'=>$this->getExpresscode(),
            'expressname'=>$this->getExpressname(),
            'expressno'=>$this->getExpressno(),
            'note'=>$this->getNote(),
            'order_code'=>$this->getOrderCode(),
            'uid'=>$this->getUid(),
            'create_time'=>time(),
            'update_time'=>time()
        ];
    }

    protected $expresscode;
    protected $expressname;
    protected $expressno;
    protected $note;
    protected $order_code;
    protected $uid;

    /**
     * @return mixed
     */
    public function getExpresscode()
    {
        if(is_null($this->expresscode)){
            $this->expresscode = $this->getData('expresscode');
        }
        return $this->expresscode;
    }

    /**
     * @param mixed $expresscode
     */
    public function setExpresscode($expresscode)
    {
        $this->expresscode = $expresscode;
    }

    /**
     * @return mixed
     */
    public function getExpressname()
    {
        if(is_null($this->expressname)){
            $this->expressname = $this->getData('expressname');
        }
        return $this->expressname;
    }

    /**
     * @param mixed $expressname
     */
    public function setExpressname($expressname)
    {
        $this->expressname = $expressname;
    }

    /**
     * @return mixed
     */
    public function getExpressno()
    {
        if(is_null($this->expressno)){
            $this->expressno = $this->getData('expressno');
        }
        return $this->expressno;
    }

    /**
     * @param mixed $expressno
     */
    public function setExpressno($expressno)
    {
        $this->expressno = $expressno;
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
    public function getOrderCode()
    {
        if(is_null($this->order_code)){
            $this->order_code = $this->getData('order_code');
        }
        return $this->order_code;
    }

    /**
     * @param mixed $order_code
     */
    public function setOrderCode($order_code)
    {
        $this->order_code = $order_code;
    }

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



}