<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-02
 * Time: 16:04
 */

namespace app\src\powersystem\logic;


use app\src\base\logic\BaseLogic;
use app\src\powersystem\model\AuthGroup;

class AuthGroupLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new AuthGroup());
    }
    /**
     * 写入用户组的规则
     */
    public function writeRules($groupid,$rules){
        if(empty($groupid)){
            return $this->apiReturnErr("用户组id错误");
        }
        if(!is_string($rules)){
            return $this->apiReturnErr("规则参数错误");
        }

        $result = $this->getModel()->save(array('rules' => $rules), array('id' => $groupid));

        return $this->apiReturnSuc($result);


    }
    /**
     * 写入用户组的菜单列表
     */
    public function writeMenuList($groupid,$menuList){
        if(empty($groupid)){
            return $this->apiReturnErr("用户组id错误");
        }
        if(!is_string($menuList)){
            return $this->apiReturnErr("规则参数错误");
        }
        $result = $this->getModel()->save(array('menulist' => $menuList), ['id' => $groupid]);

        return $this->apiReturnSuc($result);

    }
    
}