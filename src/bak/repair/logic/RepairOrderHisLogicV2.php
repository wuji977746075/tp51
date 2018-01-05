<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-21 11:05:53
 */

namespace app\src\repair\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\repair\model\RepairOrderHis;

class RepairOrderHisLogicV2 extends BaseLogicV2
{
    /**
     * @return mixed
     */
    protected function _init(){
        $this->setModel(new RepairOrderHis());
    }

}