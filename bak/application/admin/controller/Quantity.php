<?php

/**
 * Created by PhpStorm.
 * User: boye009
 * Date: 2016/4/18
 * Time: 10:19
 */

namespace app\admin\controller;

use app\src\goods\logic\ProductSkuLogic;
use app\src\goods\logic\ProductLogic;
use app\src\goods\logic\SkuvalueLogic;
use app\src\productquantityhis\logic\ProductQuantityHisLogic;
use app\src\system\logic\DatatreeLogicV2;
use app\src\user\logic\UcenterMemberLogic;
use app\src\goods\logic\SkuLogic;
class Quantity extends Admin{

    public function alert(){
        if(IS_GET){
            $map = array(
                'itboye_product_sku.quantity' => array('ELT',10),
                'itboye_product.status' => 1,
                'itboye_product.onshelf' => 1,
            );
//            $result = apiCall(ProductSkuApi::QUERY_WITH_PRODUCT,array($map,array('curpage'=>$this->_param('p',1),'size'=>10)));

            $result =(new ProductSkuLogic())->query_with_product([$map,['curpage'=>$this->_param('p',1),'size'=>10]]);

            if(!$result['status']){
                $this -> error($result['info']);
            }
            foreach($result['info']['list'] as &$vo){
                $tmp_sku = $vo['sku_id'];
                $tmp_sku = explode(';',$tmp_sku);
                array_pop($tmp_sku);
                foreach($tmp_sku as $k => $v){
                    $tmp = explode(':',$v);
                    $tmp_sku[$k] = array('id'=>$tmp[0],'vid'=>$tmp[1]);
                }
                $sku_all = $this -> getSkuName(json_encode($tmp_sku));
                if($sku_all['status']){
                    $vo['sku_all'] = $sku_all['info'];
                }else{
                    $this -> error($sku_all['info']);
                }
                $vo['sku'] = json_encode($tmp_sku);
            }
//            dump($result);
            $this -> assign('list',$result['info']['list']);
            $this -> assign('show',$result['info']['show']);
            return $this -> boye_display();
        }
    }

    public function detail(){
        if(IS_GET){
            $pid = $this->_param('pid','');
            $sku = $this->_param('sku','');
            $startdatetime = $this->_param('startdatetime','');
            $enddatetime = $this->_param('enddatetime','');
            $dtree_type = $this->_param('dtree_type',0);
            if($pid === '' || $sku === '' || $startdatetime === '' || $enddatetime === ''){
                $this -> error('参数有误');
            }
            //验证pid合法性
            $map = array(
                'id'=>$pid,
                'status'=>1,
                'onshelf'=>1
            );
            $product_name = "";
            //司机一起查询产品详细信息
            $result = (new ProductLogic())->getInfo($map);

            if($result['status']){
                if(count($result['info']) == 0){
                    $this -> error("商品选择错误");
                }else{
                    $product_name = $result['info']['name'];
                }
            }else{
                $this -> error($result['info']);
            }
            //验证pid和sku的合法性
            $sku_id = $this -> formatSku($sku);
            $map = array(
                'product_id'=>$pid,
                'sku_id'=>$sku_id,
            );
            $quantity = false;
            $result = (new ProductSkuLogic())->getInfo($map);

            if($result['status']){
                if(count($result['info']) == 0){
                    $this -> error("商品参数错误");
                }else{
                    $quantity = $result['info']['quantity'];
                }
            }else{
                $this -> error($result['info']);
            }
            if($quantity === false || !is_numeric($quantity)){
                $this -> error("参数错误");
            }
            if($dtree_type){
                $map = [
                    'id'=>$dtree_type
                ];
            }
            //验证dtree_type
            $result = (new DatatreeLogicV2())->getInfo($map);
            if($result['status']){
                if($result['info']['parentid'] != getDatatree('QUANTITY_TYPE')){
                    $dtree_type = 0;
                }
            }else{
                $this -> error($result['info']);
            }


            $sku_all = $this -> getSkuName($sku);
//            dump($sku_all);
            if($sku_all['status']){
                $sku_all = $sku_all['info'];
            }else{
                $this -> error($sku_all['info']);
            }
            $this -> assign('sku',$sku_all);
            $this -> assign('product',array('id'=>$pid,'name'=>$product_name));
            $this -> assign('sku_id',$sku_id);
            $this -> assign('startdatetime',$startdatetime);
            $this -> assign('enddatetime',$enddatetime);
            $startdatetime = strtotime($startdatetime);
            $enddatetime = strtotime($enddatetime);
            $map = array(
                'pid' => $pid,
                'sku_id' => $sku_id,
                'create_time' => array(array('EGT',$startdatetime),array('ELT',$enddatetime)),
            );
//            dump($map);
            if($dtree_type != 0){
                $map['dtree_type'] = $dtree_type;
            }
            $page = ['curpage'=>$this->_param('p',1),'size'=>10];
            $order = "create_time asc";
//            $result = apiCall(ProductQuantityHisApi::QUERY,array($map,array('curpage'=>$this->_param('p',1),'size'=>10),$order));
            $result = (new ProductQuantityHisLogic())->queryWithPagingHtml($map,$page,$order);
            dump($result);
            exit;
            if(!$result['status']){
                $this -> error($result['info']);
            }
            foreach($result['info']['list'] as $k => $v){
//                $res = apiCall(DatatreeApi::GET_INFO,array(array()));
                $res = (new DatatreeLogicV2())->getInfo(['id'=>$v['dtree_type']]);
                dump($res);
                exit;
                if($res['status']){
                    $result['info']['list'][$k]['dtree'] = $res['info']['name'];
                }else{
                    $this -> error($res['info']);
                }
            }
            $this -> assign('list',$result['info']['list']);
            $this -> assign('show',$result['info']['show']);
            return $this -> boye_display();
        }
    }


