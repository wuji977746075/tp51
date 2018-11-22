<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\src\system\model;

use think\Model;

class Config extends Model {

	protected $table = "common_config";

//	protected $validate = [
//		'rule' => [
//			'title' => 'require',
//			'name' => 'require'
//		]
//	];

	protected $insert = ['status'=>1, 'create_time'=>NOW_TIME, 'update_time'=>NOW_TIME];
	protected $update = ['update_time' => NOW_TIME];

}
