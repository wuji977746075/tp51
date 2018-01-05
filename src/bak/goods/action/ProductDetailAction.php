<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-18
 * Time: 19:29
 */
namespace app\src\goods\action;

use app\src\base\action\BaseAction;
use app\src\base\helper\ValidateHelper;
use app\src\favorites\logic\FavoritesLogic;
use app\src\favorites\model\Favorites;
use app\src\goods\logic\ProductImageLogic;
use app\src\goods\logic\ProductLogic;
use app\src\goods\model\ProductImage;
use think\Db;
use think\Model;

/**
 * Class DetailAction
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\service\ProductDetail
 */
class ProductDetailAction extends BaseAction
{

    /**
     * 商品详情页
     * @author hebidu <email:346551990@qq.com>
     * @param $id  integer 商品id
     * @return array
     */
    public function detail($id){

        $logic  = new ProductLogic();
        $result = $logic->detail($id);

        if(ValidateHelper::legalArrayResult($result)){

            $product_info = $result['info'];
            $product_info['carousel_images'] = [];

            if(!empty($product_info['main_img'])){
                array_push($product_info['carousel_images'],$product_info['main_img']);
            }
            $productImageLogic  = new ProductImageLogic();

            $result = $productImageLogic->queryNoPaging(['pid'=>$id]);

            if(ValidateHelper::legalArrayResult($result)){
                foreach ($result['info'] as $item){
                    if($item instanceof Model){
                        $item = $item->toArray();
                    }
                    if($item['type'] == ProductImage::Carousel_Images){
                        array_push($product_info['carousel_images'],$item['img_id']);
                    }
                }
            }

            $product_info['is_fav'] = $this->isFavoriteProduct($id);

            $product_info = $this->filterUnused($product_info);

            return $this->success($product_info);
        }elseif($result['status'] && empty($result['info'])){
            return $this->error('该商品不存在');
        }

        return $this->error($result);
    }

    private function filterUnused($product){
        unset($product['price2']);
        unset($product['price3']);
        unset($product['cnt1']);
        unset($product['cnt2']);
        unset($product['cnt3']);
        unset($product['sku_id']);
        unset($product['sku_desc']);
        unset($product['ori_price']);
        unset($product['price']);
        unset($product['quantity']);
        unset($product['icon_url']);
        unset($product['create_time']);
        unset($product['pid']);
        unset($product['is_second']);
        unset($product['sku_product_code']);
        unset($product['buy_limit']);
        unset($product['min_buy_cnt']);
        unset($product['dt_origin_country']);
        return $product;
    }

    /**
     * 获取该商品是否已收藏
     * @param $id
     * @return int
     */
    private function isFavoriteProduct($id){

        $result = (new FavoritesLogic())->getInfo([
            'type'=>Favorites::FAV_TYPE_PRODUCT,
            'favorite_id'=>$id
        ]);

        if(ValidateHelper::legalArrayResult($result)){
            return 1;
        }else{
            return 0;
        }

    }

}