    public function add(){
        if(IS_GET){
            $pid = $this->_param('pid','');
            $sku = $this->_param('sku','');
            //$sku = json_decode(htmlspecialchars_decode($sku),true);
            //验证pid合法性
            $map = array(
                'id'=>$pid,
                'status'=>1,
                'onshelf'=>1
            );
            $result = (new ProductLogic())->getInfo($map);
            if($result['status']){
                if(count($result['info']) == 0){
                    $this -> error("商品选择错误");
                }else{
                    $product_name = $result['info']['name'];
                }
            }else{
                $this -> error($result['info']);
            }
            //验证pid和sku的合法性
            $sku_id = $this -> formatSku($sku);
            $map = array(
                'product_id'=>$pid,
                'sku_id'=>$sku_id,
            );
            $quantity = false;
            $result = (new ProductSkuLogic())->getInfo($map);
            if($result['status']){
                if(count($result['info']) == 0){
                    $this -> error("商品参数错误");
                }else{
                    $quantity = $result['info']['quantity'];
                }
            }else{
                $this -> error($result['info']);
            }
            if($quantity === false || !is_numeric($quantity)){
                $this -> error("参数错误");
            }
            $sku_all = $this -> getSkuName($sku);
            if($sku_all['status']){
                $sku_all = $sku_all['info'];
            }else{
                $this -> error($sku_all['info']);
            }
            $this -> assign('sku_id',$sku_id);
            $this -> assign('product',array('id'=>$pid,'name'=>$product_name,'quantity'=>$quantity));
            $this -> assign('sku',$sku_all);
            return $this ->boye_display();
        }else{
            //post添加纪录
            $pid = $this->_param('pid','');
            $change_time = $this->_param('change_time','');
            $notes = $this->_param('notes','');
            $sku_id = $this->_param('sku_id','');
            $dtree_type = $this->_param('dtree_type','');
            $change = $this->_param('change','');
            $quantity = '';
            $user_name = 0;
            if($pid === '' || $change_time === '' || $change === '' || $notes === '' || $dtree_type === ''){
                $this -> error("参数错误");
            }
            //检测数据
            if($change === 0){
                $this -> error('change参数错误');
            }
//            $result = apiCall(ProductApi::GET_INFO,array(array('id'=>$pid)));
            $result = (new ProductLogic())->getInfo(['id'=>$pid]);
            if($result['status']) {
                if(is_null($result['info'])){
                    $this -> error('pid参数错误');
                }
            }else{
                $this -> error($result['info']);
            }
            $map = array(
                'product_id' => $pid,
                'sku_id' => $sku_id,
            );
//            $result = apiCall(ProductSkuApi::GET_INFO,array($map));
            $result = (new ProductSkuLogic())->getInfo($map);
            if($result['status']){
                if(is_null($result['info'])){
                    $this -> error("sku_id参数错误");
                }
                $quantity = $result['info']['quantity'];
            }else{
                $this -> error($result['info']);
            }
            if($quantity === ''){
                $this -> error('商品库存错误');
            }
            $map = array(
                'id' => $dtree_type,
            );
//            $result = apiCall(DatatreeApi::GET_INFO,array($map));
            $result = (new DatatreeLogicV2())->getInfo($map);;
            if(!empty($result)){
                if($result['parentid'] != getDatatree('QUANTITY_TYPE')){
                    $this -> error('类型选择错误');
                }
            }else{
                $this -> error($result['info']);
            }
//            $result = apiCall(UserApi::GET_INFO,array(UID));
            $result = (new UcenterMemberLogic())->getInfo(['id'=>UID]);
            if($result['status']){
                if(is_null($result['info'])){
                    $this -> error('管理身份过期');
                }
                $user_name = $result['info']['username'];
            }else{
                $this -> error($result['info']);
            }

            if($user_name === 0){
                $this -> error('管理身份有误');
            }

//            $left_quantity = $quantity + (int)$change;
//            if($left_quantity < 0){
//                $this -> error("剩余库存数不能为负");
//            }
            $entity[] = array(
                'change' => $change,
                'pid' => $pid,
                'create_time' => time(),
                'change_time' => strtotime($change_time),
                'operator_uid' => UID,
                'operator_name' => $user_name,
                'notes' => $notes,
                'sku_id' => $sku_id,
//                'left_quantity' => $left_quantity,
                'dtree_type' => $dtree_type,
            );

//            $result = apiCall(ProductQuantityHisApi::ADD_ALL,array($entity));
            $result = (new ProductQuantityHisLogic())->addAll($entity);
            if($result['status']){
                $this -> success('变动成功',url('Admin/Quantity/index',['pid'=>$pid]));
            }else{
                $this -> error($result['info']);
            }
        }
    }

