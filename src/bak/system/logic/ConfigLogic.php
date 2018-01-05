<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\src\system\logic;

use app\src\base\logic\BaseLogic;
use app\src\system\model\Config;

class ConfigLogic extends BaseLogic{

	protected function _init(){
		$this->setModel(new Config());
	}

    public function getCacheConfig($pa='',$time=180){
        $map = [];
        is_numeric($pa) && $map = ['id'=>$pa];
        is_string($pa)  && $map = ['name'=>$pa];
        is_array($pa)   && $map = $pa;
        if(!$map) return returnErr('内部参数错误');
        $key = getCacheKey($map,'config');
        if($time){
            $v = cache($key);
            if($v) return returnSuc($v);
        }
        $r = $this->getInfo($map);
        if(!$r['status']) return $r;
        if(!$r['info']) return returnErr('未找到配置');
        $info = $r['info'];
        if($time && $info){
            $v = cache($key,$info['value'],$time);
        }
        return isset($info['value']) ? returnSuc($info['value']) : returnErr('数据异常');
    }
	/**
	 * 设置
	 * @param $config
	 * @return array
	 */
	public function set($config){
        $effects = 0;
        $result = false;
        if ($config && is_array($config)) {
            $flag = true;
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $result = $this ->getModel() -> where($map) -> setField('value', $value);
                if($result !== false){
                    $effects = $effects + $result;
                }else{
                    $flag = false;
                }
            }
            if($flag !== false){
                $result = $effects;
            }
        }

		if($result === false){
			return $this->apiReturnErr($this->getModel()->getError());
		}else{
			return $this->apiReturnSuc($result);
		}
	}

}
