<?php
/**
 * 周舟 hzboye010@163.com
 * addby sublime snippets
 * 基本测试类
 */

namespace app\test\controller;

use think\Controller;
use ApiService;
use DOMDocument;

class Ava extends Controller {
	protected $format; //return-format
	protected $api_ver; //客户端版本号
	// protected $notify_id;
	// protected $alg;

	public function initialize() {
		header("X-AUTHOR:".POWER);
		//封装为front-app
		$config = [
			'client_id'     => CLIENT_ID,
			'client_secret' => CLIENT_SECRET,
			'site_url'      => config("site_url"),
		];
		$this->format = input('format','array');
		$this->assign("format", $this->format);
		$this->assign("access_token", CLIENT_ID);
		//基本参数验证
		$this->api_ver = input('post.api_ver', 100);
		// $this->alg       = 'md5';//I('post.alg','md5');
		// $this->notify_id = time();//I('post.notify_id',time());
		// $this->assign('api_ver',I('post.api_ver',100));
		// $this->assign('alg',I('post.alg','md5'));
		// $this->assign('notify_id',I('post.notify_id',time()));
		// $this->assign("error",$access_token);
	}

	//test 实例用法
	public function test() {
		$type = 'BY_Ava_test';
    if(IS_AJAX){
      $data = $this->getPost($type,'msg');
      $r = (new ApiService)->callRemote("",$data,false);
      return $this->parseResult($r);
		} else {
			$this->assign('logs', true); //前端日志开关，可选
			$this->assign('type', $type);
			$this->assign('field', [
				['api_ver', '100', LL('api version')],
				['msg', 'name', lang('username')],
			]);
			return $this->fetch();
		}
	}
	//解析curl返回 并格式化
	protected function parseResult($r) {
		if(is_string($r)){
			exit($r);
		}elseif(is_array($r)){
			// if(is_string($r['info']) && (false != strrpos($r['info'], '!DOCTYPE html') || false != strrpos($r['info'], 'Fatal error') || false != strrpos($r['info'], '系统异常'))){
			// 	exit($r['info']);
			// }
		}
		$ret = [];
		$ret['xml']   = $this->arrayToXml($r);
		$ret['json']  = json_encode($r);
		$ret['array'] = dump($r,false);
		json($ret)->send();
		// if($this->format == 'xml'){
		// 	xml($r)->send();
		// }else if($this->format == 'json'){
		// 	json($r)->send();
		// }else if($this->format == 'jsonp'){
		// 	json($r)->send();
		// 	// jsonp($r)->send();
		// }else{
		// 	if(!is_string($r)){
		// 		dump($r);
		// 	}else{
		// 		echo $r;
		// 	}
		// }
		exit;
		// multi_dump($r, $this->format, lang('parsed-data'));
	}

	function arrayToXml($arr,$dom=0,$item=0){
		if (!$dom){
			$dom = new DOMDocument("1.0");
		}
		if(!$item){
			$item = $dom->createElement("root");
			$dom->appendChild($item);
		}
		foreach ($arr as $key=>$val){
			$itemx = $dom->createElement(is_string($key)?$key:"item");
			$item->appendChild($itemx);
			if (!is_array($val)){
				$text = $dom->createTextNode($val);
				$itemx->appendChild($text);

			}else {
				$this->arrayToXml($val,$dom,$itemx);
			}
		}
		return $dom->saveXML();
	}
	//设置test模块curl请求参数
	protected function getPost($type,$str=''){

		$r = [];
		$r['api_ver'] = $this->api_ver;
		$r['type']    = $type;
		$str = explode(',', $str);
		foreach ($str as $v) {
			$r[$v] = input($v,'');
		}
		return $r;
	}
	/**
	 * 批量获取post参数
	 * @param array $filter
	 * @return array
	 */
	protected function getPostParams($filter = []){
		$data = [];
		//['key','default','alias']
		foreach ($filter as $val){
			if(is_array($val)){
				$name = !empty($val[2]) ? $val[2] : $val[0];
				$data[$name] = !empty($val[1]) ? $val[1] : input('post.'.$val[0]);
			}else{
				$data[$val] = input('post.'.$val);
			}
		}
		return $data;
	}

}