<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-19
 * Time: 10:05
 */

namespace app\src\goods\action;


use app\src\base\action\BaseAction;
use app\src\category\logic\CategoryPropvalueLogic;
use app\src\goods\logic\ProductLogic;
use app\src\goods\model\Product;
use app\src\goods\model\ProductAttr;
use app\src\goods\model\ProductImage;

class ProductAddAction extends BaseAction
{

    public function add($params){
        $this->params = $params;

        $productModel   = $this->getBaseInfo();
        $attrModel = $this->getAttributes();
        $imgModelList = $this->getImages();
        $propArray = $this->getProductProp();

        $logic = new ProductLogic();
        $result = $logic->addToProduct($productModel,$attrModel,$imgModelList,$propArray);

        return $result;
    }

    /*
     * 获取商品属性信息表
     */

    /**
     * 获取商品基本信息
     */
    private function getBaseInfo()
    {
        $product = new Product();
        $field = ["place_origin", "lang", "dt_goods_unit", "dt_origin_country", "synopsis", "weight", "detail", "store_id", "status", "onshelf", "update_time", "create_time", "cate_id", "loc_address", "loc_province", "loc_city", "loc_country", "template_id", "uid", "name", "product_code", "secondary_headlines"];
        foreach ($field as $vo) {
            $product->setAttr($vo, $this->_param($vo, ''));
        }
        return $product;
    }

    /**
     * 获取商品属性信息
     *
     * @return ProductAttr
     */
    private function getAttributes()
    {
        $attrModel = new ProductAttr();
        $field = ["favorite_cnt", "view_cnt", "contact_way", "contact_name", "consignment_time", "view_cnt", "has_receipt", "under_guaranty", "support_replace", "total_sales", "buy_limit"];

        foreach ($field as $vo) {
            $attrModel->setAttr($vo, $this->_param($vo, ''));
        }
        return $attrModel;
    }

    /**
     * 获取商品图片表
     * @return array
     */
    private function getImages()
    {
        //1. 主图
        $main_img = $this->_param('main_img', '');
        $img = explode(",", $this->_param('img', ''));

        //2. 商品轮播图
        $imgModelList = [];
        if (!empty($main_img)) {
            $imgModel = new ProductImage();
            $imgModel->setAttr('type', getDatatree('PRODUCT_MAIN_IMG'));
            $imgModel->setAttr('img_id', $main_img);

            array_push($imgModelList, $imgModel->getData());
        }

        foreach ($img as $vo) {
            if ($vo) {
                $imgModel = new ProductImage();
                $imgModel->setAttr('type', getDatatree('PRODUCT_SHOW_IMG'));
                $imgModel->setAttr('img_id', $vo);
                array_push($imgModelList, $imgModel->getData());
            }
        }

        return $imgModelList;
    }

    private function getProductProp(){
        $properties = $this->_param('properties','');
        if(empty($properties)) return [];
        $data = array();
        $error = false;
        $properties = explode(",",$properties);
        $propvalueLogic = new CategoryPropvalueLogic();
        foreach($properties as  $vo){
            $map = array(
                'id' => $vo,
            );
            $result = $propvalueLogic->getInfo($map);
            if($result['status']){
                $prop_id = $result['info']['prop_id'];
            }else{
                $error = $result['info'];
                break;
            }
            $entity = array(
                'prop_id' => $prop_id,
                'value_id' => $vo
            );
            array_push($data,$entity);
        }

        return $data;
    }

}

