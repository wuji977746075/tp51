<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\src\config\model;

use think\Model;

class Config extends Model {
	protected $table = 'common_config';

	//http://www.kancloud.cn/manual/thinkphp5/129320 验证规则
	/*$rules = [
	'name'  => 'require|max:25',
	'age'   => 'number|between:1,120',
	];*/

	protected $validate = [
		'rule' => [
			'title' => 'require',
			'name' => 'require'
		]
	];

	protected $insert = ['status'=>1, 'create_time'=>NOW_TIME, 'update_time'=>NOW_TIME];
	protected $update = ['update_time' => NOW_TIME];

	/**
	 * 设置
	 * @return true 设置成功 false 参数不正确
	 */
	public function set($config) {
		$effects = 0;
		if ($config && is_array($config)) {
			foreach ($config as $name => $value) {
				$map = ['name' => $name];
				$result = $this -> where($map) -> setField('value', $value);
				if(false !== $result) $effects = $effects + $result;
			}
			if(0 === $effects) return false;
			return $effects;
		}
		return false;
	}

}
