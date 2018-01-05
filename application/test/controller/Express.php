<?php
/**
 * 周舟 hzboye010@163.com
 * addby sublime snippets
 */
namespace app\test\controller;
use app\shop\api\OrdersExpressApi;

class Express extends Ava{
	public function view($company_code='',$no='',$tpl=0){
		if($tpl){
			$this->test1($company_code,$no);
		}else{
			$this->test($company_code,$no);
		}
	}

	public function viewOrder($order_code='',$tpl=0){
		header("Content-type: text/html; charset=utf-8");
		$r = apiCall(OrdersExpressApi::GET_INFO,[['order_code'=>$order_code]]);
		// dump($r);exit;
		if(!$r['status']) exit($this->getErrTpl('系统错误'));
		if(!$r['info'])   exit($this->getErrTpl('无此订单'));
		if(!$r['info']['expresscode'] || !$r['info']['expressno']) exit($this->getErrTpl('此订单尚未发货'));
		if($tpl){
			$this->test1($r['info']['expresscode'],$r['info']['expressno']);
		}else{
			$this->test($r['info']['expresscode'],$r['info']['expressno']);
		}
	}
	//快递查询 内嵌模板
	public function test1($id='',$no=''){
		//内嵌iframe
		$url = 'http://m.kuaidi100.com/index_all.html?type='.$id.'&postid='.$no;
		// echo $url;exit;
		$this->assign('url',$url);
		$this->display('express');

	}
	//快递查询 重组模板
	public function test($id='',$no=''){
		//重组网页
		$url = 'http://wap.kuaidi100.com/wap_result.jsp?rand='.mt_rand().'&id='.$id.'&postid='.$no;
		// echo $url;
		$str = $this->getHttp($url);
		// dump($str);exit;
		preg_match('/<form(.*?)>([\s\S]*)<\/form>/',$str,$head);
		// dump($head);exit;
		if(!isset($head[2])){
			$this->test1($id,$no);
			exit;
			// exit($this->getErrTpl('api失效 或 不支持快递【'.$id.'】'));
		}
		$str = $head[2];
		$search = array(
			"/<input.*?\/>/si",
			"/<div.*?>.*?<\/div>/si",
			"/<span.*?>.*?<\/span>/si",
			// "·",
		);
		$str = preg_replace ($search, "", $str);
		preg_match_all('/<p.*?>([^>]*?)<br(.*?)>(.*?)<\/p>/si',$str,$head);
		$time = $head[1];
		$stat = $head[3];
		$list = array();
		foreach ($time as $k=>$v) {
			$list[] = array($v,$stat[$k]);
		}
		// $this->assign('time',$time);
		// $this->assign('stat',$stat);
		$this->assign('list',$list);
		// $this->assign('empty','<center><h1>没有查到物流信息，如此信息经常出现，请联系管理员</h1></center>');
		$this->assign('empty',$this->getErrTpl('未查到物流信息'));
		$this->display('express2');
	}

	protected function getErrTpl($con){
		return '<center><h1>'.$con.'</h1></center>';
	}

	protected function getHttp($url) {
		if (function_exists('file_get_contents')) {
			$file_contents = @file_get_contents($url);
		}
		if ($file_contents =='') {
			$ch = curl_init();
			$timeout = 30;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$file_contents = curl_exec($ch);
			curl_close($ch);
		}
		return $file_contents;
	}
}