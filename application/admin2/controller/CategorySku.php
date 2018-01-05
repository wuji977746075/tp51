<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\admin\controller;

use app\src\category\logic\CategoryLogic;
use app\src\goods\logic\SkuLogic;
use app\src\goods\logic\SkuvalueLogic;
use think\Log;

class CategorySku extends Admin{

	protected $level;
	protected $parent;
	protected $preparent;

	public function index(){
		$name = $this->_param('name',''); //search word
    $cate_id = $this->_param('cate_id',0);
    $this->assign('cate_id',$cate_id);

    $r = (new CategoryLogic())->getInfo(["id" => $cate_id]);
    !$r['status'] && $this->error($r['info']);
    $info = $r['info'];
    $cate_name = $info ? $info['name'] : '';
    $parent = $info ? $info['parent']  : 0;
    $root   = $info ? $info['root_id'] : 0;
    $this->assign('parent',$parent);
    $this->assign('root_id',$root);
    $this->assign('cate_name',$cate_name);

    $page = ['curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS')];
    $map  = ['cate_id'=>['in',array_unique([0,$cate_id,$parent,$root])]];
    $params = ['cate_id'=>$cate_id];
    $r = (new SkuLogic())->queryWithPagingHtml($map,$page,'id asc',$params);
    if($r['status']){
			$this->assign('show',$r['info']['show']);
			$this->assign('list',$r['info']['list']);
			return $this->boye_display();
		}else{
			Log::record('INFO:'.$r['info'],'[FILE] '.__FILE__.' [LINE] '.__LINE__);
			$this->error(L('UNKNOWN_ERR'));
		}
	}

	public function add(){
		$cate_id = $this->_param('cate_id',-1);
		if(IS_GET){ //view
      $this->assign('cate_id',$cate_id);
      return $this->boye_display();
		}else{ //save
			$name = $this->_param('name','');
      empty($name) && $this->error("属性不能为空！");
      $entity = [
        'cate_id' =>$cate_id,
        'name'    =>$name,
			];
			// $r = apiCall(SkuApi::ADD,array($entity));
			$r = (new SkuLogic())->add($entity);
      !$r['status'] && $this->error($r['info']);
			$this->success("添加成功！",url('Admin/CategorySku/index',array('cate_id'=>$cate_id,'preparent'=>$this->preparent,'parent'=>$this->parent,'level'=>  $this->level)));
    }
	}

  public function edit(){
		$cate_id = $this->_param('cate_id',-1);
		$id = $this->_param('id','');

    if(IS_GET){ //view
			$r = (new SkuLogic())->getInfo(['id'=>$id]);
			$r['status'] && $this->assign("vo",$r['info']);
			$this->assign('cate_id',$cate_id);

      return $this->boye_display();

		}else{ //save
			$name = $this->_param('name','');
      empty($name) && $this->error("属性不能为空！");

      $entity = [
  			'name'=>$name,
  		];
  		// $r = apiCall(SkuApi::SAVE_BY_ID,array($id,$entity));
  		$r = (new SkuLogic())->saveByID($id,$entity);
      !$r['status'] && $this->error($r['info']);
  		$this->success("保存成功！",url('Admin/CategorySku/index',['cate_id'=>$cate_id,'preparent'=>$this->preparent,'parent'=>$this->parent,'level'=>$this->level]));
    }
  }

	public function delete(){
    $id  = $this->_param('id',0);
		$map = array('sku_id'=>$id);
		// $r = apiCall(SkuvalueApi::QUERY_NO_PAGING,array($map));
		$r = (new SkuvalueLogic())->queryNoPaging($map);
    !$r['status'] && $this->error($r['info']);
    if(count($r['info']) > 0){
       $this->error("存在规格值，请先删除规格值！");
    }
    $r = (new SkuLogic())->delete(['id'=>$id]);
		!$r['status'] && $this->error($r['info']);
		$this->success("删除成功！");
	}

  protected function _initialize(){
    parent::_initialize();
    $this->level = $this->_param('level', 0);
    $this->parent = $this->_param('parent', 0);
    $this->preparent = $this->_param('preparent', 0);

    $this->assign("level", $this->level);
    $this->assign("parent", $this->parent);
    $this->assign("preparent", $this->preparent);
  }
}
