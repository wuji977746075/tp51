<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

namespace  app\admin\controller;


use app\src\system\logic\DatatreeLogicV2;
use app\src\tool\helper\RadixHelper;
use think\Db;

class Datatree extends Admin {

	private $parent;
	private $preparent;

	protected function initialize(){
		parent::initialize();
		$this->parent = $this->_param('parent/d',0);
		$result = (new DatatreeLogicV2)->getInfo(['id'=>$this->parent]);
		$this->preparent = $result ? $result['parentid'] : 0;

		$this->assign('parent',$this->parent);
		$this->assign('preparent',$this->preparent);
	}

	public function index(){
		$name = $this->_param('name','');
		$map  = [];

    $map[] = ['parentid','=',$this->parent];
		$params = ['parent'=>$this->parent];
		if($name){
			$map[] = ['name','like',"%$name%"];
			$params['name'] = $name;
		}

		$order = " sort desc,code desc ";
    $r = (new DatatreeLogicV2)->queryWithPagingHtml($map,$this->pager,$order,$params);
    $this->assign('name',$name);
    $this->assign('show',$r ['show']);
    $this->assign('list',$r ['list']);
    return $this->show();

	}


	private function getDatatreeNextCode(){
        $parent = (new DatatreeLogicV2)->getInfo(['id'=>$this->parent]);

        $r = (new DatatreeLogicV2)->getInfo([['code','like',$parent['code'].'___']],'code desc');

        $code = $parent['code'].'001';
        if(empty($r)){
            return $code;
        }

        $parent_code = $r['code'];
        if(strlen($parent_code) < 3){ $this->error('父级编码错误('.$parent_code.'0');}
        $hex36 = substr($parent_code,strlen($parent_code) - 3,3);
        $num   = RadixHelper::hex36ToNum($hex36) + 1;
//        $num   = 35*36*36 +  35*36 + 35;//46655 同一层级最多数据

        $hex36 = RadixHelper::numTo36Hex($num);


        if(strlen($hex36) < 3){
            $hex36 = str_pad($hex36,3,"0",STR_PAD_LEFT);
        }

        $code = substr($parent_code,0,strlen($parent_code) - 3).$hex36;
        return $code;
    }

	public function add(){
    $r = (new DatatreeLogicV2)->getInfo(['id'=>$this->parent]);
		if(IS_GET){
      $code = $this->getDatatreeNextCode();
      $this->assign('code',$code);
			return $this->show();
		}else{
			$level = 0;
			$parents = $this->parent.',';
			$level = intval($r['level'])+1;
      $parents = $r['parents'].$parents;
      $entity = array(
        'code'       =>$this->_param('code',''),
        'alias'      =>$this->_param('alias',''),
        'name'       =>$this->_param('name',''),
        'notes'      =>$this->_param('notes',''),
        'sort'       =>$this->_param('sort',''),
        'level'      =>$level,
        'parents'    =>$parents,
        'parentid'   =>$this->parent,
        'iconurl'    =>$this->_param('iconurl',''),
        'data_level' =>$this->_param('data_level',0)
			);

			$r = (new DatatreeLogicV2)->add($entity);

			$this->success("操作成功！",url('Datatree/index',['parent'=>$this->parent]));
		}
	}

	public function delete(){
		$id = $this->_param('id',0);

		$r = (new DatatreeLogicV2)->queryNoPaging(['parentid'=>$id]);

		if(is_array($r) && count($r) > 0){
			$this->error("有子级，请先删除所有子级！");
		}
    $r = (new DatatreeLogicV2)->getInfo(['id'=>$id]);

    if(isset($r['data_level'])){
      $data_level = $r['data_level'];
      if($data_level == 1){
        $this->error("系统级数据无法删除！");
      }

      $r = (new DatatreeLogicV2)->delete(['id'=>$id]);
      $this->success("操作成功！");
    }

		$this->error("操作失败！");
	}

	public function edit(){
		$id = $this->_param('id',0);
		if(IS_GET){

			$r = (new DatatreeLogicV2)->getInfo(['id'=>$id]);
      $len = ($r['level']) * 3;
			if(empty($r['code']) || $len != strlen($r['code'])){
        $code = $this->getDatatreeNextCode();
        $r['code'] = $code;
      }

			$this->assign("vo",$r);
			return $this->show();
		}else{

			$entity = [
        'code'       =>$this->_param('code',''),
        'alias'      =>$this->_param('alias',''),
        'name'       =>$this->_param('name',''),
        'notes'      =>$this->_param('notes',''),
        'sort'       =>$this->_param('sort',''),
        'iconurl'    =>$this->_param('iconurl',''),
        'data_level' =>$this->_param('data_level',0)
			];
			$r = (new DatatreeLogicV2)->saveByID($id,$entity);
			$this->success("操作成功！",url('Datatree/index',['parent'=>$this->parent]));
		}
	}

	public function deleteChildren(){

    $id = $this->_param('id',0);
    $r = (new DatatreeLogicV2)->delete(['parentid'=>$id]);
    $this->success("操作成功！",url('Datatree/index',['parent'=>$this->parent]));
  }
}

