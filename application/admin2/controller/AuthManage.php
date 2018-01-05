<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\admin\controller;

use app\src\menu\logic\MenuLogic;
use app\src\powersystem\logic\AuthGroupLogic;
use app\src\powersystem\logic\AuthRuleLogic;
use app\src\user\logic\MemberLogic;
use app\src\system\logic\ProvinceLogic;

class AuthManage extends Admin {

	protected function _initialize() {
		parent::_initialize();
	}

	/**
	 * API访问授权	 *
	 */
	public function apiaccess() {

		$map = array();
		$map_access = array();
		$groupid = $this->_param('groupid', -1);
		//模块标识
		$modulename = $this->_param('modulename', '');

		$map['status'] = array('egt', 0);
		if ($groupid != -1) {
			$map['group_id'] = $groupid;
		}

		$map_access['status'] = 1;
		if (!empty($modulename)) {
			$map_access['module'] = $modulename;
		}

		$memberMap = array();

		//用户组
//		$result = apiCall(AuthGroupApi::QUERY_NO_PAGING, array($map));
		$result = (new AuthGroupLogic())->queryNoPaging($map);

		//模块
//		$modules = apiCall(AuthRuleApi::ALL_MODULES,array());
		$modules = (new AuthRuleLogic())->allModules();


		//访问权限节点
//		$authRules = apiCall(AuthRuleApi::QUERY_NO_PAGING, array($map_access, 'name asc', 'id,module,name,title'));
		$authRules = (new AuthRuleLogic())->queryNoPaging($map_access, 'name asc', 'id,module,name,title');

		if($modules['status']){
			$this->assign("modulename",$modulename);
			$this->assign("modules",$modules['info']);
		}else{
			$this -> error("所属模块数据获取失败！");
		}

		if($authRules['status']){
			$this->assign("authrules",$authRules['info']);
		}else{
			$this -> error("规则数据获取失败！");
		}
		if ($result['status']) {
			if ($groupid === -1 && count($result['info']) > 0) {
				//默认第一个用户组
				$groupid = $result['info'][0]['id'];
				$rules = $result['info'][0]['rules'];
			} else {
				$rules = $this -> getRules($result['info'], $groupid);
			}
			//当前用户组拥有的规则
			$this -> assign("rules", $rules);
			//当前用户组
			$this -> assign("groupid", $groupid);
			$this -> assign("groups", $result['info']);

			return  $this -> boye_display();;
		} else {
			$this -> error($result['info']);
		}
	}

	/**
	 * 成员授权
	 *
	 */
	public function user() {
		$map = [];
    $param = [];
		$groupid = $this->_param('groupid', -1);
		$map['status'] = ['egt', 0];
		if ($groupid != -1) {
			$map['group_id'] = $groupid;
			$param['groupid'] = $groupid;
		}
		$memberMap = [];


    // 查询全部省
    // $r = $this->getArea(1,'country',false);
    $r = (new ProvinceLogic)->queryNoPaging(['countryid'=>1]);
    $this->exitIfError($r);
    $this->assign('provs',$r['info']);
		//用户组
		$r = (new AuthGroupLogic)->queryNoPaging([]);
		!$r['status'] && $this -> error($r['info']);
		if ($groupid === -1) {
			$groupid = $r['info'][0]['id'];
		}

		$this -> assign("groupid", $groupid);
		$this -> assign("groups", $r['info']);
		$memberMap['status'] = ['egt', 0];
		$memberMap['group_id'] = $groupid;
		//查询用户信息
		$r = (new MemberLogic)->queryByGroup($memberMap, ['curpage' => $this->_param('p', 0), 'size' => 10],$param);
		if ($r['status']) {
			$this -> assign("show", $r['info']['show']);
			$this -> assign("list", $r['info']['list']);
		}
		return  $this -> boye_display();;
	}

	/**
	 * 访问授权	 *
	 */
	public function access() {
		$map = [];
		$groupid = $this->_param('groupid', -1);

		$map['status'] = ['egt', 0];
		if ($groupid != -1) $map['id'] = $groupid;
    $result = (new AuthGroupLogic)->queryNoPaging($map);

		$map_access = [];
		//模块标识
		$modulename = $this->_param('modulename', '');
		$map_access['status'] = 1;
		if (!empty($modulename)) $map_access['module'] = $modulename;

		//菜单列表
		$menulist = (new MenuLogic)->queryNoPaging($map_access, 'pid asc', 'id,title,url,pid,group');

		!$menulist['status'] && $this -> error('获取数据失败！');
		$menus = "";
		//获取当前用户组的菜单列表
		if (!isset($map['id'])) $map['id'] = 1;
		$menus = (new AuthGroupLogic)->getInfo($map);
		if ($menus['status']) $menus = $menus['info']['menulist'];
		$tree = list_to_tree($menulist['info']);
		$tree = $this -> createTree($tree, $menus);
		$this -> assign("tree", $tree['child']);

		!$result['status'] && $this -> error($result['info']);
		if ($groupid === -1 && count($result['info']) > 0) {
			//默认第一个用户组
			$groupid = $result['info'][0]['id'];
			$rules = $result['info'][0]['rules'];
		} else {
			$rules = $this -> getRules($result['info'], $groupid);
		}
		//当前用户组拥有的规则
		$this -> assign("rules", $rules);
		//当前用户组
		$this -> assign("groupid", $groupid);
		$this -> assign("groups", $result['info']);
		return  $this -> boye_display();;
	}

	/**
	 * 根据用户组id，获取用户组对应规则
	 * @param $groups 用户组列表
	 * @param $groupid 用户组id
	 * @return string 用户组规则
	 */
	private function getRules($groups, $groupid) {

		foreach ($groups as $vo) {
			if ($vo['id'] == $groupid) {
				return $vo['rules'];
			}
		}
	}

    /**
	 * 根据菜单列表生成树形数据
	 * @param $tree 所有的菜单数据
	 * @param $menus 当前用户组的所对应的菜单，字符串
	 */
	private function createTree($tree, $menus) {
		$allchecked	= true;//所有子菜单都选中了
		$ul = "<ul>";
		foreach ($tree as $vo) {
			$ul .= "<li ";
			$childUL['allchecked'] = true;
			if (isset($vo['_child'])) {
				$childUL = $this -> createTree($vo['_child'],$menus);
			}

			if (strpos($menus, $vo['id'] . ",") === false || !$childUL['allchecked']	) {
				$allchecked = false;
				$selected = 'false';
			} else {

				$selected = 'true';
			}

			if ($vo['pid'] == 0) {
				$jstree = '{ "selected" : ' . $selected . ', "opened" :  true}';
			} else {
				$jstree = '{ "selected" : ' . $selected . ', "opened" : false }';
			}
			$ul .= "data-jstree='".$jstree."' id=\"jstree_" . $vo['id'] . "\" >" . $vo['title'];

            if (isset($childUL['child'])) {
                $ul .= $childUL['child'];
            }
			$ul .= "</li>";
		}

		$ul .= "</ul>";
		return array('child'=>$ul,'allchecked'=>$allchecked);
	}



	//$VO必是有Admin/Index/index 的字符串
	private function getCTRLName($vo) {
		$temp = explode('/', $vo);
		if (count($temp) == 3) {
			return $temp[0] . '_' . $temp[1];
		} else {
			return "errURL";
		}
	}

}
