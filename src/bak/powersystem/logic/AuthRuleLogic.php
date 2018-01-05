<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-02
 * Time: 16:04
 */

namespace app\src\powersystem\logic;


use app\src\base\logic\BaseLogic;

use app\src\powersystem\model\AuthRule;

class AuthRuleLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new AuthRule());
    }

    /**
     * 获取不重复module字段数据
     *
     */
    public function allModules(){
        $result = $this->getModel()->distinct(true)->field('module')->select();
        if($result === false){
            return $this->apiReturnErr($this->getModel()->getDbError());
        }else{
            return $this->apiReturnSuc($result);
        }
    }
}