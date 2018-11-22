<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-08
 * Time: 19:51
 */

namespace app\src\order\action;


use app\src\address\logic\AddressLogic;
use app\src\base\action\BaseAction;
use app\src\base\exception\BusinessException;
use app\src\base\helper\ExceptionHelper;
use app\src\base\helper\LogHelper;
use app\src\base\utils\CodeGenerateUtils;
use app\src\freight\facade\FreightFacade;
use app\src\freight\model\FreightTemplate;
use app\src\goods\logic\ProductLogic;
use app\src\goods\model\Product;
use app\src\order\enum\PayCurrency;
use app\src\order\logic\OrdersLogic;
use app\src\order\logic\OrdersPaycodeLogic;
use app\src\order\model\Orders;
use app\src\order\model\OrdersContactinfo;
use app\src\order\model\OrdersItem;
use app\src\shoppingCart\logic\ShoppingCartLogic;

use think\Db;
use app\src\wallet\logic\ScoreHisLogicV2;
use app\src\wallet\logic\WalletLogic;

/**
 * 创建订单
 * Class OrderCreateAction
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\order\action
 */
class OrderCreateAction extends BaseAction
{

    private $address;//收货地址信息
    private $skuItem;//商品详细数据,方便查找，使用了商品规格id作为主键
    private $items;//商品详细数据，对应购物车
    private $processItem;//分类后的商品项

    private $freightArr;//运费

    /**
     * 立即购买
     * 可使用积分 2017-07-25 14:17:24
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @param $count_arr
     * @param $sku_ids
     * @param $address_id
     * @param $note
     * @param $score
     * @return array
     */
    public function createNow($uid,$count_arr,$sku_ids,$address_id,$note='',$score=0,$bill_info=[]){
        try{
            // 组合购买数 与 规格
            $buyItems = $this->getBuyItemFromPost($count_arr,$sku_ids);
            //0. 查询出商品数据 (this->items)、收货地址(this->address)
            $this->getData($uid,$buyItems,$address_id);

            //1. 检查商品的有效性
            $this->checkProduct();

            //2. 对商品进行分类（同店铺、同币种、同国家）
            $this->classifiedGoods();

            //3. 计算生成订单信息
            // 3.1 一个订单的运费
            // 3.2 一个订单的总价
            // 3.3 一个订单的优惠信息
            $this->calcOrderInfo();

            //4. 生成订单记录
            return $this->addOrder($uid,$note,$score,$bill_info);
        }catch (BusinessException $ex){
            return $this->error($ex);
        }
    }

    /**
     * 根据请求传输过来的数据构造购买项目
     * @param $count_arr
     * @param $sku_ids
     * @return array
     */
    private function getBuyItemFromPost($count_arr,$sku_ids){
        $ret = [];
        for ( $i = 0 ; $i < count($count_arr) ; $i++) {
            array_push($ret,[
                'count'=>$count_arr[$i],
                'psku_id'=>$sku_ids[$i]
            ]);
        }

        return $ret;
    }

    /**
     * 创建订单
     * 可使用积分 2017-07-25 14:17:04
     * @author hebidu <email:346551990@qq.com>
     * @param $uid integer 用户id
     * @param $cartIds string 购物车项目里的id
     * @param $address_id integer 收货地址id
     * @param $note
     * @param $score 使用的积分
     * @return array
     * @throws BusinessException
     * @internal param string $ids 商品的规格id
     */
    public function create($uid,$cartIds,$address_id,$note,$score=0,$bill_info=[]){

        try{
            $buyItems = $this->getBuyItemFromShoppingCart($uid,$cartIds);
            //0. 查询出商品数据、收货地址
            $this->getData($uid,$buyItems,$address_id);

            //1. 检查商品的有效性
            $this->checkProduct();

            //2. 对商品进行分类（同店铺、同币种、同国家）
            $this->classifiedGoods();

            //3. 计算生成订单信息
            // 3.1 一个订单的运费
            // 3.2 一个订单的总价
            // 3.3 一个订单的优惠信息
            $this->calcOrderInfo();

            //4. 生成订单记录
            return $this->addOrder($uid,$note,$score,$bill_info);
        }catch (BusinessException $ex){
            return $this->error($ex);
        }
    }

