<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 15:00
 */
namespace app\admin\controller;

use app\src\admin\helper\AdminSessionHelper;
use app\src\category\logic\CategoryLogic;
use app\src\file\logic\UserPictureLogic;
use app\src\freight\logic\FreightTemplateLogic;
use app\src\goods\action\ProductAddAction;
use app\src\goods\action\ProductDetailAction;
use app\src\goods\action\ProductEditAction;
use app\src\goods\logic\ProductFaqLogic;
use app\src\goods\logic\ProductGroupLogic;
use app\src\goods\logic\ProductLogic;
use app\src\goods\logic\ProductPropLogic;
use app\src\goods\logic\ProductSkuLogic;
use app\src\goods\logic\SkuLogic;
use app\src\goods\logic\SkuvalueLogic;
use app\src\store\logic\StoreLogic;
use app\src\system\logic\DatatreeLogicV2;
use app\src\system\logic\ProvinceLogic;
use think\Request;

class Product extends Admin
{

    private $store_id;

    public function _initialize()
    {
        parent::_initialize();
        //从Session 中获取店铺数据
        $this->store_id = AdminSessionHelper::getCurrentStoreId();
        //从输入获取店铺数据，并赋值 Session
        if (empty($this->store_id)) {
            $this->store_id = $this->_param('store_id', 0);
            AdminSessionHelper::setCurrentStoreId($this->store_id);
        }
        //从数据库查询店铺数据，并赋值 Session
        if (empty($this->store_id)) {
            $result = (new StoreLogic())->query();
            if ($result['status']) {
                $this->store_id = $result['info']['list'][0]['id'];
                AdminSessionHelper::setCurrentStoreId($this->store_id);
            }
        }
        //无店铺数据退出
        if (empty($this->store_id)) {
            $this->error("缺少店铺ID参数！");
        }
    }

    public function group()
    {
        $store_id = $this->store_id;//_param('store_id', 0);

        $map = ['parentid' => getDatatree('WXPRODUCTGROUP')];
        $r = (new DatatreeLogicV2)->queryNoPaging($map);
        $l = new ProductGroupLogic;
        $now = time();
        //统计各组商品情况 过期/总数
        foreach ($r as &$v) {
            $id = $v['id'];
            $r2 = $l->count(['g_id'=>$id]);
            $v['all_pro'] = $r2['status'] ? $r2['info'] : 0;
            $r2 = $l->count(['g_id'=>$id,'start_time'=>['gt',$now]]);
            $v['not_sta'] = $r2['status'] ? $r2['info'] : 0;
            $r2 = $l->count(['g_id'=>$id,'end_time'=>['lt',$now]]);
            $v['overdue'] = $r2['status'] ? $r2['info'] : 0;
        }
        $this->assign("groups", $r);
        $this->assign("store_id", $store_id);
        return $this->boye_display();
    }

    public function deleteGroup()
    {
        $id = $this->_param('id', 0);

        $result =(new DatatreeLogicV2())->queryNoPaging(['parentid' => $id]);

        if (is_array($result) && count($result) > 0) {
            $this->error("有子级，请先去数据字典中删除所有子级！");
        }

        if ((new DatatreeLogicV2)->delete(['id' => $id])) {
            //删除成功，则删除该分组商品，防止垃圾数据 TODO 事务回滚
            $r =  (new ProductGroupLogic)->delete(['g_id' => $id]);
            !$r['status'] && $this->error($r['info']);
        }
        $this->success("操作成功！");

    }

    public function addGroup()
    {
        if (IS_GET) {
            return $this->boye_display();
        } else {
            $parentid = getDatatree('WXPRODUCTGROUP');
            $result = (new DatatreeLogicV2())->getInfo(['id' => $parentid]);
            $parents = $parentid . ',';
            $level = intval($result['level']) + 1;
            $parents = $result['parents'] . $parents;

            $entity = array(
                'name' => $this->_param('name', ''),
                'notes' => $this->_param('notes', ''),
                'sort' => $this->_param('sort', ''),
                'level' => $level,
                'parents' => $parents,
                'parentid' => $parentid,
                'code' => $this->_param('code', ''),
                'iconurl' => $this->_param('iconurl', ''),
            );
            if (!(new DatatreeLogicV2)->add($entity)){
                $this->error($result['info']);
            }

            $this->success("操作成功！", url('Admin/Product/group'));
        }
    }

