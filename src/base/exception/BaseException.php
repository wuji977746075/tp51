<?php
namespace src\base\exception;
use \Exception;
/**
 * Class BaseException
 * 任何业务异常的基类
 * @author  rainbow <email:977746075@qq.com>
 * @package src\base\exception
 */
class BaseException extends Exception {

  /**
   * 系统异常后发送给客户端的HTTP Status
   * @var integer
   */
  private $data = [];
  /**
   * Getter for data
   * @return
   */
  function getData() {
      return $this->data;
  }

  /**
   * Setter for data
   * @param data value to set
   * @return self
   */
  function setData($data) {
      $this->data = $data;
      return $this;
  }
  /**
   * exception build
   * @param string $sMsg  [exception message]
   * @param int    $iCode [0:suc,1+:err]
   * @param mixed  $data  [exception detail/suc data]
   */
  function __construct($sMsg,$iCode,$data=[]) {
    $sMsg  = is_string($sMsg) ? $sMsg : json_encode($sMsg);
    $iCode = intval($iCode); // 一定要,不然exception
    parent::__construct($sMsg,$iCode);
    $data && $this->setData($data);
    $this->_init();
  }

  function _init(){ }
  // code : int,错误码
  // data : arr,code=0时为正确数据,否则为错误堆栈(bebug)/[]
  // msg  : str,错误/正确信息
  function __toString() {
    return ret(['code'=>$this->getCode(),'data'=>$this->getData(),'msg'=>$this->getMessage()],true);
    // return json(['code'=>$this->getCode(),'data'=>$this->getData(),'msg'=>$this->getMessage()]);
  }
}