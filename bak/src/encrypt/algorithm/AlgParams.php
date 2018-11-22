<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-29
 * Time: 10:19
 */
namespace  app\src\encrypt\algorithm;

use app\src\encrypt\exception\CryptException;

class  AlgParams{

    private $client_secret;
    private $client_id;
    private $data;
    private $notify_id;
    private $time;
    private $type;
    private $sign;
    private $apiVer;

    /**
     * @return mixed
     */
    public function getApiVer()
    {
        return $this->apiVer;
    }

    /**
     * @param mixed $apiVer
     */
    public function setApiVer($apiVer)
    {
        $this->apiVer = $apiVer;
    }


    public function initFromArray($data){
        if(isset($data['type'])){
            $this->setType($data['type']);
        }
        if(isset($data['time'])) {
            $this->setTime($data['time']);
        }
        if(isset($data['notify_id'])) {
            $this->setNotifyId($data['notify_id']);
        }
        if(isset($data['client_id'])) {
            $this->setClientId($data['client_id']);
        }
        if(isset($data['client_secret'])) {
            $this->setClientSecret($data['client_secret']);
        }

        if(isset($data['data'])) {
            $this->setData($data['data']);
        }
        if(isset($data['api_ver'])) {
            $this->setApiVer($data['api_ver']);
        }
    }

    /**
     * 获取返回参数信息
     */
    public function getResponseParams(){
        return [
            'client_id'     =>  $this->getClientId(),
            'time'          =>  $this->getTime(),
            'data'          =>  $this->getData(),
            'notify_id'     =>  $this->getNotifyId(),
            'type'          =>  $this->getType(),
            'sign'          =>  $this->getSign(),
            'api_ver'       =>  $this->getApiVer()
        ];
    }

    public function isValid(){
        if(empty($this->getTime())){
            throw new CryptException("time invalid(s)");
        }

        if(empty($this->getType())){
            throw new CryptException("type invalid");
        }

        if(empty($this->getNotifyId())){
            throw new CryptException("notify_id invalid");
        }

        if(empty($this->getClientSecret())){
            throw new CryptException("client_secret invalid");
        }

        if(empty($this->getData())){
            throw new CryptException("data invalid");
        }
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    

    /**
     * @return mixed
     */
    public function getSign()
    {
        $this->sign = $this->getTime().$this->getType().$this->getData().$this->getClientSecret().$this->getNotifyId();
        $this->sign = md5($this->sign);
        return $this->sign;
    }


    /**
     * @return mixed
     */
    public function getClientSecret()
    {
        return $this->client_secret;
    }

    /**
     * @param mixed $client_secret
     */
    public function setClientSecret($client_secret)
    {
        $this->client_secret = $client_secret;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @param mixed $client_id
     */
    public function setClientId($client_id)
    {
        $this->client_id = $client_id;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getNotifyId()
    {
        return $this->notify_id;
    }

    /**
     * @param mixed $notify_id
     */
    public function setNotifyId($notify_id)
    { 
        $this->notify_id = empty($notify_id)? time() : $notify_id;
    }



    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }


}