    /**
     * 商品运费设置
     */
    public function express()
    {
        if (IS_GET) {

            $id = $this->_param('id', 0);

            $result = (new ProductLogic())->getInfo(['id' => $id]);

            if (!$result['status']) {
                $this->error($result['info']);
            }

            if (is_null($result['info'])) {
                $this->error("警告：商品信息获取失败！");
            }

            $product = $result['info'];

            $location = $product['loc_country'] . ">>" . $product['loc_province'] . ">>" . $product['loc_city'] . ">>" . $product['loc_address'];

            $this->assign("store_id", $product['store_id']);
            $this->assign("location", $location);

            $this->assign("template_id", $product['template_id']);

            $result = (new ProvinceLogic())->queryNoPaging(['countryid' => 1017]);
            if ($result['status']) {
                $this->assign("province", $result['info']);
            } else {
                $this->error("警告：省份信息获取失败！");
            }


            $this->assign("countrylist", config('COUNTRY_LIST'));
            $this->assign("id", $id);
            return $this->boye_display();
        } else {

            $query = $this->_param('query', '', 'htmlspecialchars_decode');
            $query = json_decode($query, JSON_UNESCAPED_UNICODE);

            $id = $this->_param('id', '');
            if (empty($id)) {
                $this->error("商品ID失效！");
            }

            $flag = $query['islocchange'];
            $entity = array();
            if ($flag) {
                $entity['loc_country'] = $query['country'];
                $entity['loc_province'] = $query['province'];
                if ($entity['loc_province'] == '请选择省份') {
                    $entity['loc_province'] = '';
                }
                $entity['loc_city'] = $query['city'];
                $entity['loc_address'] = $query['area'];
            }


            $result = (new ProvinceLogic())->saveByID($id, $entity);

            if (!$result['status']) {
                $this->error($result['info']);
            }

            $this->success("操作成功！");

        }
    }