    /**
     * 添加订单时 分配积分:多店铺前面尽量使用完积分
     * @param  score int >=0
     * @return 积分抵扣的金额,分
     * @throws BusinessException
     */
    private function allotScore($uid,$score=0,$rollback=false){
        // check score
        $score = intval($score);
        if($score <0){
            $rollback && Db::rollback();
            throw  new BusinessException("allot:非法的参数:积分");
        }
        $score_pay = 0;
        if($score){

            $len  = count($this->freightArr);
            $last = $score;
            $r = (new ScoreHisLogicV2)->getOpScore(ScoreHisLogicV2::PAY_CUT);
            if(!$r['status']){
                $rollback && Db::rollback();
                throw  new BusinessException($r['info']);
            }
            $rate = floatval($r['info']);
            foreach ($this->freightArr as $key=>&$item){
                $price = intval($item['order_price']) + intval($item['order_freight']);
                //1:最多可使用积分
                $rand = min(ceil($price/(100*$rate)),$last);
                $last -= $rand;
                //2:随机积分
                // if($len>1){ // 多店铺非最后
                //     $rand  = rand(0,$last-1);
                //     $len  -= 1;
                //     $last -= $rand;
                // }else{ // 单店铺 或 多店铺最后
                //     $rand = $last;
                // }
                $item['score']  = $rand;
                $score_pay_item = floor($rand*$rate*100);
                if($score_pay_item>$price){
                    $rollback && Db::rollback();
                    throw  new BusinessException('allot:使用积分过多');
                }
                $score_pay += $score_pay_item;
                $item['score_pay'] = $score_pay_item; //分
            }
            if($last > 0){
                $rollback && Db::rollback();
                throw  new BusinessException('最多只能使用'.($score-$last).'积分');
            }elseif ($last <0) {
                $rollback && Db::rollback();
                throw  new BusinessException('订单积分分配异常');
            }
        }else{
            foreach ($this->freightArr as $key=>&$item){
                $item['score']     = 0;
                $item['score_pay'] = 0; //分
            }
        }
        return $score_pay;
    }
    /**
     * 插入订单记录 : 事务
     * TODO: 创建订单的时候 , 锁定库存 | 判断库存
     * TODO: 订单关闭的时候 ，解锁库存 | 退回库存
     * 可使用积分 2017-07-25 14:16:49
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @param $note
     * @param $score int 使用的积分
     * @param $bill_info arr[type,title,code] 发票信息
     * @return array
     * @throws BusinessException
     */
    private function addOrder($uid,$note,$score=0,$bill_info=[]){
        Db::startTrans();

        $codeUtils  = new CodeGenerateUtils();
        $orderLogic = new OrdersLogic();
        $now          = time();
        $orders_items = [];
        $orderCodes   = []; // 订单编号
        $total_price  = 0;  // 总金额

        $bill_type  = empty($bill_info['type']) ? 0:$bill_info['type'];
        $bill_title = empty($bill_info['title']) ? '':$bill_info['title'];
        $bill_code  = empty($bill_info['code']) ? '':$bill_info['code'];
        // 分配 各店铺 积分 : trans + throws
        $score_pay = $this->allotScore($uid,$score,true);
        // 准备订单信息
        foreach ($this->freightArr as $key=>$item){
            $order_code = $codeUtils->getOrderCode($uid);
            $orders = new Orders();
            $orders->setCsStatus(Orders::CS_DEFAULT);
            $orders->setDiscountMoney(0);
            $orders->setUid($uid);
            $orders->setPrice($item['order_price']);
            $orders->setPostPrice($item['order_freight']);
            $orders->setOrderCode($order_code);
            $orders->setFrom(Orders::COME_FROM_OTHER);
            $orders->setNote($note);
            $orders->setStatus(1);
            $orders->setPayStatus(Orders::ORDER_TOBE_PAID);
            $orders->setOrderStatus(Orders::ORDER_TOBE_CONFIRMED);
            $orders->setCreateTime($now);
            $orders->setUpdateTime($now);
            $orders->setCommentStatus(Orders::ORDER_TOBE_EVALUATE);
            $orders->setStoreid($item['store_id']);
            $orders->setGoodsAmount($item['order_price']);
            $orders->setPayType(0);
            $orders->setPayCode("");
            $orders->setPayBalance(0);
            $orders->setScorePay($item['score_pay']);
            $orders->setScore($item['score']);
            $orders->setBillType($bill_type);
            $orders->setBillTitle($bill_title);
            $orders->setBillCode($bill_code);
            if($item['order_price'] == 0){
                Db::rollback();
                throw  new BusinessException("非法的订单金额");
            }
            //计算 抵扣后支付额
            $total_price += $item['order_price']; // 订单金额(包括邮费)

            $items =  $this->processItem[$key];
            //订单项
            foreach($items as $vo){
                $cart_item = $vo['_cart_item'];
                $icon_url = isset($cart_item['icon_url'])?$cart_item['icon_url']:"";

                if(empty($icon_url) && isset($vo['main_img']) && !empty($vo['main_img'])){
                    $icon_url = $vo['main_img'];
                }

                $ordersItem = new OrdersItem();
                $ordersItem->setOrderCode($order_code);
                $ordersItem->setName($vo['name']);
                $ordersItem->setCreatetime($now);
                $ordersItem->setImg($icon_url);
                $ordersItem->setPrice($vo['_price']);
                $ordersItem->setOriPrice($vo['ori_price']);
                $ordersItem->setSkuId($vo['sku_id']);
                $ordersItem->setDtGoodsUnit($vo['dt_goods_unit']);
                $ordersItem->setDtOriginCountry($vo['dt_origin_country']);
                $ordersItem->setPId($vo['id']);
                $ordersItem->setSkuDesc($vo['sku_desc']);
                $ordersItem->setWeight($vo['weight']);
                $ordersItem->setPskuId($cart_item['psku_id']);
                $ordersItem->setCount($cart_item['count']);

                addLog("Order_create",$vo,"_price","",false,"");
                array_push($orders_items,$ordersItem);
            }

            //3. 设置订单收货人信息
            $ordersContactInfo = $this->getContactInfo();
            $ordersContactInfo->setOrderCode($order_code);
            $ordersContactInfo->setUid($uid);
            $ordersContactInfo->setNotes($note);

            //1. 添加订单
            $r = $orderLogic->addOrder($orders_items,$orders,$ordersContactInfo);
            if($r['status']){
                array_push($orderCodes,$order_code);
            }else{
                addLog('Order_create_add_order_error',json_encode($orders_items),json_encode($r['info']),"",false);
                return returnErr($r['info'],true);
            }
        }

        if($total_price <=0){
            return returnErr('订单金额不得为0',true);
        }
        addLog('Order_create_getPayInfo',json_encode($orderCodes),"","",false);
        $need_pay = $total_price - $score_pay;
        if($need_pay < 0) return returnErr('积分抵扣超额',true);
        // 生成 和 返回 支付信息 ,0也生成
        if($need_pay){ // 部分抵扣 + 其他支付方式
            $r  = (new OrdersPaycodeLogic)->getPayInfo($uid,$orderCodes,$need_pay,PayCurrency::RMB);
        }else{ // 全额抵扣 积分支付
            $r  = (new OrdersPaycodeLogic)->getPayInfo($uid,$orderCodes,$total_price,PayCurrency::RMB,$score_pay);
        }
        if(!$r['status']) return returnErr($r['info'],true);
        $info = $r['info'];
        $pay_code = $info['pay_code'];

        if($need_pay){ // 还需要其他支付
            if($score){ // 积分抵扣了部分
                // 减少用户积分 + 检查 (订单取消或超时 需返回给用户)
                $r = (new ScoreHisLogicV2)->cutScore($uid,$score,ScoreHisLogicV2::PAY_CUT,'抵扣商城订单:'.implode(';', $orderCodes));
                if(!$r['status']) return returnErr($r['info'],true);
            }else{
                // 第三方/自定义支付 - 不用额外操作
            }
        }else{ // 有全额抵扣/优惠 导致支付额为0
            if($score){
                // 全积分支付 + 积分处理
                $r = (new WalletLogic)->scorePay(['pay_code'=>$pay_code,'uid'=>$uid,'score'=>$score]);
                if(!$r['status']) return returnErr($r['info'],true);
            }else{ // 暂无其他全额 => 报错
                return returnErr('此订单实付不得为0',true);
            }
        }

        Db::commit();
        return returnSuc($info);
    }


