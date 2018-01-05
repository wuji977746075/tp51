<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-10
 * Time: 14:23
 */

namespace app\src\freight\facade;


use app\src\base\helper\LogHelper;
use app\src\base\helper\ValidateHelper;
use app\src\freight\logic\FreightAddressLogic;

class FreightFacade
{
    /**
     *
     * @param $receive_addr
     * @param $address_id
     * @return bool
     */
    private function isInAddress($receive_addr,$address_id){

        $country_id = $receive_addr['country_id'];
        $province_id = $receive_addr['province_id'];
        $city_id = $receive_addr['city_id'];

        if(strpos($address_id , $country_id.',') != false){
            return true;
        }

        if(strpos($address_id , $province_id.',') != false){
            return true;
        }

        if(strpos($address_id , $city_id.',') != false){
            return true;
        }

        return false;
    }

    /**
     *
     * @param $item
     * @param $receive_addr
     * @return array
     */
    public function calFreightPriceForOneItem($item,$receive_addr){

        $freight = $this->getFreightForOneItem($item,$receive_addr);
        //TODO: 支持重量、体积运费计算

        return $this->calculateFreightTypeCount($item,$freight);
    }



    /**
     * 获得一个商品的运费模板
     * @param $item
     * @param $receive_addr
     * @return bool
     */
    public function getFreightForOneItem($item,$receive_addr){
        //根据收货地址，商品信息，
        //1. 查询出商品的运费模板信息列表
        //2. 商品的购买数量
        $template_id = $item['template_id'];

        //3. 查询出符合条件的运费模板
        $map = [
            'template_id'=>$template_id
        ];

        $result = (new FreightAddressLogic())->queryNoPaging($map);

        $default_freight = false;
        $freight = false;

        if(ValidateHelper::legalArrayResult($result)){
            foreach ($result['info'] as $vo){
                if(empty($vo['addressids'])){
                    //默认的运费模板
                    $default_freight = $vo;
                }else{

                    //其它模板
                    if($this->isInAddress($receive_addr,$vo['addressids'])){
                        $freight = $vo;
                    }

                }
            }

            if($freight === false){
                $freight = $default_freight;
            }
        }

        return $freight;
    }

    /**
     * 按件数来计算运费
     * 运费模板id
     * @param $freight
     * @param $item
     * @return array
     */
    public function calculateFreightTypeCount($item,$freight){
        $freight_price = 0;

        $buy_count = $item['_cart_item']['count'];

        $replenishmoney = $freight['replenishmoney'];
        $replenishpiece = $freight['replenishpiece'];
        $firstmoney = $freight['firstmoney'];
        $firstpiece =$freight['firstpiece'];

        if($buy_count < $firstpiece){
            $freight_price += $firstmoney;
        }else{
            $buy_count =  $buy_count - $firstpiece;
            $replenish_cnt = $replenishpiece <= 0 ? 0 : ceil($buy_count / $replenishpiece);
            $freight_price += $firstmoney +  $replenish_cnt * $replenishmoney;
        }

        return $freight_price;
    }

}