    /**
     * 商品SKU 管理
     */
    public function sku(){

        if (IS_GET) { //view
            $id = $this->_param('id', 0);
            $r = (new ProductLogic())->getInfo(['id' => $id]);
            !$r['status'] && $this->error($r['info']);
            $product = $r['info'];
            is_null($product) && $this->error("警告：商品信息获取失败！");

            //? 多规格
            $has_sku = (new ProductSkuLogic())->hasSku($id);
            !$has_sku['status'] && $this->error($has_sku['info']);
            $has_sku = $has_sku['info'];
            if ($has_sku == 1) { //多规格
                $skuinfo = (new ProductSkuLogic())->getSkuId($id, 'sku_id');
                !$skuinfo['status'] && $this->error($skuinfo['info']);
                $skuinfo = $skuinfo['info'];
                // string(34) "[{"id":"221","vid":["919","920"]}]"
                $skuinfo = $this->getSkuValue(json_decode($skuinfo, JSON_UNESCAPED_UNICODE));
                // string(8) "919,920,"
                $this->assign("skuinfo", $skuinfo);

                $skulist = (new ProductSkuLogic())->queryNoPagingWithMemberPrice(['product_id' => $id]);

               //  获取vip1.2和价格
               // foreach ($skulist['info'] as &$val) {
               //     foreach ($val['member_price'] as &$vo) {
               //         if ($vo['member_group_id'] == 1) {
               //             $val['vip1_price'] = $vo['price'];
               //         }
               //         if ($vo['member_group_id'] == 2) {
               //             $val['vip2_price'] = $vo['price'];
               //         }
               //     }
               //     unset($val['member_price']);
               // }

                if ($skulist['status']) {
                    $skuvaluelist = json_encode($skulist['info'], JSON_UNESCAPED_UNICODE);
                // string(846) "[
                // {"id":18,"sku_id":"221:919;","sku_desc":"期限:三个月;","ori_price":6000,"price":3000,"quantity":99999,"product_code":"","create_time":1489719539,"product_id":"8","icon_url":0,"cnt1":0,"price2":0,"cnt2":0,"price3":0,"cnt3":0,"update_time":1489719539,"member_price1":0,"member_price":[{"id":35,"p_id":8,"member_group_id":1,"sku_id":18,"price":"0.00"},
                // ...]"
                    $this->assign("skuvaluelist", $skuvaluelist);
                }
            } else { //统一规格
                $res = (new ProductSkuLogic())->getInfoWithMemberPrice(['product_id' => $id]);

                !$res['status'] && $this->error($res['info']);
                $this->assign('unify_price',$res['info']['price']);
                $this->assign('unify_ori_price', $res['info']['ori_price']);
                $this->assign('unify_group_price', $res['info']['group_price']);
                $this->assign('unify_quantity', $res['info']['quantity']);
                $this->assign('unify_product_code', $res['info']['product_code']);

               //  获取vip1.2和价格
               // $vip_price = [0, 0];
               // foreach ($res['info']['member_price'] as $val) {
               //     if ($val['member_group_id'] == 1) {
               //         $vip_price[0] = $val['price'];
               //     }
               //     if ($val['member_group_id'] == 2) {
               //         $vip_price[1] = $val['price'];
               //     }
               // }
               // $this->assign('vip_price', $vip_price);
            }
            $this->assign("has_sku", $has_sku);
            $this->assign("store_id", $product['store_id']);

            //分类的规格
            $cate_id = $product['cate_id'];
            $r = (new SkuLogic())->querySkuTable($cate_id);
            !$r['status'] && $this->error($r['info']);
            // $this->assign("skulist", $this->color2First($r['info']));
            $this->assign("skulist", $r['info']);
            //   array(1) {
            //     [0] => array(3) {
            //       ["id"] => int(221)
            //       ["name"] => string(6) "期限"
            //       ["value_list"] => array(2) {
            //         [0] => array(3) {
            //           ["id"] => int(919)
            //           ["name"] => string(9) "三个月"
            //           ["sku_id"] => int(221)
            //         }
            //         [1] => array(3) {
            //           ["id"] => int(920)
            //           ["name"] => string(9) "六个月"
            //           ["sku_id"] => int(221)
            //         }
            //       }
            //     }
            //   }

            //类目信息
            $r = (new CategoryLogic())->getInfo(['id' => $cate_id]);
            !$r['status'] && $this->error($r['info']);

            $level = 0;
            $parent = 0;
            $preparent = -1;
            if (is_array($r['info'])) {
                $level  = $r['info']['level'];
                $parent = $r['info']['parent'];
                //上级类目信息
                $r = (new CategoryLogic())->getInfo(['id' => $parent]);
                !$r['status'] && $this->error($r['info']);
                $preparent = $r['info']['parent'];
            }

            $this->assign("cate_id", $level);
            $this->assign("parent", $parent);
            $this->assign("preparent", $preparent);
            $this->assign("cate_id", $cate_id);
            $this->assign("id", $id);
            return $this->boye_display();

        } else { //save
            $id = $this->_param('id', 0);
            $has_sku = $this->_param('has_sku', 0);
            if ($has_sku == 1) {
                $sku_list = $this->_param('sku_list', '');
                $sku_list = json_decode(htmlspecialchars_decode($sku_list), JSON_UNESCAPED_UNICODE);
                // {"sku_id":"221:919;","price":30,"icon_url":"","quantity":99999,"product_code":"","ori_price":60,"group_price":29,"sku_desc":"期限:三个月;"}
            } else {
                $this->error('因商品时限特殊性,不允许统一规格');
                // $group_price  = $this->_param('group_price/f', 0);
                // $ori_price    = $this->_param('ori_price/f', 0);
                // $price        = $this->_param('price/f', 0);
                // $product_code = $this->_param('product_code', "");
                // $quantity     = $this->_param('quantity/d', 0);
                // $icon_url     = $this->_param('icon_url/d', 0);
                // $sku_list = [
                //     'group_price'  => $group_price,
                //     'ori_price'    => $ori_price,
                //     'price'        => $price,
                //     'product_code' => $product_code,
                //     'product_id'   => $id,
                //     'quantity'     => $quantity,
                //     'icon_url'     => $icon_url,
                // ];
            }
            $r = (new ProductSkuLogic)->addSkuList($id, $has_sku, $sku_list);
            !$r['status'] && $this->error($r['info']);
            $this->success("保存成功！");
        }
    }


    /**
     *
     */
    private function getSkuValue($skuvalue)
    {
        $valuelist = "";
        foreach ($skuvalue as $value) {
            foreach ($value['vid'] as $vo) {
                $valuelist = $valuelist . $vo . ",";
            }
        }
        return $valuelist;
    }

    /**
     * 商品详情页/新增
     * @param $get .productid  商品ID
     * @param $get .store_id 店铺ID
     */
    public function detail()
    {
        if (IS_GET) {
            //$productid = $this->_param('id','');
            $id = $this->_param('id', 0);
            //$store_id = _param('store_id', 0);
            $store_id = $this->store_id;
            if (empty($id)) {
                $this->error("缺少商品ID");
            }
            if (empty($store_id)) {
                $this->error("缺少店铺ID");
            }
            $map['id'] = $id;


            $result = (new ProductLogic())->getInfo($map);

            if ($result['status']) {
                $detail = $result['info']['detail'];
            } else {
                $this->error("商品信息获取失败！");
            }

            $this->assign("detail", $detail);
            $this->assign("id", $id);
            // $this->assign("store_id", $store_id);
            return $this->boye_display();

        } else {
            $detail = $this->_param("detail", '');
            $id = $this->_param("id", '');

            $map['id'] = $id;

            $result =  (new ProductLogic())->save($map, array('detail' => $detail));
            if ($result['status']) {
                $this->success("修改成功！");
            } else {
                $this->error($result['info']);
            }
        }
    }