    /**
     * 获取订单联系人信息
     * @author hebidu <email:346551990@qq.com>
     * @return OrdersContactinfo
     */
    private function getContactInfo(){
        $ordersContactInfo = new OrdersContactinfo();
        $ordersContactInfo->setContactName($this->address['contactname']);
        $ordersContactInfo->setCountry($this->address['country']);
        $ordersContactInfo->setProvince($this->address['province']);
        $ordersContactInfo->setCity($this->address['city']);
        $ordersContactInfo->setArea($this->address['area']);
        $ordersContactInfo->setDetailInfo($this->address['detailinfo']);
        $ordersContactInfo->setMobile($this->address['mobile']);
        $ordersContactInfo->setWxno($this->address['wxno']);
        $ordersContactInfo->setIdCard($this->address['id_card']);
        $ordersContactInfo->setPostalCode($this->address['postal_code']);

        return $ordersContactInfo;
    }

    /**
     * 计算订单的价格
     * @param $list
     * @return int
     * @throws BusinessException
     */
    private function calculatePriceOneOrder(&$list){

        if(count($list) <=  0) return 0;

        $order_price = 0;
        $cnt1   = -1;
        $cnt2   = -1;
        $cnt3   = -1;
        $price1 = -1;
        $price2 = -1;
        $price3 = -1;
        $total_buy_cnt = 0;
        $price  = 0;
        foreach ($list as $key=>&$item2){
            //赋值原价
            //商品信息
            $cart_item = $item2['_cart_item'];
            $buy_cnt =  $cart_item['count'];
//            if($cnt1 == -1){
//                $cnt1 = empty($item2['cnt1']) ? 0: $item2['cnt1'];
//                $cnt2 = empty($item2['cnt2']) ? 0: $item2['cnt2'];
//                $cnt3 =  empty($item2['cnt3']) ? 0: $item2['cnt3'];
//                $price1 = $item2['price'];
//                $price3 = $item2['price3'];
//                $price2 = $item2['price2'];
//            }

            $total_buy_cnt += $buy_cnt;

            if($total_buy_cnt < $cnt1){
            throw  new BusinessException(lang("err_cart_item_count_invalid",['name'=>'','min'=>$cnt1]));
        }

            //注意处理cnt3 等于0的情况，默认等于0为没有该价格，或无穷大
//            if($total_buy_cnt >= $cnt3 && $cnt3 > 0){
//                $price = $price3;
//            }elseif ($total_buy_cnt >= $cnt2 && $cnt2 > 0){
//                $price = $price2;
//            }elseif($total_buy_cnt >= $cnt1){
//                $price = $price1;
//            }

            //赋值商品此刻的价格
            $price = $item2['price'];
            //TODO: 商品参与活动价格
            $item2['_price'] = $price;
            $order_price += ($price * $buy_cnt);
        }

        return $order_price;
    }


