<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-21
 * Time: 11:14
 */

namespace app\src\message\facade;


class MessageEntity
{
    private $country;//国家
    private $mobile;//手机号
    private $code; //验证码
    private $content;//消息内容
    private $tplId;//消息模板id
    //消息模板值	变量名和变量值对，如：#code#=431515，整串值需要urlencode，比如正确结果为：%23code%23%3d431515。
    //如果你的变量名或者变量值中带有#&=中的任意一个特殊符号，请先分别进行utf-8 urlencode编码后再传递
    private $tplValue;
    private $scene;

    /**
     * @return mixed
     */
    public function getScene()
    {
        return $this->scene;
    }

    /**
     * @param mixed $scene
     */
    public function setScene($scene)
    {
        $this->scene = $scene;
    }//消息使用场景



    /**
     * @return mixed
     */
    public function getTplId()
    {
        return $this->tplId;
    }

    /**
     * @param mixed $tplId
     */
    public function setTplId($tplId)
    {
        $this->tplId = $tplId;
    }

    /**
     * @return mixed
     */
    public function getTplValue()
    {
        return $this->tplValue;
    }

    /**
     * @param mixed $tplValue
     */
    public function setTplValue($tplValue)
    {
        $this->tplValue = $tplValue;
    }



    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param mixed $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }



}