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
  protected $data = [];
  /**
   * Getter for data
   * @return
   */
  public function getData() {
      return $this->data;
  }

  /**
   * Setter for data
   * @param data value to set
   * @return self
   */
  public function setData($data) {
      $this->data = $data;
      return $this;
  }
  /**
   * exception build
   * @param string $sMsg  [exception message]
   * @param int    $iCode [0:suc,1+:err]
   * @param mixed  $mData [exception detail/suc data]
   */
  public function __construct($sMsg,$iCode,$mData=[]) {
    parent::__construct($sMsg,$iCode);
    $this->data = $mData;
  }
  // code : int,错误码
  // data : arr,code=0时为正确数据,否则为错误堆栈(bebug)/[]
  // msg  : str,错误/正确信息
  public function __toString() {
    $iCode = $this->getCode();
    if($iCode){
      $aData = $this->getTrace();
    }else{
      $aData = $this->getData();
    }
    return json(['code'=>$iCode,'data'=>$aData,'msg'=>$this->getMessage()]);
  }
}