    public function index(){

        $pid = $this->_param('pid');

        //获取有效产品数据;
        $Effective = ['status' => 1];
        $result = (new ProductLogic())->queryNoPaging($Effective);
        if(!$result['status']){
            $this -> error($result['info']);
        }
        $this -> assign('productList',$result['info']);

        if($pid === ''){
            $pid = $result['info'][0]['id'];
        }
        //判断pid有效性
        $map = array(
            'id' => $pid,
            'status' => 1,
        );
        $result = (new ProductLogic())->getInfo($map);

        if($result['status']){
            if(is_null($result['info'])){
                $this -> error("pid参数有误");
            }
        }else{
            $this -> error($result['info']);
        }
        $result = (new ProductSkuLogic())->isSku($pid);
        if($result['status']){
            if($result['info'] == 0){
                $this -> error("该商品未添加规格信息");
            }
        }else{
            $this -> error($result['info']);
        }
        $this -> assign('pid',$pid);
        $result = (new ProductSkuLogic())->getSkuName($pid,'sku_desc');
        if(empty($result)){
            $this -> error($result['info']);
        }
        $sku_name = json_decode($result['info'],true);
        $result = (new ProductSkuLogic())->getSkuId($pid,'sku_id');
        if(empty($result)){
            $this -> error($result['info']);
        }
        $sku_id = json_decode($result['info'],true);
        //获取选中的规格
        $sel_sku = array();
        //合并两个数组
        $sku = array();
        foreach($sku_id as $k => $v){
            array_push($sel_sku,array('id'=>$v['id'],'vid'=>$v['vid'][0]));
            $sku[$k]['id'] = array('id_id'=>$sku_id[$k]['id'],'name_id'=>$sku_name[$k]['id']);
            $sku[$k]['vid'] = array();
            foreach($v['vid'] as $kk => $vv){
                $sku[$k]['vid'][$kk] = array('id_vid'=>$sku_id[$k]['vid'][$kk],'name_vid'=>$sku_name[$k]['vid'][$kk]);
            }
        }
        $this -> assign('sku',$sku);
        $this -> assign('sel_sku',$sel_sku);
        //默认时间
        $enddatetime = date('Y-m-d H:i:s');
        $startdatetime = date('Y-m-d H:i:s',strtotime('-30 day'));
        $this -> assign('startdatetime',$startdatetime);
        $this -> assign('enddatetime',$enddatetime);

        return $this -> boye_display();
    }

