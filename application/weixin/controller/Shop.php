<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-29
 * Time: 11:44
 */

namespace app\weixin\controller;


use app\mobile\api\MobAddressApi;
use app\mobile\api\MobOrderApi;
use app\mobile\api\MobProductApi;
use app\src\user\logic\UserMemberLogic;
use app\src\user\logic\MemberLogic;
use app\mobile\api\MobShopApi;
use app\mobile\api\MobSpCartApi;
use app\pc\helper\PcFunctionHelper;
use app\src\product\logic\ProductimageLogic;
use app\src\shoppingCart\action\ShoppingCartQueryAction;
use app\src\product\logic\ProductLogic;
class Shop extends Home
{
    protected function _initialize()
    {
        parent::_initialize();
        $this->assignNav('商城');
    }

    public function index()
    {

        $this->redirect(url('shop/product_kind'));
        return $this->fetch();
    }
    protected function assignNav($nav = '首页'){
        $this->assign('nav', $nav);
    }
    //购物车
    public function cart()
    {

        $this->assignTitle('购物车');
        /*$result = MobSpCartApi::query(UID);

        if(!$result['status']) $this->error($result['info']);

        foreach ($result['info'] as &$val){
            $val['price'] = intval($val['price']) / 100;
            $val['ori_price'] = intval($val['ori_price']) / 100;
            $val['icon_url'] = PcFunctionHelper::getImgUrl($val['icon_url'], 120);
        }

        $this->assign('spcart', $result['info']);*/

        return $this->fetch();
    }
    
    //购物车删除
    public function cart_delete()
    {
        if(IS_POST){

            $id = $this->_param('id');
            $result = MobSpCartApi::delete(UID, $id);
            if($result['status']){
                $this->success('删除成功');
            }else{
                $this->error($result['info']);
            }
        }
    }
    
    //购物车修改数量
    public function cart_edit()
    {
        if(IS_POST){
            $id = $this->_param('id');
            $count = $this->_param('count');
            $result = MobSpCartApi::edit(UID, $id, $count);
            if($result['status']){
                $this->success('操作成功','');
            }else{
                $this->error($result['info']);
            }
        }
    }

    public function join_cart()
    {

        return $this->fetch();
    }

    /**
     * 添加到购物车
     */
    public function cart_add(){
        if(IS_POST){
            $pid = $this->_param('pid','','商品错误');
            $sku_pkid = $this->_param('sku_pkid','','规格错误');
            $count = intval($this->_param('count'));
            if($count <=0) $this->error('数量必须大于0');

            $result = MobSpCartApi::add(UID, $pid,  $sku_pkid, $count);

            if($result['status']){
                $this->success('添加成功');
            }else{
                $this->error($result['info'],'');
            }
        }
    }

    public function product_kind()
    {

        $this->assignTitle('商城');
        $result = MobShopApi::querySubCategory(1);
        if(!$result['status']){
            $this->error($result['info']);
        }

        //获取类目
        $cate = [];
        foreach ($result['info'] as $val){
            $cate[] = [
                'name' => $val['name'],
                'img'  => PcFunctionHelper::getImgUrl($val['img_id']),
                'id'   => $val['id']
            ];
        }

        $cate=[['id'=>1,'img'=>'','name'=>'name1'],['id'=>2,'img'=>'','name'=>'name2'],['id'=>3,'img'=>'','name'=>'name3']];

        $this->assign('cate', $cate);

        return $this->fetch();
    }

    public function product_list()
    {
        $role=$this->role;
        $this->assignTitle('商城');

        $product_type=input('product_type');
        if(!empty($product_type)){
            if($product_type==1) $map['type']=1;
            if($product_type==2) $map['type']=2;
            if($product_type==3) $map['type']=['in','3,4,5'];
        }



        $memberinfo=$this->memberinfo;
        $this->assign('user_Info',$memberinfo);
        $map['onshelf']='1';
        $order='itboye_product.create_time desc';
        $result=(new ProductLogic())->plist($map,$role,$order);
        if(!$result['status']) $this->error('查询数据出错');
        $list=$result['info'];

        $this->assign('list', $list);

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


        return $this->fetch();
    }



