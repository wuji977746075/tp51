<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * 周舟 hzboye010@163.com
 * addby sublime snippets
 */

namespace app\admin\controller;


use app\src\system\logic\ProvinceLogic;

class Province extends Admin {

	protected function _initialize(){
		parent::_initialize();
	}

	/**
	 * show province list
	 * @return [type] [description]
	 */
	public function index(){
		$this->assignTitle('省份管理');
		$p = $this->_param('p',0);
		$father = $this->_param('father',0);
        $where = [];
        $param = [];
        if(!empty($father)){
            $where['countryid'] = $father;
            $param['father'] = $father;
        }
        $kword = request()->param('kword','');
        if(!empty($kword)){
            $where['province']   = array('like','%'.$kword.'%');
            $param['kword'] = $kword;
        }

		$r = (new ProvinceLogic())->queryWithPagingHtml($where,array('curpage'=>$p,'size'=>10),'id desc',$param);

		if($r['status']){
            $this->assign('father',$father);
            $this->assign('p',$p);
		    $this->assign("kword",$kword);
			$this->assign('list',$r['info']['list']);
			$this->assign('show',$r['info']['show']);
		}else{
			$this->error('系统错误： Province:query:error ');
		}

		return $this->boye_display("default",'Area/index_p');
	}

	/**
	 * 删除省份 将删除子市、区
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function delete(){
        $id = $this->_param('id',0);
		$r = (new ProvinceLogic())->deleteByID($id);

		if(!$r['status']) $this->error($r['info']);
		else $this->success('修改成功');
	}

	public function add(){
		if(IS_GET){
			return $this->boye_display("default",'Area/add_p');
		}elseif(IS_AJAX){
			$map               = array();
			$map['provinceID'] = $this->_param('provinceID');
			$map['province']   = $this->_param('province');
			$map['countryid']  = 1017;
			$this->upd($map);
		}
	}

	public function edit($id){
        $ret_url = $this->_param('ret_url','');
		$r = (new ProvinceLogic())->getInfo(['id'=>$id]);
		if(!$r['status']) $this->error('ProvinceApi get error');
		if(!$r['info'])   $this->error('省份不存在');

		if(IS_GET){
			$this->assign('entry',$r['info']);
            return $this->boye_display("default",'Area/edit_p');
		}elseif(IS_POST){
			$map               = array();
            $map['provinceID'] = $this->_param('provinceID');
            $map['province']   = $this->_param('province');
			$this->upd($map,$id,$ret_url);
		}
	}


	protected function upd($map,$id=0,$ret_url=''){

//		if(!preg_match('/[\x{4e00}-\x{9fa5}]+/u', $map['province'])) $this->error('名称格式错误');
//		if(!preg_match('/^[A-Za-z0-9]{2}0000$/', $map['provinceID'])) $this->error('编号格式错误');

		$where               = array();
		$where['provinceID'] = $map['provinceID'];
		$r = (new ProvinceLogic())->getInfo($where);
		unset($where);

		if(!$r['status']) $this->error('ProvinceApi get error');

		if(is_array($r['info']) && $r['info']['id'] != $id){
				$this->error('请换一个编号');
		}

		if($id){
			$r = (new ProvinceLogic())->saveByID($id,$map);
			if(!$r['status']) $this->error('修改失败');
			$this->success('修改成功',$ret_url);
		}else{
			$r = (new ProvinceLogic())->add($map);
			if(!$r['status']) $this->error('添加失败');
			$this->success('添加成功',$ret_url);
		}
	}
}