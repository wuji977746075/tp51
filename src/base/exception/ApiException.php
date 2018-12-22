<?php
namespace src\base\exception;
use CryptUtils;
/**
 * 接口异常抛出基类
 *  错误字符串/数组json字符串(client_id,itboye:业务加密串)
 * Class ApiException
 * @package src\base\exception
 */
class ApiException extends BaseException {

  /**
   * 系统异常后发送给客户端的HTTP Status
   * @var integer
   */
  protected $httpStatus = 200;
  //直接的错误字符串 or json字符串,数组结构如下:
  // code : int,错误码
  // data : arr,code=0时为正确数据,否则为错误堆栈(bebug)/[]
  // msg  : str,错误信息/''
  // notify_id : int,请求的标志
  // time : int,返回的时间
  public function __toString() {
    $iCode = $this->getCode();
    $aData = $this->getData();
    if($iCode && config('app_debug')){
      $aData = $this->getTrace();
    }
    try{
      if(defined('NOTIFY_ID') && defined('CLIENT_SECRET_REQ')){
        $aData = CryptUtils::encrypt($aData);
        // 外层并未包 client_id+itboye
        $a = [
          'code'=>$iCode,
          'data'=>$aData,
          'msg' =>$this->getMessage(),
          // 原请求id :app验证签名用
          'notify_id' => NOTIFY_ID,
          // 返回时间 :app验证签名用
          'time' =>''.time(),
          // app验证签名用
          'type' => 'T',
          // 签名用,下面unset
          'client_secret' => CLIENT_SECRET_REQ,
        ];
        //app验证签名用
        $a['sign'] = CryptUtils::sign($a);
      }else{
        $a = [
          'code'=>$iCode,
          'msg' =>$this->getMessage(),
          'data'=>$aData
        ];
      }
    }catch(\Exception $e){
      // 接口返回异常的终结 2/2 => code:msg:[trace]
      return ret([
        'code'=>$e->getCode(),
        'msg' =>$e->getMessage(),
        'data'=>$e->getTrace()
      ],true);
    }
    unset($a['client_secret']);
    return ret($a,true);
  }
}