<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 17:57
 */

namespace app\admin\controller;


use app\src\category\logic\CategoryLogic;
use app\src\goods\logic\ProductGroupLogic;
use app\src\goods\logic\ProductLogic;
use app\src\system\logic\DatatreeLogicV2;

class ProductGroup extends Admin
{
    private $cate;
    private $store_id;//todo : +多店铺支持

    //todo : +多店铺支持
    public function index(){
        $id = $this->_param('id',0); //分组id
        if(IS_GET){
            $this->assign('id',$id);

            $cate = $this->_param('cate',0);//类目
            $this -> assign('cate',$cate);

            $store_id = $this->_param('store_id',0);
            $this->assign("store_id",$store_id);
            $page_no  = $this->_param('p',1);
            $this -> cate = [];
            $cate && $this->queryChild($cate);

            $r = (new DatatreeLogicV2())->getInfo(['id' => $id]);
            if($r){
                $this -> assign('name',$r['name']);
                //不管理 微信分组
                // $parent = getDatatree('WXPRODUCTGROUP');
                // if($r['parentid'] != $parent){
                //     $this -> error("分类id参数错误！");
                // }
            }else{
                $this -> error($r);
            }
            //查询顶级分类信息
            $r = (new CategoryLogic())->queryNoPaging(['level'=>1,'parent'=>0]);
            if($r['status']){
                $this -> assign('cate_parent',$r['info']);
            }else{
                $this -> error($r['info']);
            }

            //查询该分类商品信息
            $map = [
                'g.g_id'    => $id,
                'p.status'  => 1,
                // 'p.onshelf' => 1,
            ];
            $cate && $map['p.cate_id'] = ['in',$this->cate];
            $page  = ['curpage'=>$page_no,'size'=>10];
            $field = "g.id,g.g_id,g.p_id,g.sku_id,g.price,g.start_time,g.end_time,g.display_order,p.onshelf";
            // $field .= ",sku.sku_desc,sku.price as sku_price,sku.icon_url";
            $field .= ",img.img_id as main_img";
            $field .= ",p.name";
            $order = "display_order desc";
            $r = (new ProductGroupLogic())->queryList($map,$page,$order,false,$field);
            !$r['status'] && $this->error($r['info']);

            $this->assign("list",$r['info']['list']);
            $this->assign("show",$r['info']['show']);

            //modal 的第一页 商品列表 - 规格 - 是否选择了
            // $r = $this->getProduct($cate,1,$id);
            // !$r['status'] && $this->error($r['info']);
            // $this->assign('group',$r['info']['list']);
            return $this->boye_display();
        }
    }

    public function ajaxGetProduct($cate=0,$p=1,$group){
        echo json_encode($this->getProduct($cate,$p,$group));
        die();
    }
    //todo : +多店铺支持
    //ajax - list
    private function getProduct($c=0,$p=1,$g){
        $map = [];
        $page_size = 10;
        $p = (int) $p;
        $this -> cate = [];
        if($c){
            $this -> queryChild($c);
            $map['p.cate_id'] = ['in',$this -> cate];
        }
        $map['g.g_id'] = $g;
        $page = ['curpage'=>$p,'size'=>$page_size];
        $r = (new ProductLogic())->queryOnShelfWithGroup($map,$page);
        // $r = (new ProductLogic())->queryOnShelfAllSkuWithGroup($map,$page);
        if($r['status']){
            foreach($r['info']['list'] as &$vo){
                $vo['able']     = !$vo['g_id'];
                $vo['icon_url'] = $vo['main_img'];
                // $vo['icon_url'] = $vo['icon_url'] ? $vo['icon_url'] : $vo['main_img'];
                unset($vo['main_img']);
                unset($vo['g_id']);
            }
            $r['info']['p'] = $p;
            $r['info']['page'] = ceil($r['info']['count']/$page_size);
            return $r;
        }else{
            return $r;
        }

    }

    /**
     * 废弃
     * @param $group
     * @return mixed
     */
    private function getProductName($group){
        $arr = $group;
        foreach($group as $k=>$vo){
            $id = $vo['p_id'];
            $result = (new ProductLogic())->getInfo(['id' => $id]);
            if(!$result['status']){
                $this -> error($result['info']);
            }

            $arr[$k]['p_name'] = $result['info']['name'];
        }
        return $arr;
    }

    //edit
    public function edit(){
        if(IS_POST){
            $p_id   = (int)$this->_param('p_id',0);
            $sku_id = (int)$this->_param('sku_id',0);
            $g_id   = (int)$this->_param('g_id',0);
            $price  = intval($this->_param('price_'.$p_id,0)*100);
            // if($price <1) $this->error('价格过低');

            $start_time = $this->_param('start_time_'.$p_id,'');
            $start_time = strtotime($start_time);
            $end_time = $this->_param('end_time_'.$p_id,'');
            $end_time = strtotime($end_time);
            $display_order = $this->_param('display_order_'.$p_id,0,'int');
            $entity = [
                'price'         => $price,
                'start_time'    => $start_time,
                'end_time'      => $end_time,
                'display_order' => $display_order
            ];

            $map = [
                'sku_id' =>$sku_id,
                'p_id'   => $p_id,
                'g_id'   => $g_id,
            ];
            $r = (new ProductGroupLogic())->save($map,$entity);
            !$r['status'] && $this -> error($r['info']);
            $this -> success('修改成功！',url('Admin/ProductGroup/index',['id'=>$g_id]));
        }
    }

    public function delete(){
        $id = $this->_param('id',0);
        $result = (new ProductGroupLogic())->delete(['id'=>$id]);
        if($result['status']){
            $this -> success('删除成功！');
        }else{
            $this -> error($result['info']);
        }
    }

    public function add(){
        if(IS_POST){
            $g_id   = $this->_param('g_id',0);
            $p_id   = $this->_param('new_p_id',0);
            $sku_id = $this->_param('new_sku_id',0);
            $start  = $this->_param('new_start_time','');
            $end    = $this->_param('new_end_time','');
            $price  = intval($this->_param('new_price',0)*100);
            $order  = intval($this->_param('new_display_order',0));

            !$start && $this->error('需要开始时间');
            !$end   && $this->error('需要结束时间');
            $start = strtotime($start);
            $end   = strtotime($end);
            ($start >= $end || $end<=time()) && $this->error('结束时间非法');
            $entity = [
                'g_id'          =>$g_id,
                'p_id'          =>$p_id,
                'sku_id'        =>$sku_id,
                'start_time'    =>$start,
                'end_time'      =>$end,
                'price'         =>$price,
                'display_order' =>$order,
            ];
            $r = (new ProductGroupLogic())->add($entity);
            !$r['status'] && $this->error($r['info']);
            $this->success('操作成功！');
        }else{
            $this->error('非法请求');
        }
    }

    /**
     * 循环版本
     * todo : +多店铺支持
     * @param $id
     * @return array
     */
    private function queryChild($id){
        $que = [];
        array_push($que,$id);
        while(count($que)){
            $tmp = array_shift($que);
            $map = [
                'parent' => $tmp,
            ];
            array_push($this -> cate , $tmp ); //查全合适分类
            $r = (new CategoryLogic())->queryNoPaging($map);
            if($r['status']){
                if(is_array($r['info'])){
                    foreach($r['info'] as $val){
                        array_push($que,$val['id']); //继续查下级
                    }
                }else{
                    // array_push($this -> cate , $tmp ); //只查了最里层分类
                }
            }else{
                return $r;
            }
        }
        return returnSuc($this -> cate);
    }

}