    public function getData(){
        $sel_sku = $this->_param('sel_sku','');
        $select_product = $this->_param('select_product','');
        $startdatetime = $this->_param('startdatetime','');
        $enddatetime = $this->_param('enddatetime','');
        //验证为空参数
        if($select_product == '' || $startdatetime == '' || $enddatetime == '' || !is_numeric($select_product)){
            $this -> ajaxReturnErr("参数有误");
        }
        //时间
        $startdatetime = strtotime($startdatetime);
        $enddatetime = strtotime($enddatetime);
        if($startdatetime >= $enddatetime){
            $this -> ajaxReturnErr("时间选择错误");
        }
        $x = $this -> getBetweenDate($startdatetime,$enddatetime);

        //验证sel_sku参数
//        $result = apiCall(ProductSkuApi::HAS_SKU,array($select_product));
        $result = (new ProductSkuLogic())->hasSku($select_product);
        if($result['status']){
            if($result['info'] != 0 && $sel_sku == ""){
                $this -> ajaxReturnErr("缺少规格参数");
            }
        }else{
            $this -> ajaxReturnErr($result['info']);
        }
        //获取数据
        $sel_sku = $this -> formatSku($sel_sku);

        //查询销售数据
        $quantity_sale_out = getDatatree('QUANTITY_SALE_OUT');//库存销售变动dtree
        $map = array(
            'pid'=>$select_product,
            'sku_id'=>$sel_sku,
            'dtree_type'=>$quantity_sale_out,
            'create_time'=>array(array('EGT',$startdatetime),array('elt',$enddatetime)),
        );
        $order = "create_time asc";
//        $result = apiCall(ProductQuantityHisApi::QUERY_NO_PAGING,array($map,$order));
        $result = (new ProductQuantityHisLogic())->queryNoPaging($map,$order);
        $sale_out = array();
        if($result['status']){
            foreach($result['info'] as $k => $v){
                $index = date('Y-m-d',$v['create_time']);
                if(isset($sale_out[$index])){
                    $sale_out[$index] = $sale_out[$index] - $v['change'];
                }else{
                    $sale_out[$index] = 0 - $v['change'];
                }
            }
        }else{
            $this -> ajaxReturnErr($result['info']);
        }
        $sale_out = $this -> formatSaleOut($x,$sale_out);
        //查询库存信息
        $map = array(
            'pid'=>$select_product,
            'sku_id'=>$sel_sku,
            'create_time'=>array(array('egt',$startdatetime),array('elt',$enddatetime)),
        );
        $order = "create_time asc";
//        $result = apiCall(ProductQuantityHisApi::QUERY_NO_PAGING,array($map,$order));
        $result = (new ProductQuantityHisLogic())->queryNoPaging($map,$order);
        $quantity_his = array();
        $left_quantity = array();
        if($result['status']){
            foreach($result['info'] as $k => $v){
                $index = date('Y-m-d',$v['create_time']);
                if(isset($quantity_his[$index])){
                    //已经有了 比较时间 取后者
                    if($quantity_his[$index]['time'] < $v['create_time']){
                        $quantity_his[$index] = array('time'=>$v['create_time'],'left_quantity'=>$v['left_quantity'],'change'=>$v['change']);
                    }
                }else{
                    $quantity_his[$index] = array('time'=>$v['create_time'],'left_quantity'=>$v['left_quantity'],'change'=>$v['change']);
                }
            }
            foreach($quantity_his as $k => $v){
                $left_quantity[$k] = array('left_quantity'=>$v['left_quantity'],'change'=>$v['change']);
            }
        }else{
            $this -> ajaxReturnErr($result['info']);
        }
        $left_quantity = $this -> formatLeftQuantity($x,$left_quantity,$select_product,$sel_sku);

        $data = array(
            'x' => $x,
            'quantity' => $left_quantity,
            'sale' => $sale_out,
        );
        $this -> ajaxReturnSuc($data);
    }

