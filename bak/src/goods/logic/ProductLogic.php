<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-20
 * Time: 9:28
 */

namespace app\src\goods\logic;


use app\src\base\enum\StatusEnum;
use app\src\base\helper\ExceptionHelper;
use app\src\base\logic\BaseLogic;
use app\src\category\logic\CategoryLogic;
use app\src\extend\Page;
use app\src\favorites\model\Favorites;
use app\src\goods\model\Product;
use app\src\goods\model\ProductAttr;
use app\src\goods\model\ProductImage;
use think\Db;
use think\Exception;
use think\exception\DbException;

class ProductLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new Product());
    }

    private function getQuery($map){

        $cate_id = isset($map['cate_id']) ? $map['cate_id']:"";
        $query = Db::table('itboye_product')->alias("p")
            ->field("cate.name as cate_name,cate.level as cate_level,cate.img_id as cate_img_id,pimg.img_id as img_id,p.status,p.lang,p.place_origin,p.uid,p.product_code,attr.expire_time,attr.consignment_time,attr.contact_name,attr.contact_way,attr.total_sales,attr.buy_limit,attr.view_cnt,p.dt_goods_unit,p.dt_origin_country,p.id,p.name,p.onshelf,p.store_id,p.weight,p.synopsis,p.secondary_headlines as secondary,p.template_id,p.loc_country,p.loc_province,p.loc_city,p.loc_address,p.cate_id,p.create_time as create_time,p.update_time as update_time")
            ->join(["itboye_product_attr"=>"attr"],"p.id = attr.pid ","LEFT")
            ->join(["itboye_category"=>"cate"],"cate.id = p.cate_id ","LEFT")
            ->join(["itboye_product_image"=>"pimg"],"p.id = pimg.pid  and pimg.type = 6015","LEFT")
            ->where("p.status",'1');//->where("pimg.type","6015");
        if(!empty($cate_id)){
            unset($map['cate_id']);
            $query->where($map)->where('cate_id|cate.parent|cate.root_id',$cate_id);
        }else{
            $query->where($map) ;
        }
        return $query;
    }

    public function manager($map = null, $page = ['curpage'=>1,'size'=>10], $order = false, $params = false) {

        try{

            $query = $this->getQuery($map);

            if(false !== $order) $query = $query->order($order);
            $start = max(intval($page['curpage'])-1,0) * intval($page['size']);
            $list = $query -> limit($start,$page['size']) -> select();

            $query = $this->getQuery($map);

            $count = $query -> count();

            // 查询满足要求的总记录数
            $Page = new Page($count, $page['size']);

            //分页跳转的时候保证查询条件
            if ($params !== false) {
                foreach ($params as $key => $val) {
                    $Page -> parameter[$key] = urlencode($val);
                }
            }

            // 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page -> show();
            $data = [];
            foreach ($list as $vo){
                if(method_exists($vo,"toArray")){
                    array_push($data,$vo->toArray());
                }else{
                    array_push($data,$vo);
                }
            }

            return $this -> apiReturnSuc(["show" => $show, "list" => $data]);
        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }

    public function shelfAll($onshelf,$ids){

        try{
            $entity = [];
            array('onshelf' => $onshelf);
            foreach($ids as $v) {
                array_push($entity,['id'=>$v,'onshelf'=>$onshelf,'update_time'=>time()]);
            }

            $this->getModel() -> saveAll($entity);

            return $this -> apiReturnSuc("上架成功");

        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }

    /**
     * 商品分组
     */
    public function mergeGroup($list){

        $pid_string = "";
        foreach ($list as $vo){
            $pid_string .= $vo['id'].',';
        }

        $logic = new ProductGroupLogic();
        $result = $logic->queryNoPaging(['p_id'=>['in',$pid_string]]);

        foreach ($list as &$vo){
            $vo['_group'] = [];
            foreach ($result['info'] as $item){
                if($vo['id'] == $item['p_id']){
                    array_push($vo['_group'],$item);
                }
            }
        }

        return $list;
    }

    public function mergePrice($list){
        $logic = new ProductSkuLogic();
        foreach ($list as &$vo){
            $result = $logic->queryNoPaging(['product_id'=>$vo['id']]);
            $vo['_min_price'] = 999999999999;
            $vo['_max_price'] = 0;
            if(!$result['status']) {
                $vo['_min_price'] = 999999999999;
                $vo['_max_price'] = 999999999999;
                continue;
            }
            foreach ($result['info'] as $sku_item){
                if($vo['_min_price'] > $sku_item['price']){
                    $vo['_min_price'] = $sku_item['price'];
                }
                if($vo['_max_price'] < $sku_item['price']){
                    $vo['_max_price'] = $sku_item['price'];
                }
            }
        }
        return $list;
    }

    public function mergeImages($list){
        $pid = "";
        foreach ($list as $vo){
            $pid .= $vo['id'].',';
        }

        $pid = rtrim($pid,',');

        $result =  $this->queryImages($pid,ProductImage::Main_Images);

        if($result['status']) {
            $tmp = [];
            foreach ($result['info'] as $item) {

                if (!isset($tmp[$item['pid']])) {
                    $tmp[$item['pid']] = [];
                }

                $tmp[$item['pid']] = $item['img_id'];
            }

            foreach ($list as &$item) {
                $pid = $item['id'];
                if(isset($tmp[$pid])){
                    $item['main_img'] = $tmp[$pid];
                }
            }
        }else{
            foreach ($list as &$item) {
                $item['main_img'] = '';
            }
        }

        return $list;
    }

    /**
     * 搜索商品的图片
     * @param $pid
     * @param $type
     * @return array
     */
    public function queryImages($pid,$type){

        try{
            $result = Db::table('itboye_product_image')->alias("img")
                ->field("img.pid,img.img_id")
                ->where('img.pid','in',$pid)
                ->where('img.type','in',$type)
                ->select();
            return $this->apiReturnSuc($result);
        }catch (DbException $ex){
            return $this->apiReturnErr($ex->getMessage());
        }

    }

    public function getBusinessStatusDesc($business_status){

        if(($business_status & Product::BUSINESS_STATUS_EXPIRED) == Product::BUSINESS_STATUS_EXPIRED){
            return lang('err_product_status_expired');
        }

        if(($business_status & Product::BUSINESS_STATUS_SHELF_OFF) == Product::BUSINESS_STATUS_SHELF_OFF){
            return lang('err_product_status_shelf_off');
        }

        if(($business_status & Product::BUSINESS_STATUS_DELETE) == Product::BUSINESS_STATUS_DELETE){
            return lang('err_product_status_delete');
        }

        if(($business_status & Product::BUSINESS_STATUS_OUTSOLD) == Product::BUSINESS_STATUS_OUTSOLD){
            return lang('err_product_status_outsold');
        }

        return "normal";
    }

    public function getBusinessStatus($product){
        $business_status = 0;
        //1. 是否下架测试
        if(isset($product['onshelf']) && $product['onshelf'] == Product::SHELF_OFF){

            $business_status |= Product::BUSINESS_STATUS_SHELF_OFF;

        }

        //2. 商品是否过期
        if(isset($product['expire_time']) && $product['expire_time']  > 0 && $product['expire_time'] < time()){

            $business_status |= Product::BUSINESS_STATUS_EXPIRED;

        }


        //3. 商品是否被删除
        if(isset($product['status']) && intval($product['status']) == -1){

            $business_status |= Product::BUSINESS_STATUS_DELETE;

        }

        return $business_status;

    }

    /**
     * 设置一个商品的业务数据
     * @author hebidu <email:346551990@qq.com>
     * @param $product
     */
    public function setBusinessStatus($product){

        $product['business_status'] = 0;


        //1. 是否下架测试
        if(isset($product['onshelf']) && $product['onshelf'] == Product::SHELF_OFF){

            $product['business_status'] |= Product::BUSINESS_STATUS_SHELF_OFF;

        }

        //2. 商品是否过期
        if(isset($product['expire_time']) && $product['expire_time']  > 0 && $product['expire_time'] < time()){

            $product['business_status'] |= Product::BUSINESS_STATUS_EXPIRED;

        }

        return $product;

    }


    /**
     * 根据商品id来查询
     * @param $pIds
     * @return array
     */
    public function queryWithIds($pIds){

        $query = Db::table('itboye_product')->alias("p");
        $result = $query->field("p.place_origin,p.uid,p.product_code ,pattr.expire_time,pattr.min_buy_cnt,pattr.has_sample,pattr.consignment_time,pattr.contact_name,pattr.contact_way,pattr.total_sales,pattr.buy_limit,pattr.view_cnt,p.dt_goods_unit,p.dt_origin_country,p.id,p.name,p.onshelf,p.store_id,p.weight,p.synopsis,p.secondary_headlines as secondary,p.template_id,p.loc_country,p.loc_province,p.loc_city,p.loc_address,p.cate_id,p.create_time as create_time,p.update_time as update_time")
            //->join("itboye_product_sku sku","sku.product_id = p.id","LEFT")
            ->join(["itboye_product_attr"=>"pattr"],"pattr.pid = p.id","LEFT")
            ->where("p.status",'1')
            ->where("p.id","in",$pIds)
            ->select();

        return $this->apiReturnSuc($result);
    }

    /**
     * 根据商品sku id字符串来查询商品信息
     * @param $skuIds
     * @return array
     */
    public function queryWithSkuIds($skuIds){
        $query = Db::table('itboye_product_sku')->alias("sku");
        $result = $query->field("img.img_id as main_img,p.status,p.lang,p.place_origin,p.uid,p.product_code,sku.cnt1,sku.cnt2,sku.cnt3,sku.price2,sku.price3 ,sku.id as sku_pkid,sku.sku_id,sku.sku_desc,sku.ori_price,sku.price,sku.quantity,sku.product_code as sku_product_code,sku.icon_url,pattr.expire_time,pattr.consignment_time,pattr.contact_name,pattr.contact_way,pattr.total_sales,pattr.buy_limit,pattr.view_cnt,p.dt_goods_unit,p.dt_origin_country,p.id,p.name,p.onshelf,p.store_id,p.weight,p.synopsis,p.secondary_headlines as secondary,p.template_id,p.loc_country,p.loc_province,p.loc_city,p.loc_address,p.cate_id,p.create_time as create_time,p.update_time as update_time")
            ->join(["itboye_product"=>"p"],"sku.product_id = p.id","LEFT")
            ->join(["itboye_product_attr"=>"pattr"],"pattr.pid = sku.product_id","LEFT")
            ->join(["itboye_product_image"=>"img"],"img.pid = p.id and img.type = 6015","LEFT")

            //            ->join(["itboye_freight_address"=>"fa"],"fa.template_id = p.template_id","LEFT")
//            ->join(["itboye_freight_template"=>"ft"],"ft.id = p.template_id","LEFT")
            ->where("p.status",'1')
            ->where("sku.id","in",$skuIds)
            ->select();
        return $this->apiReturnSuc($result);
    }

    private function getSearchQuery($filter,$order,$uid,$lang,$cate_id,$prop_id,$keyword){

        $query = Db::table('itboye_product')->alias("p")
            ->field("p.*, ifnull(f.id,0) as is_fav,sku.price as price")
            ->join(["itboye_product_prop"=>"prop"],"prop.pid = p.id","LEFT")
            ->join(["itboye_product_sku"=>"sku"],"sku.product_id = p.id","LEFT")
            ->join(["itboye_favorites"=>"f"],"f.favorite_id = p.id and f.uid = ".$uid .' and f.type = '.Favorites::FAV_TYPE_PRODUCT,"LEFT")
            ->where('p.onshelf',Product::SHELF_ON)
            ->where('p.status',1);
        //过滤价格
//        $query->where('p.lang',$lang);

        if(!empty($filter)){
            if(isset($filter['l_price']) && isset($filter['r_price'])){
                $l_price = $filter['l_price'];
                $r_price = $filter['r_price'];
                $query->where("price > :l_price and price < :r_price")->bind(['l_price'=>[$l_price,\PDO::PARAM_INT],'r_price'=>[$r_price,\PDO::PARAM_INT]]);
//                $query->where('price','>',$l_price)->where('price','<',$r_price);
            }
        }



        if(!empty($cate_id)){
            //寻找是否有子级类目，仅支持2级类目id,
            $result = (new CategoryLogic())->queryNoPaging(['parent'=>$cate_id]);
            if($result['status'] && is_array($result['info']) && count($result['info']) > 0){


                $result = (new CategoryLogic())->queryNoPaging(['parent'=>$cate_id]);

                if($result['status'] && is_array($result['info'])){
                    $cate_list = "";
                    foreach ($result['info'] as $vo){
                        $cate_list .= $vo['id'].',';
                    }
                    $query->where('p.cate_id','in',$cate_list);
                }
            }else{
                $query->where('p.cate_id',$cate_id);
            }
        }
        if(!empty($prop_id)){
            $query->where('prop.prop_id',$prop_id);
        }
        if(!empty($keyword)){
            $query->where(['p.name'=>['like','%'.$keyword.'%']]);
        }

        return $query;
    }

    private function getSearchQueryWithGroup($filter,$order,$uid,$lang,$cate_id,$prop_id,$group_id,$keyword){

        $query = Db::table('itboye_product')->alias("p")
            ->field("p.*, ifnull(f.id,0) as is_fav,sku.price as price")
            ->join(['itboye_product_group' => 'group'],'p.id = group.p_id','LEFT')
            ->join(["itboye_product_prop"=>"prop"],"prop.pid = p.id","LEFT")
            ->join(["itboye_product_sku"=>"sku"],"sku.product_id = p.id","LEFT")
            ->join(["itboye_favorites"=>"f"],"f.favorite_id = p.id and f.uid = ".$uid .' and f.type = '.Favorites::FAV_TYPE_PRODUCT,"LEFT")
            ->where('p.onshelf',Product::SHELF_ON)
            ->where('p.status',1);
        //过滤价格
//        $query->where('p.lang',$lang);

        if(!empty($filter)){
            if(isset($filter['l_price']) && isset($filter['r_price'])){
                $l_price = $filter['l_price'];
                $r_price = $filter['r_price'];
                $query->where("price > :l_price and price < :r_price")->bind(['l_price'=>[$l_price,\PDO::PARAM_INT],'r_price'=>[$r_price,\PDO::PARAM_INT]]);
//                $query->where('price','>',$l_price)->where('price','<',$r_price);
            }
        }



        if(!empty($cate_id)){
            //寻找是否有子级类目，仅支持2级类目id,
            $result = (new CategoryLogic())->queryNoPaging(['parent'=>$cate_id]);
            if($result['status'] && is_array($result['info']) && count($result['info']) > 0){


                $result = (new CategoryLogic())->queryNoPaging(['parent'=>$cate_id]);

                if($result['status'] && is_array($result['info'])){
                    $cate_list = "";
                    foreach ($result['info'] as $vo){
                        $cate_list .= $vo['id'].',';
                    }
                    $query->where('p.cate_id','in',$cate_list);
                }
            }else{
                $query->where('p.cate_id',$cate_id);
            }
        }
        if(!empty($prop_id)){
            $query->where('prop.prop_id',$prop_id);
        }
        if(!empty($keyword)){
            $query->where(['p.name'=>['like','%'.$keyword.'%']]);
        }

        return $query;
    }

    public function search($filter,$order,$uid,$lang,$cate_id,$prop_id,$keyword,$page=['page_index'=>1,'page_size'=>10]){

        if(empty($uid)){
            $uid = -1;
        }

        $query = $this->getSearchQuery($filter,$order,$uid,$lang,$cate_id,$prop_id,$keyword);
        //排序
        if(!empty($order)){
            switch ($order){
                case "pd"://价格从高到低
                    $query->order("price desc");
                    break;
                case "pa"://价格从低到高
                    $query->order("price asc");
                    break;
                case "d"://默认
                    $query->order("update_time desc");
                    break;
                default:
                    $query->order("update_time desc");
                    break;
            }
        }

        $start = max(intval($page['page_index'])-1,0) * intval($page['page_size']);

        $list = $query->limit($start,$page['page_size'])->group("p.id")->select();

        if (false === $list) {
            return $this -> apiReturnErr(lang('err_data_query'));
        }

        $query = $this->getSearchQuery($filter,$order,$uid,$lang,$cate_id,$prop_id,$keyword);

        $query = $query->field("count( DISTINCT  p.id) as tp_count");

        $result = $query->find();
        $count = 0;
        if(is_array($result) && isset($result['tp_count'])){
            $count = $result['tp_count'];
        }

        return $this -> apiReturnSuc(["count" => $count, "list" => $list]);
    }

    public function searchWithGroupNoPaging($filter,$order,$uid,$lang,$cate_id,$prop_id,$group_id,$keyword,$page=['page_index'=>1,'page_size'=>10]){

        if(empty($uid)){
            $uid = -1;
        }

        $query = $this->getSearchQueryWithGroup($filter,$order,$uid,$lang,$cate_id,$prop_id,$group_id,$keyword);
        //排序
        $query->order('group.display_order desc');

        $list = $query->group("p.id")->select();

        if (false === $list) {
            return $this -> apiReturnErr(lang('err_data_query'));
        }

        return $this -> apiReturnSuc($list);
    }

    /**
     * 商品详情数据
     * @param $id
     * @return array
     */
    public function detail($id){

        $result = Db::table('itboye_product')->alias("p")
            ->field("p.secondary_headlines,sku.price2,sku.price3,sku.cnt1,sku.cnt2,sku.cnt3,img.img_id as main_img,pattr.*,p.uid,p.place_origin,p.product_code ,datatree1.name as goods_unit_name,sku.id as sku_pkid,sku.sku_id,sku.sku_desc,sku.ori_price,sku.price,sku.quantity,sku.product_code as sku_product_code,sku.icon_url,p.dt_goods_unit,p.dt_origin_country,p.id,p.name,p.onshelf,p.store_id,p.weight,p.synopsis,p.secondary_headlines as secondary,p.template_id,p.loc_country,p.loc_province,p.loc_city,p.loc_address,p.cate_id,p.create_time as create_time,p.update_time as update_time")
            ->join(["itboye_product_attr"=>"pattr"],"pattr.pid = p.id","LEFT")
            ->join(["itboye_product_sku"=>"sku"],"sku.product_id = p.id","LEFT")
            ->join(["itboye_product_image"=>"img"],"img.pid = p.id and img.type = 6015","LEFT")
            ->join(["common_datatree"=>"datatree1"],"datatree1.parentid =  37 and datatree1.code = p.dt_goods_unit","LEFT")
            ->where("p.id",$id)
            ->select();

        if(is_array($result) && count($result) > 0) {

            $product = $result[0];

            foreach ($result as $p) {

                if (!isset($product['sku_list'])) {
                    $product['sku_list'] = [];
                }

                if (isset($p['sku_pkid'])) {
                    $sku_info = [
                        'sku_pkid' => $p['sku_pkid'],
                        'sku_id' => $p['sku_id'],
                        'sku_desc' => $p['sku_desc'],
                        'ori_price' => $p['ori_price'],
                        'price' => $p['price'],
                        'quantity' => $p['quantity'],
                        'product_code' => $p['sku_product_code'],
                        'icon_url' => $p['icon_url'],
                        'price3' => $p['price3'],
                        'price2' => $p['price2'],
                        'cnt1' => $p['cnt1'],
                        'cnt2' => $p['cnt2'],
                        'cnt3' => $p['cnt3']
                    ];

                    array_push($product['sku_list'], $sku_info);
                }

            }


            $result = $product;
            $result['properties'] = $this->getProperties($id);
            $result['sku_info'] = $this->getSkuTable($product['sku_list']);

            if(empty($result['properties'])){
                unset($result['properties']);
            }

            $result = $this->setBusinessStatus($result);


        }


        return $this->apiReturnSuc($result);
    }

    /**
     * 获取商品属性
     * @author hebidu <email:346551990@qq.com>
     */
    private function getProperties($pid){
        $logic = new ProductPropLogic();

        return  $logic->queryPropList($pid);
    }

    /**
     * 根据商品的规格获取规格信息
     * @param $sku_list
     * @return array
     * @internal param $cate_id
     */
    private function getSkuTable($sku_list){

        $sku_info = [];
        foreach ($sku_list as $vo){

            $sku_id_str = $vo['sku_id'];
            $sku_desc_str = $vo['sku_desc'];
            $sku_id_arr = explode(";",trim($sku_id_str,";"));
            $sku_desc_str = explode(";",trim($sku_desc_str,";"));
            for ($i = 0 ;$i<count($sku_id_arr);$i++){

                $sku_key = $sku_id_arr[$i];
                $sku_desc= $sku_desc_str[$i];
                if(strpos($sku_key,":") === false){
                    continue;
                }
                //k:v   k_name:v_name
                list($sku_id,$sku_value_id) =  explode(":",$sku_key);
                list($sku_name,$sku_value_name) =  explode(":",$sku_desc);
                //添加规格信息
                if(!array_key_exists($sku_id,$sku_info)){
                    $sku_info[$sku_id] = [
                        'sku_id'=>$sku_id,
                        'sku_name'=>$sku_name,
                        'value_list'=>[]
                    ];
                }

                //添加规格值信息
                $value_arr = &$sku_info[$sku_id]['value_list'];
                $exists = false;
                foreach ($value_arr as $item){
                    if($item['value_id'] == $sku_value_id){
                        $exists = true;
                        break;
                    }
                }
                if(!$exists){
                    array_push($value_arr,[
                        'value_id'=>$sku_value_id,
                        'value_name'=>$sku_value_name
                    ]);
                }
            }
        }



        return array_values($sku_info);
    }

    /**
     * 添加一个商品信息
     * @param Product $product
     * @param ProductAttr $attr
     * @param array $imgList
     * @param array $propArray
     * @return array
     */
    public function addToProduct(Product $product,ProductAttr $attr,array $imgList,array $propArray){
        Db::startTrans();
        try{
            $productAttrLogic = new ProductAttrLogic();
            $productImgLogic  = new ProductImageLogic();
            $propLogic  = new ProductPropLogic();
            //1. 插入商品主表
            $data = $product->getData();
            $data['status'] = StatusEnum::NORMAL;
            $data['create_time'] = time();
            $data['update_time'] = time();

            $result = $this->add($data);
            if(!$result['status']) return $this->apiReturnErr($result['info'],true);
            $pid = $result['info'];
            $attr->setAttr('pid',$pid);

            //2. 插入商品属性表
            $result = $productAttrLogic->add($attr->getData(),"pid");
            if(!$result['status']) return $this->apiReturnErr($result['info'],true);

            //3. 插入商品图片表
            if(count($imgList) > 0){
                foreach ($imgList as &$vo){
                    $vo['pid'] = $pid;
                }
                $result = $productImgLogic->addAll($imgList);
                if(!$result['status']) return $this->apiReturnErr($result['info'],true);
            }
            //5. 插入商品参数表
            if(count($propArray) > 0){
                foreach ($propArray as &$vo){
                    $vo['pid'] = $pid;
                }
                $result = $propLogic->addAll($propArray);
                if(!$result['status']) return $this->apiReturnErr($result['info'],true);
            }
            Db::commit();
            return $this->apiReturnSuc($pid);
        }catch (Exception $ex){
            return $this->apiReturnErr($ex,true);
        }
    }

    public function queryOnShelf($map = null,$page = array('curpage'=>0,'size'=>10),$order = false, $params = false, $fields = false){
        $query = $this ->getModel();

        $query = $query -> page($page['curpage'] . ',' . $page['size']);

        if($order !== false){
            $query = $query -> order($order);
        }
        if($fields !== false){
            $query = $query -> field($fields);
        }
        $map['status'] = 1;
        $map['onshelf'] = 1;
        $list = $query -> where($map) -> select();

        if ($list === false) {
            $error = $this ->getModel() -> getDbError();
            return $this -> apiReturnErr($error);
        }

        $count = $this -> getModel() -> where($map) -> count();

        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);

        //分页跳转的时候保证查询条件
        if ($params !== false) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        return $this -> apiReturnSuc(array("count" => $count,"show" => $show,"list" => $list));

    }

    public function queryNoPagingOnShelf($map = null,$order = false){
        $query = $this->getModel();

        $main_img_type = 6015;//主图

        $query = $query -> alias('p') -> join(['(select * from itboye_product_image where type='.$main_img_type.')' => 'image'] ,'p.id=image.pid','left');

        $map['status'] = 1;
        $map['onshelf'] = 1;

        $query = $query->where($map);

        if(!($order === false)){
            $query = $query -> order($order);
        }

        $result = $query -> select();

        if ($result === false) {
            $error = $this -> getModel() -> getDbError();
            return $this -> apiReturnErr($error);
        }

        return $this -> apiReturnSuc($result);
    }



}