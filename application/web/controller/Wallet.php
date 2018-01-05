<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/3
 * Time: 14:37
 */

namespace app\web\controller;

use app\src\wallet\logic\WalletHisLogicV2;

/**
 * Class Wallet
 * 钱包相关页面
 * @package app\web\controller
 */
class Wallet extends BaseWeb
{
    public function _initialize()
    {
        parent::_initialize();
        $this->assign('title', '交易记录');
    }

    public function history()
    {

        $uid = $this->_get('uid');
        $result = (new WalletHisLogicV2())->queryNoPaging(['uid'=>$uid]);
        $this->assign('data', $result);
        return $this->fetch();
    }
}
