<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2016-12-26 15:38:18
 * Description : [余额相关 及 余额支付]
 */

namespace app\domain;

use app\src\wallet\logic\WalletLogic;
use app\src\wallet\logic\ScoreHisLogicV2;
use app\src\wallet\logic\WithdrawAccountLogicV2;
use app\src\user\logic\MemberConfigLogicV2;
use app\src\system\logic\DatatreeLogicV2;
use app\src\wallet\logic\WalletHisLogicV2;
/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\domain
 * @example
 */
class WalletDomain extends BaseDomain{

  //余额 - 充值 : 返回支付信息
  public function order(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int,money|0|int','');

    $r = (new WalletLogic)->order($params);
    $this->exitWhenError($r,true);
  }
  /**
   * 业务 - 余额变动记录
   * @Author
   * @DateTime 2017-01-04T10:29:54+0800
   * @return   [type]                   [description]
   */
  public function his(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int','current_page|1|int,per_page|10|int');

    $r = (new WalletHisLogicV2) -> his($params);
    $this->exitWhenError(returnSuc($r),true);
  }
  //查询余额 - 无则添加记录
  //rsp : + score
  public function detail(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int','');

    $r = (new WalletLogic) -> getBalance($params['uid']);
    $this->exitWhenError($r);
    $ret = $r['info'];
    // 查询用户积分
    $r = (new ScoreHisLogicV2) -> getUserScore($params['uid']);
    $this->exitWhenError($r);
    $ret['score'] = $r['info'];
    // 查询积分抵扣比例
    $r = (new ScoreHisLogicV2) -> getOpScore(ScoreHisLogicV2::PAY_CUT);
    $this->exitWhenError($r);
    $ret['score_rate'] = $r['info'];
    $this->apiReturnSuc($ret);
  }
  //设置或重置或更改支付密码
  public function setSecret(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int,new','old');
    $r = (new MemberConfigLogicV2) -> setSecret($params);
    $this->exitWhenError($r,true);
  }
  //验证支付密码
  public function checkSecret(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int,secret','');
    $r = (new MemberConfigLogicV2) -> checkSecret($params);
    $this->exitWhenError($r,true);
  }

  //提现申请
  public function apply(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int,money|0|int,account_id|0|int','reason');
    $r = (new WalletLogic) -> apply($params);
    $this->exitWhenError($r,true);
  }
  //绑定提现账号
  public function bind(){
    $this->checkVersion("101");

    $params = $this->parsePost('uid|0|int,account_type|0|int,account,real_name','open_bank,id_number');
    $r = (new WithdrawAccountLogicV2) -> bind($params);
    $this->exitWhenError($r,true);
  }
  // 获取银行卡类型
  public function getBank(){
    $this->checkVersion("100");

    $params = $this->parsePost('bank_no','');
    $r = (new WithdrawAccountLogicV2) -> getBankInfo($params['bank_no']);
    $this->exitWhenError($r ? returnSuc($r) : returnErr(L('err_bank_type')),true);
  }
  // 提现账号列表
  public function bindList(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int','');
    $r = (new WithdrawAccountLogicV2) -> bindList($params['uid']);
    $this->exitWhenError(returnSuc($r),true);
  }
  // 删除提现账号
  public function bindDel(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int,id|0|int','');
    extract($params);
    $r = (new WithdrawAccountLogicV2) -> pretendDelete(['uid'=>$uid,'id'=>$id]);
    $this->exitWhenError(returnSuc($r),true);
  }
  // 支持的提现账号类型 - 其他需到后台[数据字典]加
  public function bindType(){
    $this->checkVersion("100");

    // $params = $this->parsePost('pay_code,uid|0|int','');
    $r = (new DatatreeLogicV2) -> getCacheList(['parentid'=>getDtreeId('account_type')]);
    // $account = [
    //       ["id"=>"6197",
    //           "name"=> "银行卡",
    //           "code"=>""]];
    $this->exitWhenError(returnSuc(array_values($r)),true);
  }
  // 余额支付
  public function pay(){
    $this->checkVersion("100");

    $params = $this->parsePost('pay_code,uid|0|int','');
    $r = (new WalletLogic) -> pay($params);
    $this->exitWhenError($r,true);
  }

  // 积分支付 - 废弃
  // public function scorePay(){
  //   $this->checkVersion("100");

  //   $params = $this->parsePost('pay_code,uid|0|int,score|0|int','');
  //   $r = (new WalletLogic) -> scorePay($params);
  //   $this->exitWhenError($r,true);
  // }

  //todo : 获取银行卡号银行
  //todo : 银行卡充值 - 暂不做
}