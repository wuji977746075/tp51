<?php
/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/4/26
 * Time: 下午2:52
 */

namespace app\src\wallet\logic;


use app\src\base\logic\BaseLogic;
use app\src\wallet\model\VUserWalletInfo;

class VUserWalletInfoLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new VUserWalletInfo());
    }
}