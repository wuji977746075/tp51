<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-20
 * Time: 15:07
 */

namespace src\mall\shopcart;
use src\base\logic\BaseLogic;
use think\Db;
use think\exception\DbException;

class ShoppingCartLogic extends BaseLogic {

    /**
     * 查询所有购物车项
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @return array
     */
    public function queryAll($uid){
        try{
            $result = Db::table("itboye_shopping_cart")->alias("cart")
            ->field("sku.id as sku_pid,sku.price as sku_price,p.product_code,m.nickname as publisher_name ,p.place_origin,dt.name as unit_desc,attr.contact_name,attr.expire_time,sku.quantity,cart.*,p.onshelf,p.status as product_status")
            ->join(["itboye_product p",""],"p.id = cart.p_id","left")
            ->join(["itboye_store store",""],"store.id = p.store_id","left")
            ->join(["common_member m",""],"m.uid = store.uid","left")
            ->join(["itboye_product_sku sku",""],"p.id = sku.product_id and cart.psku_id = sku.id ","left")
            ->join(["itboye_product_attr attr",""],"p.id = attr.pid","left")
            ->join(["common_datatree dt",""],"p.dt_goods_unit = dt.code and dt.parentid = 37","left")
            ->where('cart.uid',$uid)
            ->order('create_time desc') //add by raibow 2017-04-14 09:34:57
            ->select();
            return $this->apiReturnSuc($result);
        }catch (DbException $ex){
            return $this->apiReturnErr($ex);
        }
    }

    /**
     * 查询单个或多个购物车项
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @return array
     */
    public function getInfoWithProduct($uid, $cart_ids = false){
        try{
            $query = Db::table("itboye_shopping_cart")->alias("cart")
                ->field("sku.price as sku_price,p.product_code,m.nickname as publisher_name ,p.place_origin,dt.name as unit_desc,attr.contact_name,attr.expire_time,sku.quantity,cart.*,p.onshelf,p.status as product_status")
                ->join(["itboye_product p",""],"p.id = cart.p_id","left")
                ->join(["itboye_store store",""],"store.id = p.store_id","left")
                ->join(["common_member m",""],"m.uid = store.uid","left")
                ->join(["itboye_product_sku sku",""],"p.id = sku.product_id and cart.psku_id = sku.id ","left")
                ->join(["itboye_product_attr attr",""],"p.id = attr.pid","left")
                ->join(["common_datatree dt",""],"p.dt_goods_unit = dt.code and dt.parentid = 37","left")
                ->where('cart.uid',$uid);
            if($cart_ids){
                $query = $query->where('cart.id','in',$cart_ids);
            }
            $result = $query->select();
            return $this->apiReturnSuc($result);
        }catch (DbException $ex){
            return $this->apiReturnErr($ex);
        }
    }

}