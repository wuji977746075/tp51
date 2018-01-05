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
use app\src\base\helper\ParamsHelper;
use app\src\category\logic\CategoryPropvalueLogic;
use app\src\goods\logic\ProductAttrLogic;
use app\src\goods\logic\ProductImageLogic;
use app\src\goods\logic\ProductLogic;
use app\src\goods\logic\ProductPropLogic;
use app\src\goods\model\Product;
use app\src\goods\model\ProductAttr;
use app\src\goods\model\ProductImage;
use think\Model;
use think\Request;

class ProductEditAction extends BaseAction
{

    public function edit($params){
        if(!isset($params['id']) || intval($params['id']) <= 0){
            return $this->error('非法id');
        }
        $this->params = $params;
        $id = $params['id'];
        //1. 商品基本信息
        $productModel   = $this->getBaseInfo();
        $logic = new ProductLogic();
        $logic->saveByID($id,$productModel->getData());
        //2. 商品属性
        $attrLogic = new ProductAttrLogic();
        $attrModel = $this->getAttributes();

        $attrLogic->save(['pid'=>$id],$attrModel->getData());

        //3. 商品图片
        $imgModelList = $this->getImages();
        $productImgLogic = new ProductImageLogic();
        $productImgLogic->delete(['pid'=>$id]);
        foreach ($imgModelList as &$vo){
            $vo['pid'] = $id;
        }

        $productImgLogic->addAll($imgModelList);

        //4. 商品属性
        $propArray = $this->getProductProp();

        foreach ($propArray as &$vo){
            $vo['pid'] = $id;
        }
        $productPropLogic = new ProductPropLogic();

        $productPropLogic->delete(['pid'=>$id]);
        $productPropLogic->addAll($propArray);

        return $this->success('操作成功');
    }

    /*
     * 获取商品属性信息表
     */
    private function getProductProp(){
        $properties = $this->_param('prop',[]);

        if(empty($properties)) return [];
        $data = array();
        $error = false;
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

    /**
     * 获取商品图片表
     * @return array
     */
    private function getImages(){
        //1. 主图
        $main_img = $this->_param('main_img', '');
        $img = explode(",", $this->_param('img', ''));

        //2. 商品轮播图
        $imgModelList = [];
        if(!empty($main_img)){
            $imgModel = new ProductImage();
            $imgModel->setAttr('type',getDatatree('PRODUCT_MAIN_IMG'));
            $imgModel->setAttr('img_id',$main_img );

            array_push($imgModelList,$imgModel->getData());
        }

        foreach ($img as $vo) {
            if ($vo) {
                $imgModel = new ProductImage();
                $imgModel->setAttr('type',getDatatree('PRODUCT_SHOW_IMG'));
                $imgModel->setAttr('img_id',$vo );
                array_push($imgModelList,$imgModel->getData());
            }
        }

        return $imgModelList;
    }

    /**
     * 获取商品基本信息
     */
    private function getBaseInfo(){
        $fields = ["dt_goods_unit","synopsis","place_origin","product_code","secondary_headlines","store_id|int","id|int",["product_name"=>"name"]];
        return ParamsHelper::setModelAttr($fields,$this->params, new Product());
    }

    /**
     * 获取商品属性信息
     *
     * @return Model
     */
    private function getAttributes(){
        $fields = ["favorite_cnt","view_cnt","contact_way","contact_name","consignment_time","view_cnt","has_receipt","under_guaranty","support_replace","total_sales","buy_limit"];
        return ParamsHelper::setModelAttr($fields,$this->params, new ProductAttr());
    }

}

