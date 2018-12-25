<?php
namespace src\base\exception;
/**
 * 后台异常抛出基类
 * Class ApiException
 * @package src\base\exception
 */
class AdminException extends BaseException{
  function __toString() {
    try{
      $iCode = $this->getCode();
      $aData = $this->getData();
      if($iCode && config('app_debug')){
        $data = $this->getTrace();
      }else{
        $data = getArrVal($aData,'data',[]);
      }
      // 0:success,1+:error_code
      $r['code']  = $iCode;
      $r['msg']   = $this->getMessage();
      $r['url']   = getArrVal($aData,'url');   //跳转地址
      $r['delay'] = getArrVal($aData,'time',0);  //跳转延时
      $r['count'] = getArrVal($aData,'count',0); //layui 列表数据有效
      $r['data']  = $data;
      // json($r)->send();die(0);
      // echo json_encode($r);die(0);
      return ret($r,true);
    }catch(\Exception $e){
      // 接口返回异常的终结 2/2 => code:msg:[trace]
      return ret([
        'code'=>$e->getCode(),
        'msg' =>$e->getMessage(),
        'data'=>$e->getTrace(),
        'url' =>'',
        'delay'=>0,
        'count'=>0
      ],true);
    }
  }
}