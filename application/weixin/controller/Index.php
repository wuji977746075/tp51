<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 青 <99701759@qq.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// 微信用户表中的信息在$this->userinfo
// 用户newmember表的信息在$this->newmember中
// 用户角色表的信息在$this->role中
namespace app\weixin\controller;

use app\src\user\logic\UserMemberLogic;
use app\src\wallet\logic\WalletLogic;
use think\Controller;
use app\weixin\controller\Home;
use app\src\wxpay\logic\WxaccountLogic;
use app\src\product\logic\ProductLogic;
use app\src\user\logic\MemberLogic;
class Index extends Home{



    /*
     * 首页
     * */
    public function iindex(){
        return $this->fetch('shop/coding');
    }

    /*
     * 首页
     * */
    public function index(){

        $memberinfo=$this->memberinfo;
        $this->assign('user_Info',$memberinfo);
        $this->assignTitle($memberinfo['nickname'].'的店铺');
        $result=(new WxaccountLogic())->getInfo(['id'=>1]);
        $user_Info=session('user_Info');

        $role=$this->role;

        $map['onshelf']='1';
        $order='itboye_product.create_time desc';
        $result=(new ProductLogic())->plist($map,$role,$order);
        if(!$result['status']) $this->error('查询数据出错');
        $product=$result['info'];
        $goods=[];$meal=[];
        foreach($product as $k=>$v){
            if(in_array($v['type'],[1,2])){
                $goods[]=$v;
            }
            if(in_array($v['type'],[3,4,5])){
                $meal[]=$v;
            }
        }
        $this->assign('goods', $goods);
        $this->assign('meal', $meal);

        //底部
        $uid=$memberinfo['id'];
        $me=(new UserMemberLogic())->getInfo(['uid'=>$uid]);
        $my_upper_id=$me['info']['goods_uid'];
        if(!empty($my_upper_id)){
            $my_upper=(new UserMemberLogic())->getInfo(['uid'=>$my_upper_id]);
            if($my_upper['info']['type']!==1){
                $my_upper=(new MemberLogic())->getInfo(['uid'=>$uid]);
                $this->assign('Default','0');

            }else{
                $my_upper=(new MemberLogic())->getInfo(['uid'=>$uid]);
                $this->assign('Default','1');

            }
        }else{
            $my_upper=(new MemberLogic())->getInfo(['uid'=>$uid]);
            $this->assign('Default','1');

        }
        $my_info=(new MemberLogic())->getInfo(['uid'=>$uid]);
        if(empty($my_upper['info']['head'])){
            $my_upper['info']['head']=$my_info['info']['head'];
        }
        $this->assign('my_upper',$my_upper['info']);




        $this->assign('product', $product);

        return $this->fetch('index/index');
    }


