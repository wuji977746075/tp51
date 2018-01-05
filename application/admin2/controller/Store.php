<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 16:22
 */

namespace app\admin\controller;


use app\src\category\logic\CategoryLogic;
use app\src\goods\logic\ProductLogic;
use app\src\store\logic\StoreLogic;
use app\src\store\logic\StorePromotionLogic;
use app\src\system\logic\ConfigLogic;
use think\Log;

class Store extends Admin
{


    /**
     * 商城配置
     */
    public function config(){
        if(IS_GET){
            $map = array('name'=>"WXPAY_OPENID");
            $result = (new ConfigLogic())->getInfo($map);
            if($result['status']){
                $this->assign("wxpayopenid",	$result['info']['value']);
                return $this->boye_display();
            }
        }elseif(IS_POST){

            $openids = $this->_param('openids','');

            $config = array("WXPAY_OPENID"=>$openids);
            $result = (new ConfigLogic())->set($config);
            if($result['status']){
                config('WXPAY_OPENID',$openids);
                $this->success(L('RESULT_SUCCESS'),url('Shop/config'));
            }else{
                if(is_null($result['info'])){
                    $this->error("无更新！");
                }else{
                    $this->error($result['info']);
                }
            }

        }
    }

    /**
     * 店铺管理
     */
    public function index(){

        //分页时带参数get参数
        $name = $this->_param('name','');

        $map = array();
        if(!empty($name)){
            $map['name'] = array('like',"%".$name."%");
        }

        $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
        $order = " create_time desc ";
        $map['uid'] = UID;
        $result = (new StoreLogic())->queryWithPagingHtml($map, $page, $order);

        if ($result['status']) {
            $this -> assign('name', $name);
            $this -> assign('show', $result['info']['show']);
            $this -> assign('list', $result['info']['list']);
            return $this->boye_display();
        } else {
            Log::record('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
            $this -> error(L('UNKNOWN_ERR'));
        }
    }

    public function add(){
        $map['uid'] = UID;
        $result = (new StoreLogic())->count($map);
        if($result['status'] && intval($result['info']) == 1){
            $this->error('店铺暂时只能创建一个');
        }

        if(IS_GET){
            return $this->boye_display();
        }elseif(IS_POST){
            $name = $this->_param('name','店铺名称');//
            $logo = $this->_param('logo','');
            $banner = $this->_param('banner','');
            $wxno = $this->_param('wxno','');
            $lat = $this->_param('lat',30.314933);
            $lng = $this->_param('lng',120.337985);
            $service_phone = $this->_param('service_phone','');

//            for($i=0;$i<count($wxnum);$i++){
//                if(!empty($weixin_name[$i])){
//                    array_push($weixin,array('openid'=>$wxnum[$i],'name'=>$weixin_name[$i]));
//                }
//            }

            $entity = array(
                'lat'=>$lat,
                'lng'=>$lng,
                'wxno'=>$wxno,
                'uid'=>UID,
                'name'=>$name,
                'logo'=>$logo,
                'notes'=>$this->_param('notes',''),
                'service_phone'=>$service_phone,
            );

            $result = (new StoreLogic())->add($entity);
            if($result['status']){
                $this->savePromotion($result['info']);
                $this->success("操作成功！",url('Admin/Store/index'));
            }else{
                $this->error($result['info']);
            }
        }
    }

    private function savePromotion($id){
        //保存店铺优惠
        $discount_money = $this->_param('discount_money',0);
        if($discount_money<0)$discount_money = 0;
        $result = (new StorePromotionLogic())->getInfo(array('store_id'=>$id));
        if($result['status']){
            if(!is_null($result['info'])){
                //保存
                $entity = array(
                    'condition'=>$this->_param('condition',0),
                    'discount_money'=>$discount_money,
                    'free_shipping'=>$this->_param('free_shipping',0,'int'),
                    'start_time'=>strtotime($this->_param('start_time',0)),
                    'end_time'=>strtotime($this->_param('end_time',0))
                );
                $result =(new StorePromotionLogic())->save(array('store_id'=>$id),$entity);
                if(!$result['status']){
                    $this->error($result['info']);
                }
            }else{

                //添加
                $entity = array(
                    'store_id'=>$id,
                    'condition'=>$this->_param('condition',0),
                    'discount_money'=>$discount_money,
                    'free_shipping'=>$this->_param('free_shipping',0),
                    'start_time'=>strtotime($this->_param('start_time',0)),
                    'end_time'=>strtotime($this->_param('end_time',0))
                );
                $result = (new StorePromotionLogic())->add($entity);
                if(!$result['status']){
                    $this->error($result['info']);
                }
            }
        }else{
            $this->error($result['info']);
        }
    }

    public function edit()
    {
        if (IS_GET) {
            $id = $this->_param('id', 0);
            $map = array('id' => $id);
            $result = (new StoreLogic())->getInfo($map);
            if ($result['status']) {
                $this->assign("store", $result['info']);
                return $this->boye_display();
            } else {
                $this->error($result['info']);
            }
        } elseif (IS_POST) {
            $id = $this->_param('id', 0);
            $lat = $this->_param('lat', 30.314933);
            $lng = $this->_param('lng', 120.337985);
            $name = $this->_param('name', '店铺名称');//
            $wxno = $this->_param('wxno', '');
            $logo = $this->_param('logo', '');

            $service_phone = $this->_param('service_phone', '');

            $entity = array(
                'wxno' => $wxno,
                'name' => $name,
                'logo' => $logo,
                'lat' => $lat,
                'lng' => $lng,
                'notes' => $this->_param('notes', ''),
                'service_phone' => $service_phone,
            );
            $result = (new StoreLogic())->saveByID($id, $entity);

            if ($result['status']) {
                //暂时注释这个功能 by hebidu @20170103
//                $this->savePromotion($id);
                $this->success("操作成功！", url('Admin/Store/index'));
            } else {
                $this->error($result['info']);
            }
        }
    }

    public function open(){
        $isopen = 1-$this->_param('isopen',0);

        $id = $this->_param('id', -1);

        $entity = array(
            'isopen'=>$isopen
        );
        $result = (new StoreLogic())->saveByID($id,$entity);

        if ($result['status'] === false) {
            $this -> error($result['info']);
        } else {
            $this -> success(L('RESULT_SUCCESS'), url('Admin/' . CONTROLLER_NAME . '/index'));
        }


    }

    public function delete(){
        $map = array('id' => $this->_param('id', -1));

        $result = (new ProductLogic())->queryNoPaging(array('storeid'=>$map['id']));

        if(!$result['status']){
            $this->error($result['info']);
        }
        if(is_array($result['info']) && count($result['info']) > 0){
            $this->error("该商店尚有相关联数据，无法删除！");
        }

        $result = (new StoreLogic())->delete($map);

        if ($result['status'] === false) {
            $this -> error($result['info']);
        } else {
            $this -> success(L('RESULT_SUCCESS'), url('Admin/' . CONTROLLER_NAME . '/index'));
        }
    }

    //==============================其它功能接口

    /**
     * ajax获取类目信息
     */
    public function cate(){
        $cate_id = $this->_param('cateid',-1);
        if($cate_id == -1){
            $this->success(array());
        }

        $map = array('parent'=>$cate_id);

        $result = (new CategoryLogic())->queryNoPaging($map);
        
        if($result['status']){
            $this->success('','',$result['info']);
        }else{
            $this->error($result['info']);
        }
    }
}