    /**
     * 首页/商品管理页面
     */
    public function index()
    {

        $onshelf = $this->_param('onshelf', 0);
        return $this->manager($onshelf);
    }

    private function manager($onshelf){

        $cate = $this->_param('cate', 0);
        $name = urldecode(urldecode($this->_param('name', '')));

        //检测store_id 是否合法
        $result = (new StoreLogic())->getInfo(array('id' => $this->store_id));

        if (!$result['status']) {
            $this->error('店铺参数错误！');
        }

        if (is_null($result['info'])) {
            $this->error("未输入合法店铺参数!");
        }

        $params = array('onshelf' => $onshelf, 'store_id' => $this->store_id);

        $map = array();
        if (!empty($name)) {
            $map['name'] = array('like', '%' . $name . '%');
            $params['name'] = $name;
        }
        if(!empty($cate)){
            $map['cate_id'] = $cate;
            $params['cate'] = $cate;
        }

        $map['onshelf'] = $onshelf;
        $map['status'] = 1;
        $map['store_id'] = $this->store_id;
        $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
        $order = " create_time desc ";

        $result = (new ProductLogic())->manager($map, $page, $order, $params);

        if ($result['status']) {

            $this->assign('cate', $cate);
            $this->assign('name', $name);
            $this->assign('onshelf', $onshelf);
            $this->assign('p', $this->_param('p', 0));
            $this->assign('store_id', $this->store_id);
            $this->assign('show', $result['info']['show']);
            $this->assign('list', $result['info']['list']);
            $store = (new StoreLogic())->getInfo(array('id' => $this->store_id));
            if (!$store['status']) {
                $this->error($store['info']);
            }
            $this->assign("store", $store['info']);

            $result = (new CategoryLogic())->queryAllCategory();
            if (!$result['status']) {
                $this->error($result['info']);
            }
            $this->assign("cate_list", $result['info']);

            return $this->boye_display('default','Product/index');
        } else {
            $this->error($result['info']);
        }
    }

    /**
     * 上架的商品
     */
    public function shelfOn(){
        $onshelf = 1;
        return $this->manager($onshelf);
    }

    /**
     * 下架的商品
     */
    public function shelfOff(){
        $onshelf = 0;
        return $this->manager($onshelf);
    }

    /**
     * 商品上下架
     * @internal param 删除成功后跳转 $success_url
     */
    public function shelf()
    {
        $status = $this->_param('on/d', 0);
        $id = $this->_param('id', -1);
        $map = array('id' => $id);

        if ($status == 1) {
            $isSku = (new ProductSkuLogic())->isSku($id);

            if ($isSku['status']) {
                if ($isSku['info'] == 0) {
                    $this->error('请先填写商品规格信息!',url('Admin/Product/index'));
                }
            } else {
                $this->error($isSku['info']);
            }
        }

        $entity['onshelf'] = $status;
        $result = (new ProductLogic())->save($map, $entity);

        if ($result['status'] === false) {
            $this->error($result['info']);
        } else {
            $this->success(L('RESULT_SUCCESS'));
        }

    }