    /**
     * 计算一个订单的运费
     * @param $store_id
     * @param $list
     * @param $receive_addr
     * @return int|mixed
     */
    private function calculateFreightOneOrder($store_id,$list,$receive_addr){

        $facade = new FreightFacade();
        $total = 0;
        foreach ($list as $key=>$item){
            $item['_freight'] = $facade->calFreightPriceForOneItem($item,$receive_addr);
            $total += $item['_freight'];
        }

        return $total;
    }

    /**
     * 计算生成订单的信息
     * @author hebidu <email:346551990@qq.com>
     */
    private function calcOrderInfo(){

        //收货地址id
        $receive_addr = $this->getReceiveAddressId();
        $this->freightArr = [];
        foreach ($this->processItem as $key=>&$item){

            $freight_price = $this->calculateFreightOneOrder($key,$item,$receive_addr);
            $price   = $this->calculatePriceOneOrder($item);

            if(!isset($this->freightArr[$key])){
                $this->freightArr[$key] = [
                    'store_id'=>$item[0]['store_id'],
                    'order_freight'=>0,
                    'order_price'=>0
                ];
            }

            $this->freightArr[$key]['order_freight'] = $freight_price;
            $this->freightArr[$key]['order_price'] = $price + $freight_price;

        }

    }

    /**
     * 获取收货地址id
     * @author hebidu <email:346551990@qq.com>
     */
    private function getReceiveAddressId(){
        $ret = [
            'country_id'=>'',
            'province_id'=>'',
            'city_id'=>''
        ];

        if(isset($this->address['cityid']) && !empty($this->address['cityid'])){
            $ret['city_id'] = $this->address['cityid'];
        }

        if(isset($this->address['provinceid']) && !empty($this->address['provinceid'])){
            $ret['city_id'] = $this->address['provinceid'];
        }

        if(isset($this->address['country_id']) && !empty($this->address['country_id'])){
            $ret['city_id'] = $this->address['country_id'];
        }

        return $ret;
    }