    /*******************************************私有方法*************************************************/

    private function getSkuName($sku){
        $sku = json_decode(htmlspecialchars_decode($sku),true);
        $tmp_sku = array();
        foreach($sku as $k => $v){
            $map = array(
                'id' => $v['id'],
            );
            $result = (new SkuLogic())->getInfo($map);
            if($result['status']){
                $tmp_sku[$k]['id'] = array('id_id'=>$v['id'],'id_name'=>$result['info']['name']);
            }else{
                return array('status'=>false,'info'=>$result['info']);
            }
            $map = array(
                'id' => $v['vid'],
            );
            $result = (new SkuvalueLogic())->getInfo($map);
            if($result['status']){
                $tmp_sku[$k]['vid'] = array('vid_id'=>$v['vid'],'vid_name'=>$result['info']['name']);
            }else{
                return array('status'=>false,'info'=>$result['info']);
            }
        }
        return array('status'=>true,'info'=>$tmp_sku);
    }

    private function formatLeftQuantity($x,$left_quantity,$pid,$sku_id){
        $res = array();
        if(count($left_quantity) == 0){
            //若没有库存变动信息
            $map = array(
                'product_id' => $pid,
                'sku_id' => $sku_id,
            );
//            $result = apiCall(ProductSkuApi::GET_INFO,array($map));
            $result = (new SkuLogic())->getInfo([$map]);
            if($result['status']){
                $quantity = $result['info']['quantity'];
                foreach ($x as $v) {
                    $res[] = $quantity;
                }
            }else{
                $this -> ajaxReturnErr($result['info']);
            }
        }else{
            $tmp = current($left_quantity);
            $rec = $tmp['left_quantity'] - $tmp['change'];//初始记录
            foreach($x as $v){
                if(isset($left_quantity[$v])){
                    $rec = $left_quantity[$v]['left_quantity'];
                    $res[] = $rec;
                }else{
                    $res[] = $rec;
                }
            }
        }
        return $res;
    }

    private function formatSaleOut($x,$sale_out){
        $res = array();
        //先初始化为0
        $sale = array();
        foreach($x as $v){
            $sale[$v] = 0;
        }
        //merge
        $sale = array_merge($sale,$sale_out);
        //get value
        foreach($sale as $v){
            $res[] = $v;
        }
        return $res;
    }

    private function getBetweenDate($time1,$time2){
        $x = array();
        for($i=$time1;date('Y-m-d',$i)!=date('Y-m-d',$time2);$i+=24*3600){
            $x[] = date('Y-m-d',$i);
        }
        $x[] = date('Y-m-d',$time2);
        return $x;
    }

    private function formatSku($sel_sku){
        $sku = "";
        $sel_sku = json_decode(htmlspecialchars_decode($sel_sku),true);//解析json
        foreach($sel_sku as $k => $v){
            $sku = $sku.$v['id'].":".$v['vid'].";";
        }
        return $sku;
    }

    private function ajaxReturnSuc($info){
        exit(json_encode(array('status'=>true,'info'=>$info)));
    }
    private function ajaxReturnErr($info){
        exit(json_encode(array('status'=>false,'info'=>$info)));
    }

}