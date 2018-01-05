<?php
/**
 * 周舟 hzboye010@163.com
 * addby sublime snippets
 */
namespace app\admin\controller;

use app\src\system\logic\CityLogic;
use app\src\system\logic\AreaLogic;
use app\src\system\logic\ProvinceLogic;
use app\src\ewt\logic\SchoolLogicV2;

class School extends Admin{
	//学校管理
	public function index(){
		$map = [];
		//省份
		$r = $this->getArea(1,'country',false);
		$this->exitIfError($r);
		$this->assign('provinces',$r['info']);
		//已选省份
		$province = $this->_param('province','');
		$this->assign('province',$province);
		$city     = $this->_param('city','');
		$area     = $this->_param('area','');
		if($area){
			$code = $area;
			$map['area_code'] = $code;
		}else{
			if($city){
				$code = substr($city, 0,4);
				$map['area_code'] = ['like',$code.'%'];
			}else{
				$code = $province ? substr($province, 0,2) : '';
				$province && $map['area_code'] = ['like',$code.'%'];;
			}
		}
		$this->assign('code',$code);

		$kword = $this->_param('kword','');
		$this->assign('kword',$kword);
		if($kword) $map['zone_name'] = ['like','%'.$kword.'%'];
		//获取已加学校
		$page = [
			'curpage'=>$this->_param('p',1),
			'size'   =>config('LIST_ROWS'),
		];
		$param = $code ? ['code'=>$code] : [];
		$r = (new SchoolLogicV2())->queryWithPagingHtml($map,$page,'id desc',$param);
		$this->assign('list',$r['list']);
		$this->assign('show',$r['show']);
		return $this->show();
	}
	//add school
	public function add(){
		$l = new SchoolLogicV2;
		if(IS_GET){
			//省份
			$r = $this->getArea(1,'country',false);
			$this->exitIfError($r);
			$this->assign('provinces',$r['info']);
			return $this->show();
		}elseif(IS_AJAX){
			$map = [];
			$map['zone_name']  = trim($this->_post('zone_name','','缺少学校名'));
			$map['alias_name'] = trim($this->_post('alias_name',''));
			$map['address']    = trim($this->_post('address','缺少学校地址'));
			$map['area_code']  = trim($this->_post('area','','请选择所属区'));
			$map['lng']        = floatval($this->_post('lng',0,'缺少经度'));
			$map['lat']        = floatval($this->_post('lat',0,'缺少纬度'));
			$this->checkMapPos($map['lng'],$map['lat']);
			// $this->exitIfError($r);
			//,U('Admin/House/schoolzone')
			if($l->add($map)) $this->success('添加成功');
			else $this->error('添加失败');
		}
	}