    /**
     * 对商品进行分类
     * @author hebidu <email:346551990@qq.com>
     */
    private function classifiedGoods(){
        $this->processItem = [];
        $this->skuItem = [];
        foreach ($this->items as $item){

            $store_id = $item['store_id'];
            $key = md5($store_id);

            $this->skuItem[$item['sku_pkid']] = $item;

            if(!isset($this->processItem[$key])) {
                $this->processItem[$key] = [];
            }

            array_push($this->processItem[$key],$item);
        }
    }


    /**
     * 检查商品数据的合法性
     * @author hebidu <email:346551990@qq.com>
     * @throws BusinessException
     */
    private function checkProduct(){

        $logic = new ProductLogic();
        foreach ($this->items as $vo){

            $status = $logic->getBusinessStatus($vo);

            //1. 判定商品业务状态是否有效
            if($status != Product::BUSINESS_STATUS_NORMAL){
                throw new BusinessException($logic->getBusinessStatusDesc($status));
            }

            //2. 判定购物车的购买数量 是否 小于商品的库存数量
            $item = $vo['_cart_item'];
            if(intval($vo['quantity']) < intval($item['count'])){
                throw  new BusinessException(lang("err_quantity_lack",['name'=>$vo['name']]));
            }

        }

    }

    /**
     * 从购物车表中获取购买的商品项
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @param $cartIds
     * @return
     * @throws BusinessException
     */
    private function getBuyItemFromShoppingCart($uid,$cartIds){
        $logic = new ShoppingCartLogic();

        $result = $logic->queryNoPaging(['uid'=>$uid,'id'=>['in',$cartIds]]);

        if(!$result['status'] || empty($result['info'])){
            throw  new BusinessException(lang('err_cart_id'));
        }

        return $result['info'];
    }

    /**
     * 获取相应的数据,并赋值
     * @param $uid
     * @param $buyItems
     * @param $address_id
     * @throws BusinessException
     * @internal param $cartIds
     */
    private function getData($uid,$buyItems,$address_id){

        //2. 拼接商品规格id
        $ids = "";
        foreach ($buyItems as $item){
            $ids .= $item['psku_id'].',';
        }
        $ids = rtrim($ids,",");

        //3. 获取收货地址信息
        $logic = new AddressLogic();
        $r = $logic->getInfo(['uid'=>$uid,'id'=>$address_id]);
        if(!$r['status'] || empty($r['info'])){
            throw  new BusinessException(lang('err_address_id'));
        }
        $this->address = $r['info'];

        // check skuid
        $logic = new ProductLogic();
        $r = $logic->queryWithSkuIds($ids);
        if(!$r['status'] || empty($r['info'])){
            throw  new BusinessException(lang('err_sku_ids'));
        }
        $this->items = $r['info'];
        // addLog("Order_create",json_encode($this->items),"","",false,"");
        foreach ($this->items as &$vo){
            foreach ($buyItems as $item) {
                if ($vo['sku_pkid'] == $item['psku_id']) {
                    $vo['_cart_item'] = $item;
                }
            }
        }
    }
}