    public function product_stock()
    {
        $role=$this->role;
        $this->assignTitle('提货商城');
        $memberinfo=$this->memberinfo;
        $this->assign('user_Info',$memberinfo);
        $map['onshelf']='1';
        $map['type']='2';
        $order='itboye_product.create_time desc';
        $result=(new ProductLogic())->plist($map,$role,$order);
        if(!$result['status']) $this->error('查询数据出错');
        $list=$result['info'];
        $this->assign('list', $list);
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
        return $this->fetch();
    }


    public function product_detail()
    {
        $role=$this->role;

        $this->assignTitle('商品详情');
        $pid=$this->_param('pid');
        $product=(new ProductLogic())->detail(['id'=>$pid],$role);
        if(!$product['status']||empty($product['info'])) $this->error('查询出错');
        foreach($product['info'] as $val){
            $list=$val;
        }
        $this->assign('pid',$pid);
        $this->assign('info',$list);
        $this->assign('img',$list['img_id']);

        return $this->fetch();
    }

    public function submit_order()
    {

        $cart_ids = $this->_param('cart_ids');
        $this->assign('cart_ids', $cart_ids);
        $cart_ids = explode(',',$cart_ids);
        $cart_info = [];

        $result = (new ShoppingCartQueryAction)->getInfo(UID, $cart_ids);
        if($result['status']){
            $cart_info = $result['info'];
        }

        if(count($cart_info)==0){
            $this->redirect('shop/cart');
        }

        foreach($cart_info as &$val){
            $val['icon_url'] = PcFunctionHelper::getImgUrl($val['icon_url'], 80);
            $val['price'] = intval($val['price']) / 100;
            $val['ori_price'] = intval($val['ori_price']) / 100;
        }

        $this->assign('cart_info', $cart_info);

        //获取收货地址
        $result = MobAddressApi::query(UID);
        if(!$result['status']) $this->error($result['info']);
        $address = [];
        $address_default = [];
        $default = 0;
        foreach ($result['info'] as $add){
            if($add['is_default']){
                $address_default = $add;
                $default = $add['id'];
            }
            $address[] = [
                'id' => $add['id'],
                'is_default' => (bool)$add['is_default'],
                'name' => $add['contactname'],
                'phone' => $add['mobile'],
                'detail' => $add['province'].$add['city'].$add['area'].$add['detailinfo']
            ];
        }

        $this->assign('default', $default);
        $this->assign('address_default', $address_default);
        $this->assign('address', json_encode($address, JSON_UNESCAPED_UNICODE));

        return $this->fetch();
    }

    public function pay()
    {

        if(IS_POST){
            //生成订单
            $address_id = $this->_param('address_id','','未选择收货地址');
            $cart_ids = $this->_param('cart_ids','','没有选中商品');
            $note = '';

            $result = MobOrderApi::create(UID, $cart_ids, $address_id, true, $note);


            if(!$result['status']) $this->error($result['info']);
            $info = $result['info'];

            //跳转微信支付
            $this->redirect(config('site_url').'/wxpay/jump2pay?pay_code='.$info['pay_code']);

        }

    }

    public function replenish()
    {
        $this->assignTitle('补货');
        $memberinfo=$this->memberinfo;

        $role=$memberinfo['roles_info']['group_info']['group_id'];
        $product=(new ProductLogic())->detail(['type'=>2],'','','');
        foreach($product['info'] as $k=>$v){
            $tem[$k]['name']=$v['name'];
            if($role==1) $tem[$k]['price']=$v['classify']['angel_price'];
            if($role==2)$tem[$k]['price']=$v['classify']['elite_price'];
            if($role==3)$tem[$k]['price']=$v['classify']['leader_price'];
            if($role==4)$tem[$k]['price']='';
        }
        $this->assign('product',$tem);
        $this->assign('role',$role);
        $this->assign('user_Info',$memberinfo);

        return $this->fetch();
    }
    public function buy_success()
    {
        $this->assignTitle('购买成功');

        $order_code=input('order_code');
        $money=input('money');
        $time=time();
        $this->assign('order_code',$order_code);
        $this->assign('money',$money);
        $this->assign('time',$time);
        return $this->fetch();
    }
    public function stock_add()
    {
        $this->assignTitle('补货');
        return $this->fetch();
    }


    public function coding(){
        $this->assignTitle('正在开发中……');
        return $this->fetch();
    }


}