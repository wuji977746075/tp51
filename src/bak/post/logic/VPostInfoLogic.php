<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/3
 * Time: 15:44
 */

namespace app\src\post\logic;


use app\src\base\logic\BaseLogic;
use app\src\post\model\VPostInfo;

class VPostInfoLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new VPostInfo());
    }

}