    /*
     * 商品详情
     * */
    public function detail(){

        $id = I('id');
        $uid = UID;
        $result = apiCall(ProductApi::DETAIL,array($id));

        if($result['status']) {
            //传入定制类
            $is_private=0;

            if($result['info']['made_order']>0) $is_private=1;
            $this->assign('is_private',$is_private);

            //清除不用字段
            unset($result['info']['uid']);
            unset($result['info']['loc_country']);
            unset($result['info']['loc_province']);
            unset($result['info']['loc_city']);
            unset($result['info']['loc_address']);
            unset($result['info']['createtime']);
            unset($result['info']['updatetime']);
            unset($result['info']['onshelf']);
            unset($result['info']['status']);
            unset($result['info']['weight']);
            unset($result['info']['synopsis']);
            unset($result['info']['dt_origin_country']);
            unset($result['info']['_price']);
            unset($result['info']['dt_goods_unit']);
            unset($result['info']['pid']);
            $result['info']['detail'] = htmlspecialchars_decode($result['info']['detail']);
            $has_sku = apiCall(ProductSkuApi::HAS_SKU, array($id));

            if ($has_sku['status']) {
                //0为统一规格,1为多规格
                $has_sku = $has_sku['info'];
            } else {
                $this->ajaxReturn($has_sku['info']);
            }
            $result['info']['has_sku'] = $has_sku;

            if ($has_sku == 1) {
                //多规格传值
                //规格id
                $skuId = apiCall(ProductSkuApi::GET_SKU_ID, array($id, 'sku_id'));
                if ($skuId['status']) {
                    $result['info']['sku_id'] = json_decode($skuId['info'],true);
                } else {
                    $this->ajaxReturn($skuId['info']);
                }
                //规格名称
                $skuName = apiCall(ProductSkuApi::GET_SKU_NAME, array($id, 'sku_desc'));
                if ($skuName['status']) {
                    $result['info']['sku_name'] = json_decode($skuName['info'],true);

                } else {
                    $this->ajaxReturn($skuName['info']);
                }
                //规格详细信息
                $skulist = apiCall(ProductSkuApi::QUERY_NO_PAGING, array(array('product_id' => $id)));
                $min_price = 1e18;
                $max_price = 0.0;
                if ($skulist['status']) {

                    foreach ($skulist['info'] as $k=>$vo) {
                        $map['sku_id'] = $vo['id'];
                        $vo['real_price'] =ceil($vo['price']);
                        $vo['price'] =ceil($vo['price']);

                        /*
                         * 还需要一个会员等级进行限制操作
                         * */

                        $member_price = apiCall(ProductMemberPriceApi::GET_INFO,array($map));
                        if($member_price['status']){
                            $skulist['info'][$k]['member_group_id'] = $member_price['info']['price'];
                        }

                        if ($vo['price'] < $min_price) {
                            $min_price = $vo['price'];
                        }
                        if ($vo['price'] > $max_price) {
                            $max_price = $vo['price'];
                        }
                    }
                    $result['info']['sku_list'] = $skulist['info'];
                    $result['info']['min_price'] = $min_price;

                    $result['info']['max_price'] = $max_price;

                } else {
                    $this->ajaxReturn($skulist['info']);
                }

            } else {
                $skulist = apiCall(ProductSkuApi::GET_INFO, array(array('product_id' => $id)));

                //规格id
                $skuId = apiCall(ProductSkuApi::GET_SKU_ID, array($id, 'sku_id'));
//因为没有sku_id取不到，出现错误
//var_dump($skuId);

                if ($skuId['status']) {
                    $result['info']['sku_id'] = json_decode($skuId['info'],true);
                } else {
                    $this->ajaxReturn($skuId['info']);
                }
                if ($skulist['status']) {
                    unset($skulist['info']['sku_id']);
                    unset($skulist['info']['sku_desc']);
                    unset($skulist['info']['createtime']);
                    unset($skulist['info']['icon_url']);
                    $result['info']['sku_list'] = $skulist['info'];
                    $result['info']['sku_info'] = $skulist['info'];
                    $result['info']['min_ori_price'] = $skulist['info']['ori_price'];
                    $result['info']['min_price'] = $skulist['info']['price'];
                } else {
                    $this->ajaxReturn($skulist['info']);
                }
            }

            $group = apiCall(ProductGroupApi::GET_GROUP_BY_ID, array($id));
            if (!$group['status']) {
                $this->ajaxReturn($group['info']);
            }
            $result['info']['group'] = $group['info'];

            $result['info']['favorite'] = 0;
            if ($uid != 0) {
                $map = array(
                    'favorite_id' => $id,
                    'uid' => $uid,
                );
                $favorite = apiCall(FavoritesApi::COUNT, array($map));
                if ($favorite['status']) {
                    if ($favorite['info'] > 0) {
                        $result['info']['favorite'] = 1;
                    }
                } else {
                    $this->ajaxReturn($favorite['info']);
                }
            }

            if (is_null($result['info']['group'])) {
                unset($result['info']['group']);
            }

            //处理图片
            $result['info']['main_img'] = getImageUrl($result['info']['main_img']);
            if (is_array($result['info']['imgs'])) {
                foreach ($result['info']['imgs'] as $k => $v) {
                    $result['info']['imgs'][$k] = getImageUrl($v);
                }
            }
            if ($has_sku == 1 && is_array($result['info']['sku_list'])) {
                foreach ($result['info']['sku_list'] as $k => $v) {
                    $result['info']['sku_list'][$k]['icon_url'] = getImageUrl($v['icon_url']);
                }
            }

            $all_quantity = 0;
            //处理一下总库存的问题
            if($has_sku==1){
                foreach($result['info']['sku_list'] as $k => $v){
                    $all_quantity = $all_quantity+$v['quantity'];
                }
            }else{
                $all_quantity = $result['info']['sku_list']['quantity'];
            }
            $result['info']['all_quantity'] = $all_quantity;
            $this->assign($result['info']);


            //查询相应的用户的购买情况
            $buy=apiCall(OrdersApi::ORDER_BUY,array(['i.p_id'=>$id]));
            $this->assign('buy_info',$buy['info']);

            $this->display();
        }
    }

    public function ajaxReturn($data){
        //不做处理
    }


}