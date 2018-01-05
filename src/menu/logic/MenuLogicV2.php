<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-15
 * Time: 10:40
 */

namespace app\src\menu\logic;


use app\src\base\logic\BaseLogicV2;
use app\src\menu\model\Menu;

class MenuLogicV2 extends BaseLogicV2
{

    /**
     * query 不分页
     * 查询显示状态下的菜单
     * @param $map
     * @param bool $order
     * @return array
     */
    public function queryShowingMenu($map,$order = false){
        return $this->queryNoPaging(array_merge($map,['hide'=>0]),$order);
    }

    public function _init()
    {
        $this->setModel(new Menu);
    }

}