<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2018-03-21 14:10:50
 * Description : [his 系统编码]
 */

namespace by\sdk\his;
// use

/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\
 * @example
 */
class HisCode {
  private $msg = [];
  private function _msg($code,$all=false){
    if($all) return $this->msg;
    return ((isset($msg[$code]) && $msg[$code]) ? $msg[$code] : $code);
  }

  // 错误信息
  // 0正确 其他错误
  public function getErr($code,$all=false){
    $this->msg = [0=>'正确',
    1=>'参数不正确',2=>'空或非标准的XML字串',3=>'接口用户没访问权限',4=>'接口用户不正确',5=>'传入非法功能号',
    6=>'密钥不匹配',7=>'签名不正确',8=>'签名类型不正确',9=>'加密不正确',10=>'重复提交',
    11=>'请求数据为空',12=>'返回数据为空',
    99=>'系统错误'];
    return $this->_msg($code,$all);
  }

  // 自定义错误信息
  // 0正确 其他错误
  public function getHisErr($code,$full=true){
    return ($full ? 'HIS_ERROR_'.$code.':' : '').$this->getErr($code);
  }

  //性别
  //0未知 1男 2女 9未说明
  public function getSex($code,$all=false){
    $this->msg = [0=>'未知',1=>'男',2=>'女',9=>'未说明'];
    return $this->_msg($code,$all);
  }

  //卡类型
  //1-9 99
  public function getCard($code,$all=false){
    $this->msg = [
    1=>'健康卡',2=>'市民卡',3=>'社保卡/医保卡',4=>'银行卡',5=>'公费医疗证',
    6=>'农合证',7=>'院内诊疗卡',8=>'就诊卡',9=>'系统内部号',
    99=>'其他卡'
    ];
    return $this->_msg($code,$all);
  }

  //病人在HIS系统记录状态
  // 0-3
  public function getUserStatus($code,$all=false){
    $this->msg = [0=>'正常',1=>'已挂失',2=>'已注销',3=>'可以注册'];
    return $this->_msg($code,$all);
  }

  //医院等级
  //0-13
  public function getHospLevel($code,$all=false){
    $this->msg = [0=>'其他',
    1=>'一级',2=>'二级',3=>'三级',4=>'特级',5=>'三甲',
    6=>'三乙',7=>'三丙',8=>'二甲',9=>'二乙',10=>'二丙',
    11=>'一甲',12=>'一乙',13=>'一丙'
    ];
    return $this->_msg($code,$all);
  }

  //科室等级
  //0-3
  public function getSectLevel($code,$all=false){
    $this->msg = [0=>'无',1=>'一级',2=>'二级',3=>'三级'];
    return $this->_msg($code,$all);
  }

  //状态
  //1-2
  public function getStatus($code,$all=false){
    $this->msg = [1=>'正常',2=>'注销'];
    return $this->_msg($code,$all);
  }

  //出诊状态
  //0-2
  public function getHDoctStatus($code,$all=false){
    $this->msg = [0=>'停诊',1=>'出诊',2=>'暂未开放'];
    return $this->_msg($code,$all);
  }

  //挂号类型
  //1-3
  public function getClinicFor($code,$all=false){
    $this->msg = [1=>'为本人挂号',2=>'为子女挂号',3=>'为他人挂号'];
    return $this->_msg($code,$all);
  }

  //时段
  //1-3
  public function getTimeDay($code,$all=false){
    $this->msg = [1=>'上午 (06:00-12:00)',2=>'下午 (12:00-18:00)',3=>'晚上(18:00-次日06:00)'];
    return $this->_msg($code,$all);
  }

  //证件类型
  //1-3 5-11 99
  public function getCertCard($code,$all=false){
    $this->msg = [
    1=>'身份证',2=>'港澳居民来往内地通行证',3=>'台湾居民来往大陆通行证',5=>'护照',
    6=>'军官证',7=>'出生证',8=>'驾驶证',9=>'残疾证',10=>'医保卡',
    11=>'市民卡',
    99=>'其他卡'
    ];
    return $this->_msg($code,$all);
  }

  //挂号渠道
  //1-11
  public function getClinicFrom($code,$all=false){
    $this->msg = [
    1=>'微信公众号',2=>'支付宝窗口',3=>'手机APP',4=>'网站',5=>'中国电信',
    6=>'中国移动',7=>'中国联通',8=>'自助终端',9=>'医院窗口',10=>'其他',
    11=>'系统',
    ];
    return $this->_msg($code,$all);
  }

  //支付渠道
  //1-6
  public function getPay($code,$all=false){
    $this->msg = [
    1=>'微信支付',2=>'支付宝支付',3=>'手机银联支付',4=>'互联网银联支付',5=>'终端银联支付',
    6=>'医院窗口支付'
    ];
    return $this->_msg($code,$all);
  }

  public function getPayType($channel,$throws=true){
    if($channel == 'alipay_app'){
      return 2;
    }elseif($channel == 'wechat_app'){
      return 1;
    }
    $throws && throws('未知支付方式');
    return 0;
  }
}