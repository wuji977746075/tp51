<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-06
 * Time: 10:07
 */

namespace app\src\wallet\model;


use think\Model;

class WalletHis extends Model
{
    const WALLET_HIS_ALIPAY = 6214;
    const WALLET_HIS_WEIXIN_MP = 6215;
    const WALLET_HIS_WEIXIN_DRIVER = 6216;
    const WALLET_HIS_WEIXIN_WORKER = 6217;
    const WALLET_HIS_BALANCE = 6218;
    const WALLET_HIS_ADMIN = 6219;
}