    /**
     * 批量上下架
     */
    public function shelfAll()
    {
        $ids = $this->_param('ids', -1);
        $status = $this->_param('status', -1, 'intval');//此处status为0说明上架，为1说明下架
        if ($ids === -1 || $status === -1) {
            $this->error("参数缺失");
        }
        if ($status == 1) {
            $status = 0;
        } else if ($status == 0) {
            $status = 1;
        } else {
            $this->error("参数错误");
        }
        $error = false;
        if ($status == 1) {
            //上架前检查
            foreach ($ids as $k => $v) {
                $map = array(
                    'id' => $v,
                );
                $isSku = (new ProductSkuLogic())->isSku($v);

                if ($isSku['status']) {
                    if ($isSku['info'] == 0) {
                        $error = '请先填写商品规格信息!id为' . $v;
                        break;
                    }
                } else {
                    $this->error($isSku['info']);
                }

                $result = (new ProductLogic())->getInfo($map);
                $template_id = "";
                if ($result['status']) {
                    $template_id = $result['info']['template_id'];
                } else {
                    $this->error($result['info']);
                }

                $result = (new FreightTemplateLogic())->getInfo(['id' => $template_id]);
                if ($result['status']) {
                    if ($result['info'] == null) {
                        $error = '请选择运费模板信息!id为' . $v;
                        break;
                    }
                } else {
                    $this->error($result['info']);
                }

            }

        }
        if ($error === false) {
            //检查无误或者是下架
            $result = (new ProductLogic())->shelfAll($status, $ids);
            if ($result['status']) {
                $this->success(L('RESULT_SUCCESS'));
            } else {
                LogRecord('[INFO]' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
                $this->error($result['info']);
            }
        } else {
            $this->error($error);
        }

    }

    /**
     * 单个删除
     * @param 删除成功后跳转|bool $success_url 删除成功后跳转
     */
    public function delete($success_url = false)
    {

        if ($success_url === false) {
            $success_url = url('Admin/Product/index', array('store_id' => $this->_param('store_id', 1)));
        }

        //TODO: 检测商品的其它数据是否存在
        $id = $this->_param('id', -1);
        $onshelf = $this->_param('onshelf');
        $map = array(
            'id' => $id,
        );

        $result = (new ProductLogic())->pretendDelete($map);

        if ($result['status'] === false) {
            Log::record('[INFO]' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
            $this->error($result['info']);
        } else {
            $this->success(L('RESULT_SUCCESS'));
        }

    }

    /**
     * 批量删除
     */
    public function bulkDelete()
    {
        $ids = $this->_param('ids/a', []);
        if (empty($ids)) $this->error('参数缺失');
        $map = ['id' => ['in', $ids]];
        $result = (new ProductLogic())->pretendDelete($map);

        if ($result['status'] === false) {
            LogRecord('[INFO]' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
            $this->error($result['info']);
        } else {
            $this->success(L('RESULT_SUCCESS'));
        }

    }

    /**
     * 商品预创建－选择类目
     */
    public function precreate(){

        $lang = AdminSessionHelper::getCurrentLang();
        $map = array('parent' => 0,'lang'=>$lang);
        $result = (new CategoryLogic())->queryNoPaging($map);

        if (!$result['status']) {
            $this->error($result['info']);
        }
        $store_id = $this->_param('store_id', 0);
        if ($store_id == 0) {
            $this->error("缺少商铺ID参数");
        }

        $this->assign("store_id", $store_id);
        if ($result['status']) {
            $this->assign("rootcate", $result['info']);
        }
        return $this->boye_display();
    }
    /**
     * 添加商品
     *
     */

    public function create()
    {
        if (IS_POST) {
            $store_id = $this->_param('store_id', 0);
            if ($store_id == 0) {
                $this->error("缺少商铺ID参数");
            }

            //限购
            if ($this->_param('isbuylimit', '0') == 1) {
                $buylimit = $this->_param('buylimit', 0);
            } else {
                $buylimit = 0;
            }

            $action = new ProductAddAction();
            $post = $_POST;
            $post['uid'] = UID;

            $result = $action->add($post);
            if ($result['status']) {
                $this->success("操作成功!", url('Admin/Product/index'));
            } else {
                $this->error($result['info']);
            }
        } else {
            $catename = $this->_param('catename', '');
            $store_id = $this->_param('store_id', 0);
            $cates = $this->_param("cates", '');
            $cates = explode("_", $cates);
            if (count($cates) <= 1) {
                $this->error("商品类目错误！");
            }
            session("cate_id", $cates[count($cates) - 1]);
            session("store_id", $store_id);
            session("catename", $catename);
            session("cates", $this->_param('cates', ''));
            session("code", $this->_param('code', ''));

            $this->assign("cate_id", $cates[count($cates) - 1]);
            $this->assign("store_id", $store_id);
            $this->assign("catename", $catename);
            $this->assign("cates", $this->_param('cates', ''));
            return $this->boye_display();
        }
    }


    /**
     * 商品信息编辑
     */
    public function edit()
    {
        if (IS_GET) {
            $onshelf = $this->_param('onshelf', 0);
            $p = $this->_param('p', 0);
            $id = $this->_param('id', 0);
            $cate_id = $this->_param('cate_id', '');

            $this->getProductPropValues($id);
            $result = (new ProductDetailAction())->detail($id);

            if ($result['status']) {

                //去除主图
                $imgs = $result['info']['carousel_images'];
                $main_img = $result['info']['main_img'];

                $selImgs = [];
                foreach ($imgs as $vo){
                    if($vo != $main_img){
                        array_push($selImgs,$vo);
                    }
                }

                $this->assign("imgs", $selImgs);
                $this->assign("vo", $result['info']);
            }

            $this->assign('cate_id', $cate_id);
            //类目列表
            $cate_name = array();
            while ($cate_id != 0) {
                $result = (new CategoryLogic())->getInfo(array('id' => $cate_id));
                array_push($cate_name, $result['info']['name']);
                $cate_id = $result['info']['parent'];
            }

            $cate_name = array_reverse($cate_name);
            $this->assign('cate_name', $cate_name);
            //查找分类
            $map = array(
                'p_id' => $id,
                'start_time' => array('elt', time()),
                'end_time' => array('egt', time())
            );
            $result = (new ProductGroupLogic())->getInfo($map);

            if ($result['status']) {
                if (is_null($result['info'])) {
                    $this->assign('product_group', 0);
                } else {
                    $this->assign('product_group', $result['info']['g_id']);
                    $this->assign('group_start_time', $result['info']['start_time']);
                    $this->assign('group_end_time', $result['info']['end_time']);
                }
            } else {
                $this->error($result['info']);
            }

            $this->assign('onshelf', $onshelf);
            $this->assign('p', $p);
            return $this->boye_display();
        } else {
            $onshelf = $this->_param('onshelf', 0);
            $id = $this->_param('id', 0);
            $params = Request::instance()->param();

            $result = (new ProductEditAction())->edit($params);
            if(!$result['status']) $this->error($result['info']);
            $this->assign('onshelf', $onshelf);
            $this->success("操作成功!", url('Admin/Product/index'));
        }
    }

    /**
     * 获取商品属性
     */
    private function getProductPropValues($pid)
    {
        $result = (new ProductPropLogic())->queryNoPaging(['pid' => $pid], false, 'value_id');
        $props = [];
        if ($result['status']) {
            foreach ($result['info'] as $val) {
                $props[] = $val['value_id'];
            }
        }
        $this->assign('props_value_ids', json_encode($props));
    }

    //==========================私有方法
//

    /**
     *
     * 采购入库
     *
     * 1. 第一次调用不会操作数据库
     * 2. 递归调用第二次才会改数据库
     *
     * @author hebidu <hebiduhebi@126.com>
     * @date  15/11/30 20:20
     * @copyright by itboye.com
     * @param int $upload_wh 标识是否上报网仓
     * @return mixed
     */
    public function putin($upload_wh = 0)
    {
        if (IS_POST) {

            $id = $this->_post("id", 0, '缺少商品ID');
            $name = $this->_post("name", '');
            $has_sku = $this->_post("has_sku", 0);
            $map = array(
                'id' => $id,
            );

            $field = "quantity";

            //1. 上报网仓
            $data = array(
                'order_code' => time(),
                'items' => array(),
            );

            if ($has_sku) {

                $sku_id = $this->_post("sku_id", '');
                $putin_arr = $this->_post("putin", '');
                $quantity_arr = $this->_post("quantity", '');
                $map = array(
                    'product_id' => $id,
                );
                foreach ($sku_id as $key => $vo) {
                    $map['sku_id'] = $vo;
                    $putin = $putin_arr[$key];
                    $quantity = $quantity_arr[$key];
                    if ($putin == 0) {
                        continue;
                    }
                    if ($putin > 0) {

                        if ($upload_wh) {
                            $result = (new ProductSkuLogic())->setInc($map, $field, $putin);
                        }
                    } elseif ($putin < 0) {
                        //减去SET_DEC 需要传入的正数
                        $putin = 0 - $putin;
                        if ($quantity - $putin < 0) {
                            $putin = $quantity;
                        }

                        if ($upload_wh) {
                            $result = (new ProductSkuLogic())->setDec($map, $field, $putin);
                        }
                        $putin = 0 - $putin;
                    }

                    array_push($data['items'], array(
                        'id' => $id,
                        'name' => $name,
                        'product_code' => $vo,
                        'count' => $putin,
                    ));

                }
            } else {
                $putin = $this->_post("putin", 0, "不能为0");
                $quantity = $this->_post("quantity", 0);

                if ($putin > 0) {
                    if ($upload_wh) {
                        $result = (new ProductLogic())->setInc($map, $field, $putin);
                    }
                } elseif ($putin < 0) {
                    $putin = 0 - $putin;
                    if ($quantity - $putin < 0) {
                        $putin = $quantity;
                    }
                    if ($upload_wh) {
                        $result = (new ProductLogic())->setDec($map, $field, $putin);
                    }
                    $putin = 0 - $putin;
                }

                array_push($data['items'], array(
                    'id' => $id,
                    'name' => $name,
                    'product_code' => $id,
                    'count' => $putin,
                ));
            }


            //id,name,product_code,count
            $this->success("操作成功!");



        } else {

            $id = $this->_get('id', 0, '缺少商品ID');

            $result = (new ProductLogic())->detail($id);

            if ($result['status']) {
                $product = $result['info'];

                $sku_list = $this->getSkuList($product);
                $this->assign("sku_list", $sku_list['sku_list']);
                $this->assign("product", $product);

                return $this->boye_display();
            } else {
                LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
                $this->error(L('UNKNOWN_ERR'));
            }
        }
    }

    private function getSkuList($product)
    {

        $skuinfo = json_decode($product['sku_info']);
        $sku_ids = array('-1');
        $sku_value_ids = array('-1');
        foreach ($skuinfo as $vo) {
            array_push($sku_ids, $vo->id);
            foreach ($vo->vid as $vid) {
                array_push($sku_value_ids, $vid);
            }
        }

        $map = array();
        $map['id'] = array('in', $sku_ids);

        $result = (new SkuLogic())->queryNoPaging($map);
        if (!$result['status']) {
            $this->error($result['info']);
        }
        $sku_result = $result['info'];

        $map = array();
        $map['id'] = array('in', $sku_value_ids);

        $result = (new SkuvalueLogic())->queryNoPaging($map);
        if (!$result['status']) {
            $this->error($result['info']);
        }
        $sku_value_result = $result['info'];
        //上述代码获取SKU以及SKU值的名称

        $sku_arr = array();
        foreach ($sku_result as $_sku) {
            $key = $_sku['id'] . ':';
            foreach ($sku_value_result as $_sku_value) {
                if ($_sku_value['sku_id'] == $_sku['id']) {

                    if (!isset($sku_arr[$_sku['id']])) {
                        $sku_arr[$_sku['id']] = array('id' => $_sku['id'], 'sku_name' => $_sku['name'], 'sku_value_list' => array());
                    }

                    array_push($sku_arr[$_sku['id']]['sku_value_list'], array('id' => $_sku_value['id'], 'name' => $_sku_value['name']));

                }
            }
        }

        $result = (new ProductSkuLogic())->queryNoPaging(array('product_id' => $product['id']));
        if (!$result['status']) {
            $this->error($result['info']);
        }

        $formatSku = array();
        foreach ($result['info'] as &$vo) {
            $formatSku[$vo['sku_id']] = array('id' => $vo['id'], 'icon_url' => getImgUrl($vo['icon_url']), 'ori_price' => $vo['ori_price'], 'price' => $vo['price'], 'product_code' => $vo['product_code'], 'product_id' => $vo['product_id'], 'quantity' => $vo['quantity'],);
            $formatSku[$vo['sku_id']]['sku_desc'] = $this->getSkuDesc($vo['sku_id'], $sku_arr);
        }

        return array('sku_list' => $formatSku, 'sku_arr' => $sku_arr);
    }

    private function getSkuDesc($sku_id, $sku_arr)
    {
        $sku_list = explode(";", $sku_id);
        $sku_desc = "";
        foreach ($sku_list as $sku_item) {
            $sku_value = explode(":", $sku_item);
            $flag = false;
            foreach ($sku_arr as $key => $sku) {
                if ($key == $sku_value[0]) {
                    $sku_desc .= $sku['sku_name'];
                    $flag = true;

                    foreach ($sku['sku_value_list'] as $sku_value_item) {
                        if ($sku_value_item['id'] == $sku_value[1]) {
                            $sku_desc .= ':' . $sku_value_item['name'];
                        }
                    }
                }
            }
            if ($flag) {
                $sku_desc .= ';';
            }
        }

        return $sku_desc;
    }

    public function view()
    {
        $result = (new UserPictureLogic())->getInfo(array('id' => $this->_param('imgId')));
        $this->assign('img', $result['info']['path']);
        return $this->boye_display();
    }

    /**
     * 商品咨询
     */
    public function faq()
    {
        $startdatetime = $this->_param('startdatetime', date('Y/m/d H:i', time() - 24 * 3600 * 30));
        $enddatetime = $this->_param('enddatetime', date('Y/m/d H:i', time()));

        $startdatetime = urldecode($startdatetime);
        $enddatetime = urldecode($enddatetime);

        $pid = $this->_param('pid', -1);
        $tobereply = $this->_param('tobereply');

        //分页时带参数get参数
        $params = array(
            'startdatetime' => $startdatetime,
            'enddatetime' => $enddatetime
        );

        $map = array();

        if ($pid != -1) {
            $map['pid'] = $pid;
            $params['pid'] = $pid;
        }

        if ($tobereply == 'yes') {
            $map['reply_time'] = 0;
        }

        $startdatetime = strtotime($startdatetime);
        $enddatetime = strtotime($enddatetime);

        if ($startdatetime === FALSE || $enddatetime === FALSE) {
            $this->error(L('ERR_DATE_INVALID'));
        }

        $map['ask_time'] = array(array('EGT', $startdatetime), array('elt', $enddatetime), 'and');

        $page = array('curpage' => $this->_param('p', 0), 'size' => 15);
        $order = "ask_time desc";
        //
        $result = (new ProductFaqLogic())->query($map, $page, $order, $params);
        //
        if ($result['status']) {
            $ProductFaq = $result['info'];

            //查询商品名称
            foreach ($ProductFaq['list'] as &$value) {
                $result = (new ProductLogic())->getInfo(['id' => $value['pid']]);
                if ($result['status']) {
                    $value['product_name'] = $result['info']['name'];
                } else {
                    $this->error('异常错误');
                }
            }

            $this->assign('startdatetime', $startdatetime);
            $this->assign('enddatetime', $enddatetime);
            $this->assign('show', $ProductFaq['show']);
            $this->assign('list', $ProductFaq['list']);


            return $this->boye_display();
        } else {
            LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
            $this->error(L('UNKNOWN_ERR'));
        }
    }

    /**
     * 删除FAQ
     */
    public function deleteFaq()
    {

        $map = array('id' => $this->_param('id', -1));
        $result = (new ProductFaqLogic())->delete($map);

        if ($result['status']) {
            $this->success(L('RESULT_SUCCESS'));
        } else {
            $this->error($result['info']);
        }

    }

    /**
     * 批量删除FAQ
     */
    public function bulkDeleteFaq()
    {
        $ids = $this->_param('ids', -1);
        if ($ids === -1) {
            $this->error(L('ERR_PARAMETERS'));
        }
        $ids = implode(',', $ids);
        $map = array('id' => array('in', $ids));

        $result = (new ProductFaqLogic())->delete($map);

        if ($result['status']) {
            $this->success(L('RESULT_SUCCESS'));
        } else {
            $this->error($result['info']);
        }

    }

    /**
     * 回复FAQ
     */
    public function replyFaq()
    {
        if (IS_POST) {

            $id = $this->_param('id', -1);
            if ($id == -1) {
                $this->error('参数错误！');
            }
            $map = array(
                'id' => $id,
            );
            $entity = array(
                'reply_content' => $this->_param('reply_content', ''),
                'reply_uid' => UID,
                'reply_username' => '虎头奔',
                'reply_time' => NOW_TIME
            );
            $result = (new ProductFaqLogic())->save($map, $entity);
            if ($result['status']) {
                $this->success('保存成功！');
            } else {
                $this->error($result['info']);
            }


        } else {

            $id = $this->_param('id', -1);
            if ($id == -1) {
                $this->error('参数错误！');
            }
            $map = array(
                'id' => $id
            );
            $result = (new ProductFaqLogic())->getInfo($map);

            if ($result['status']) {

                $faq = $result['info'];
                $this->assign('faq', $faq);

            } else {
                $this->error($result['info']);
            }

            return $this->boye_display();

        }


    }

    /**
     * 将产品信息保存到数据库
     * @param $store_id
     * @param $product
     * @return mixed
     */
    private function addToProduct($store_id, $product)
    {

        $product_base = $product['product_base'];
        $attr_ext = $product['attr_ext'];
        $group = $product['group'];

        $entity = array(
            'uid' => UID,
            'store_id' => $store_id,
            'template_id' => '',
            'onshelf' => '0',
            'status' => 1,
            'min_buy_cnt' => $attr_ext['min_buy_cnt'],
            'can_mixed_batch' => $attr_ext['can_mixed_batch'],
            'has_sample' => $attr_ext['has_sample'],
            'consignment_time' => $attr_ext['consignment_time'],
            'contact_name' => $attr_ext['contact_name'],
            'contact_way' => $attr_ext['contact_way'],
            'expire_time' => $attr_ext['expire_time'],
            'secondary_headlines' => $attr_ext['secondary_headlines'],
            'has_receipt' => $attr_ext['has_receipt'],
            'under_guaranty' => $attr_ext['under_guaranty'],
            'support_replace' => $attr_ext['support_replace'],
            'total_sales' => $attr_ext['total_sales'],
            'buy_limit' => $attr_ext['buy_limit'],
            'img' => $product['img'],
            'group' => $group,
        );

        $entity = array_merge($entity, $product_base);

        $result = (new ProductLogic())->add($entity);
        return $result;
    }

    /**
     * 将颜色SKU 放在最前面
     */
    private function color2First($skulist)
    {
        $colorIndex = 0;
        for ($i = 0; $i < count($skulist); $i++) {
            if ($skulist[$i]->name == "颜色") {
                $colorIndex = $i;
                break;
            }
        }

        if ($colorIndex > 0) {
            $temp = $skulist[0];
            $skulist[0] = $skulist[$colorIndex];
            $skulist[$colorIndex] = $temp;
        }
        return $skulist;
    }

}