<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\admin\controller;

use app\src\goods\logic\SkuvalueLogic;
use app\src\goods\model\Skuvalue;
use think\Log;
class CategorySkuvalue extends Admin{

	protected $level;
	protected $parent;
	protected $preparent;
	protected $ban_sku_ids = [221]; //禁止操作的规格id列表

	protected function _initialize(){
		parent::_initialize();
		$this->level = $this->_param('level',0);
		$this->parent = $this->_param('parent',0);
		$this->preparent = $this->_param('preparent',0);

		$this->assign("level",$this->level );
		$this->assign("parent",$this->parent );
		$this->assign("preparent",$this->preparent );
	}

	public function index(){

		$cate_id = $this->_param('cate_id',-1);
		$sku_id = $this->_param('sku_id',-1);
		$map = array(
			// 'cate_id'=>$cate_id,
			'sku_id'=>$sku_id
		);
		$name = $this->_param('name','');
		$params = array(
			// 'cate_id'=>$cate_id,
			'sku_id'=>$sku_id
		);

		$page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));

		$order = "id asc ";
		//
		$result = (new SkuvalueLogic())->queryWithPagingHtml($map,$page,$order,$params);
		//
		if($result['status']){
			$this->assign('sku_id',$sku_id);
			$this->assign('cate_id',$cate_id);
			$this->assign('show',$result['info']['show']);
			$this->assign('list',$result['info']['list']);
			return $this->boye_display();
		}else{
			Log::record('INFO:'.$result['info'],'[FILE] '.__FILE__.' [LINE] '.__LINE__);
			$this->error(L('UNKNOWN_ERR'));
		}
	}

	public function add(){
		$cate_id = $this->_param('cate_id',-1);
		$sku_id  = $this->_param('sku_id',-1);

		if(IS_GET){ //view
			$this->assign('sku_id',$sku_id);
			$this->assign('cate_id',$cate_id);

			return $this->boye_display();
		}else{ //save
			$this->checkSkuId($sku_id);

			$name = $this->_param('name','');
			$dnredirect = $this->_param('dnredirect',false);

			if(empty($name)){
				$this->error("属性不能为空！");
			}

			$entity = array(
				'name'=>$name,
				'sku_id'=>$sku_id
			);

			// $result = apiCall(SkuvalueApi::ADD,array($entity));
			$result = (new SkuvalueLogic())->add($entity);

			if($result['status']){
				if($dnredirect){
					$this->success("添加成功！");
				}else{
					$this->success("添加成功！",url('Admin/CategorySkuvalue/index',array('sku_id'=>$sku_id,'cate_id'=>$cate_id,'preparent'=>$this->preparent,'parent'=>$this->parent,'level'=>$this->level)));
				}
			}
			else{
				$this->error($result['info']);
			}

		}
	}

	// 不允许操作特殊
	private function checkSkuId($sku_id=0){
		in_array(intval($sku_id),$this->ban_sku_ids) && $this->error('禁止手动操作特殊规格:'.$sku_id);
	}
	//修改规格值 -
	public function edit(){
		$cate_id = $this->_param('cate_id',-1);
		$sku_id  = intval($this->_param('sku_id',-1));
		$id = $this->_param('id','');

		if(IS_GET){
			// $result = apiCall(SkuvalueApi::GET_INFO,array(array('id'=>$id)));
			$result = (new SkuvalueLogic())->getInfo(['id'=>$id]);
			if($result['status']){
				$this->assign("vo",$result['info']);
			}
			$this->assign('sku_id',$sku_id);
			$this->assign('cate_id',$cate_id);

			return $this->boye_display();
		}else{
			$this->checkSkuId($sku_id);

			$name = $this->_param('name','');
			empty($name) && $this->error("属性不能为空！");
			$entity = [
				'name'=>$name,
			];
			$r = (new SkuvalueLogic())->saveByID($id,$entity);
			!$r['status'] && $this->error($r['info']);
			$this->success("保存成功！",url('Admin/CategorySkuvalue/index',array('sku_id'=>$sku_id,'cate_id'=>$cate_id,'preparent'=>$this->preparent,'parent'=>$this->parent,'level'=>$this->level)));

		}
	}

	//删除规格值 - 不允许操作特殊
	public function delete(){
		$id = $this->_param('id',0);
		$l = new SkuvalueLogic;
		$r = $l->getField(['id'=>$id],'sku_id');
		$this->checkSkuId($r);

		$r = (new SkuvalueLogic())->delete(['id'=>$id]);
		!$r['status'] && $this->error($r['info']);
		$this->success("删除成功！");

	}


}
