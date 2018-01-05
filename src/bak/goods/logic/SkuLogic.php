<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-16
 * Time: 17:16
 */

namespace app\src\goods\logic;


use app\src\base\logic\BaseLogic;
use app\src\goods\model\Sku;

class SkuLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new Sku());
    }



    public function querySkuTable($cate_id){

        $result = $this->getModel()->where(array('cate_id'=>$cate_id))->select();

        $skuvalueApi = new SkuvalueLogic();
        $return = array();
        foreach($result as $sku){
            $one = array(
                'id'=>$sku['id'],
                'name'=>$sku['name'],
                'value_list'=>array()
            );
            $map = array('sku_id'=>$sku['id']);
            $skuvalue = $skuvalueApi->queryNoPaging($map);
            if($skuvalue['status']){
                $one['value_list'] = $skuvalue['info'];
            }else{
                return $this->apiReturnErr($skuvalue['info']);
            }
            array_push($return,$one);
        }

        return $this->apiReturnSuc($return);

    }

}