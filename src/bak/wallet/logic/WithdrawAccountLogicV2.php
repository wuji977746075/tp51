<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2016-12-28 17:33:30
 * Description : [Description]
 */

namespace app\src\wallet\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\wallet\model\WithdrawAccount;
use app\src\system\logic\DatatreeLogicV2;
use app\src\tool\getBank;
// use think\Db;
// use think\Exception;

/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\
 * @example
 */
class WithdrawAccountLogicV2 extends BaseLogicV2 {
    //初始化
    protected function _init(){
        $this->setModel(new WithdrawAccount());
    }

    //根据银行卡号获取银行卡号类型信息
    public function getBankInfo($bank_no=''){
      return (new getBank())->check($bank_no);
    }
    /**
     * 业务 - 提现账号添加
     * @Author
     * @DateTime 2016-12-29T09:17:51+0800
     * @param    array                    $params [description]
     * @return   [apiReturn]                      [description]
     */
    public function bind(array $params){
      extract($params);
      //check account_type
      $r = (new DatatreeLogicV2())->isParent($account_type,getDtreeId('account_type'));
      if(!$r) return returnErr(Linvalid('account_type'));
      $ext = $r['name'];
      //银行卡类型
      if($account_type === 6197){
        $r = (new getBank())->check($account);
        if($r) $ext = implode('-', $r);
        else $ext = '';
      }else{
        $open_bank = '';
      }
      $ext = $ext ? $ext : '未知类型';
      //bind
      $map = [
      'uid'=>$uid,
      'account_type'  =>$account_type,
      'account'       =>$account,
      'status'        =>0,
      'open_bank'     =>$open_bank,
      'extra'         =>$ext,
      'real_name'     =>$real_name,
      ];
      $this->add($map);
      return returnSuc(L('success'));
    }

    //提现账户列表
    public function bindList($uid=0,$field='a.id,a.account,a.account_type,d.name,a.extra'){
      $model = $this->getModel();
      $r = $model->alias('a')->join(['common_datatree d',''],'d.id = a.account_type','left')->where(['uid'=>$uid,'status'=>0])->field($field)->select();
      foreach ($r as $v) {
        $info = explode('-', $v['extra']);
        $v['extra'] = isset($info[0]) ? $info[0] : '';
      }
      return $r;
    }
}