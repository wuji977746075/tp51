<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\src\config\logic;
use app\src\base\logic\BaseLogic;
use app\src\config\model\Config;

class ConfigLogic extends BaseLogic{

    const QUERY_NO_PAGING = "system/Config/queryNoPaging";
    const ADD             = "system/Config/add";
    const SAVE            = "system/Config/save";
    const SAVE_BY_ID      = "system/Config/saveByID";
    const DELETE          = "system/Config/delete";
    const QUERY           = "system/Config/query";
    const GET_INFO        = "system/Config/getInfo";
    const SET             = "system/Config/set";

	protected function _init(){
		$this->model = new Config();
	}

	/**
	 * 设置
	 */
	public function set($config){
		$result = $this->model->set($config);

		if($result === false){
			return $this->apiReturnErr($this->model->getError());
		}else{
			return $this->apiReturnSuc($result);
		}
	}

}
