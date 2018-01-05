<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace app\admin\controller;

use app\src\category\logic\CategoryPropvalueLogic;
use think\Log;

class CategoryPropvalue extends Admin{

	private $cate_id;
	protected function _initialize(){
		parent::_initialize();
		$this->cate_id = $this->_param('cate_id',-1);
		$this->assign("cate_id",$this->cate_id);
	}

	public function index(){
		$prop_id = $this->_param('prop_id',-1);
		$map = array(
			'prop_id'=>$prop_id
		);
		$params = array(			
			'prop_id'=>$prop_id
		);
		$page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
		$order = " id asc ";
		$result = (new CategoryPropvalueLogic())->queryWithPagingHtml($map,$page,$order,$params);
		if($result['status']){
			$this->assign('prop_id',$prop_id);
			$this->assign('show',$result['info']['show']);
			$this->assign('list',$result['info']['list']);
			return $this->boye_display();
		}else{
			Log::record('INFO:'.$result['info'],'[FILE] '.__FILE__.' [LINE] '.__LINE__);
			$this->error($result['info']);
		}
	}

	public function add(){
		$prop_id = $this->_param('prop_id',-1);
		if(IS_GET){
			$this->assign('prop_id',$prop_id);
            return $this->boye_display();
		}else{
			$name = $this->_param('valuename','');
			if(empty($name)){
				$this->error("属性不能为空！");
			}
			$entity = array(
				'prop_id'=>$prop_id,
				'valuename'=>$name,
			);
			$result = (new CategoryPropvalueLogic())->add($entity);
			if($result['status']){
				$this->success("添加成功！",url('Admin/CategoryPropvalue/index',array('prop_id'=>$prop_id,'cate_id'=>$this->cate_id)));
			}
			else{
				$this->error($result['info']);
			}
		}
	}
	
	public function edit(){
		$prop_id = $this->_param('prop_id');
		$cate_id = $this->_param('cate_id');
		$valueid = $this->_param('valueid');
		if(IS_GET){
			$result = (new CategoryPropvalueLogic())->getInfo(['id'=>$valueid ]);
			if($result['status']){
				$this->assign("vo",$result['info']);
			}
			$this->assign('prop_id',$prop_id);
			$this->assign('cate_id',$cate_id);
			$this->assign('valueid',$valueid);
            return $this->boye_display();
		}else{
            $valueid = $this->_param('valueid');
            $name = $this->_param('valuename','');
			if(empty($name)){
				$this->error("属性不能为空！");
			}
			$entity = array(
				'valuename'=>$name,
			);
			$result = (new CategoryPropvalueLogic())->saveByID($valueid,$entity);
			if($result['status']){
				$this->success("保存成功！",url('CategoryPropvalue/index',array('prop_id'=>$prop_id,'cate_id'=>$cate_id)));
			}
			else{
				$this->error($result['info']);
			}
		}
	}

	public function delete(){
		$id = $this->_param('valueid',0);
		$result = (new CategoryPropvalueLogic())->delete(['id'=>$id]);
		if($result['status']){
			$this->success("删除成功！");
		}else{
			$this->error($result['info']);
		}
	}
}

