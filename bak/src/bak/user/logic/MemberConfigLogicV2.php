<?php
/**
 * Created by PhpStorm.
 * User: rainbow
 * Time: 2016-12-26 16:34:03
 */

namespace app\src\user\logic;


use app\src\base\logic\BaseLogicV2;
use app\src\user\model\MemberConfig;

class MemberConfigLogicV2 extends BaseLogicV2
{
    /**
     * @return mixed
     */
    protected function _init(){
      $this->setModel(new MemberConfig());
    }

    /**
     * uid是否实名认证了
     */
    public function isReal($uid){
      $r = $this->getInfo(['uid'=>$uid,'identity_validate'=>1]);
      return $r ? true : false;
    }

    /**
     * 业务 - 设置或更改或重置支付密码
     * @Author
     * @DateTime 2016-12-26T16:46:49+0800
     * @param    apiReturn  $params [description]
     */
    public function setSecret(array $params){
      extract($params);
      $r = $this->getInfo(['uid'=>$uid]);
      if(!$r) return returnErr(Linvalid('uid'));
      if(!$this->isLegal($new)) return returnErr(Linvalid('new'));

      if($old){
        //更改支付密码
        if(!$this->isLegal($old)) return returnErr(Linvalid('old'));
        if($r['pay_secret'] != $this->createSecret($old)) return returnErr(Linvalid('old'));
      }else{
        //设置或重置支付密码
      }
      $this->save(['uid'=>$uid],['pay_secret'=>$this->createSecret($new)]);
      return returnSuc(L('success'));
    }

    /**
     * 业务 - 验证支付密码
     * @Author
     * @DateTime 2016-12-26T16:46:49+0800
     * @param    apiReturn  $params [description]
     */
    public function checkSecret(array $params){
      extract($params);
      $r = $this->getInfo(['uid'=>$uid]);
      if(!$r) return returnErr(Linvalid('uid'));
      if(!$r['pay_secret'])        return returnErr(L('please_set_secret'));
      if(!$this->isLegal($secret)) return returnErr(L('err_pay_secret'));
      if($r['pay_secret'] != $this->createSecret($secret)) return returnErr(L('err_pay_secret'));
      return returnSuc(L('success'));
    }

    public function createSecret($str=''){
      return md5($str);
    }

    public function isLegal($str=''){
      return preg_match("/^[0-9]{6}$/", $str);
    }
}