	//edit school
	public function edit($id){
		//检查ID
		$l = new SchoolLogicV2;
		$r = $l->getInfo(['id'=>$id]);
		if(empty($r)) $this->error('学校id错误');
		if(IS_GET){
			$this->assign('id',$id);
			//area_name
			$area_code = $r['area_code'];
			$r['prov_name'] = $this->getProvinceName($area_code);
			$r['city_name'] = $this->getCityName($area_code);
			$r['area_name'] = $this->getAreaName($area_code);
			$this->assign('entry',$r);
			//省份
			$r = $this->getArea(1,'country',false);
			$this->exitIfError($r);
			$this->assign('provinces',$r['info']);

			//获取图片
			// $r = apiCall(SchoolZoneApi::GET_IMGS,array(array('school_zone_id'=>$id)));
			// $this->exitIfError($r);
			// $imgs = $r['info'];
			// $this->assign('main_img',$imgs['main_img']);
			// $this->assign('show_imgs',$imgs['show_imgs']);
			return $this->show();
		}elseif(IS_AJAX){
			$map = [];
			$map['zone_name']  = trim($this->_post('zone_name','','缺少学校名'));
			$map['alias_name'] = trim($this->_post('alias_name',''));
			$map['address']    = trim($this->_post('address','缺少学校地址'));
			$map['area_code']  = trim($this->_post('area','','请选择所属区'));
			$map['lng'] = floatval($this->_post('lng',0,'缺少经度'));
			$map['lat'] = floatval($this->_post('lat',0,'缺少纬度'));
			$this->checkMapPos($map['lng'],$map['lat']);

			// $main_img = intval($this->_post('main_img',''));
			// $imgs     = explode(',', trim(I('post.img','')));
			// if(count($imgs)) array_pop($imgs);
			// $img      = array('main_img'=>$main_img,'img_list'=>$imgs);
			//添加 schoolzone 和 图片
			// $this->exitIfError($r);
			if($l->save(['id'=>$id],$map)) $this->success('修改成功',url('admin/school/index'));
			else $this->error('修改失败');
		}
	}
	// delete school
	public function del($id){
		//删除 school_zone 和 学校图片
		if((new SchoolLogicV2())->delete(['id'=>$id])) $this->success('操作成功');
		else $this->error('操作失败');
	}


//ajax - getChildArea
	public function getArea($aid='',$type='',$ajax=true){
		if($aid){
			if($type == 'city'){ //查询市区
				$r = (new AreaLogic())->queryNoPaging(['father'=>$aid]);
				echo json_encode($r);exit;
			}elseif($type == 'province'){ //查询城市
				$r = (new CityLogic())->queryNoPaging(['father'=>$aid]);
				echo json_encode($r);exit;
			}elseif($type == 'country'){ //查询身份
				$r = (new ProvinceLogic())->queryNoPaging(['countryid'=>$aid]);
				if($ajax) {
					echo json_encode($r);exit;
				}else{
					return $r;
				}
			}
		}
		echo json_encode(returnErr('system - error'));exit;
	}

/*private*/
	protected function checkMapPos($lng,$lat){
		// return true;
	}
	private function getCityName($code=''){
		$code = substr($code, 0,4).'00';
		$r = (new CityLogic())->getInfo(['cityID'=>$code],false,'city');
		$this->exitIfError($r);
		$this->exitIfEmpty($r,'对应的城市['.$code.']不存在');
		return $r['info']['city'];
	}
	private function getAreaName($code=''){
		$r = (new AreaLogic())->getInfo(['areaID'=>$code],false,'area');
		$this->exitIfError($r);
		$this->exitIfEmpty($r,'对应的市区['.$code.']不存在');
		return $r['info']['area'];
	}
	private function getProvinceName($code){
		$code = substr($code,0,2).'0000';
		$r = (new ProvinceLogic())->getInfo(['provinceID'=>$code],false,'province');
		$this->exitIfError($r);
		$this->exitIfEmpty($r,'对应的社区['.$code.']不存在');
		return $r['info']['province'];
	}
	//获取经纬度大约直线距离
	public function getHowFar($lnt=0,$lat=0){
		$r = intval(sqrt(pow(100000*abs($lng),2)+pow(110000*abs($lat),2)));
		if($r<50) $r="极近";
		else $r.='米';
		return $r;
	}

	protected function arrayByKey($r=array(),$k='id'){
		$l = [];
		foreach ($r as $v) {
			isset($v[$k]) ? $l[$v[$k]]=$v : $l[]=$k;
		}
		return $l;
	}

	//area zone data check
	// protected function checkData($zone,$name,$type,$area){
	// 	//检查
	// 	if(!preg_match('/^[A-Za-z0-9]{2}$/', $zone) || $zone=='00') $this->error('编号格式错误');
	// 	$zone_code = $area.$zone;
	// 	//是否唯一
	// 	$r = apiCall(AreaZoneApi::GET_INFO,array(array('zone_code'=>$zone_code)));
	// 	$this->exitIfError($r);
	// 	$this->exitIfNotEmpty($r,'区域code已存在');
	// 	$r = apiCall(AreaZoneApi::GET_INFO,array(array('area_code'=>$area,'zone_type'=>$type,'zone_name'=>$name)));
	// 	$this->exitIfError($r);
	// 	$this->exitIfNotEmpty($r,'此类区域下已有同名');
	// }
}