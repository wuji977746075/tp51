<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-19
 * Time: 13:45
 */

namespace app\src\goods\action;

use app\admin\controller\Admin;

class ProductGroupAddAction extends Admin
{

    public function addProduct($params){
        $product_group = $this->_param('product_group', '');
        $group_start_time = $this->_param('group_start_time', '');
        $group_start_time = strtotime($group_start_time);
        $group_end_time = $this->_param('group_end_time', '');
        $group_end_time = strtotime($group_end_time);
        $group = array(
            'product_group' => $product_group,
            'group_start_time' => $group_start_time,
            'group_end_time' => $group_end_time,
        );
        $groupModel = new ProductGroupLogic();
        if($group['product_group'] !== '' && $group['product_group'] !== 0){
            $map = array(
                'p_id' => $pid
            );
            $error = false;
            $result = $groupModel ->delete($map);

            if($result['status'] === false){
                $error = $result['info'];
            }

            if($error === false){
                $entity = array(
                    'start_time' => $group['group_start_time'],
                    'end_time' => $group['group_end_time'],
                    'price' => 0,
                    'p_id' => $pid,
                    'g_id' => $group['product_group'],
                );
                $result = $groupModel -> add($entity);
                if($result['status'] === false){
                    $error = $result['info'];
                }
            }
        }
    }
}