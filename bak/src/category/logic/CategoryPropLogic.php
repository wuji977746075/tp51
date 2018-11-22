<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 15:54
 */

namespace app\src\category\logic;


use app\src\base\logic\BaseLogic;
use app\src\category\model\CategoryProp;

class CategoryPropLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new CategoryProp());
    }

    /**
     * 查询属性
     * @param $map
     * @return array
     */
    public function queryPropTable($map){

        $result = $this->getModel()-> where($map)->select();
        if($result === false){
            return $this->apiReturnErr($this->getModel()->getError());
        }

        $propvalueApi = new CategoryPropvalueLogic();
        $return = array();
        foreach($result as $prop){
            $one = array(
                'id'=>$prop['id'],
                'name'=>$prop['propname'],
                'property_value'=>array()
            );
            $map = array('prop_id'=>$prop['id']);
            $propvalue = $propvalueApi->queryNoPaging($map);
            if($propvalue['status']){
                $one['property_value'] = $propvalue['info'];
            }else{
                return $this->apiReturnErr($propvalue['info']);
            }
            array_push($return,$one);
        }
        return $this->apiReturnSuc($return);

    }

}