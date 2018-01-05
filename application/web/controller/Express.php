<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-11
 * Time: 9:22
 */

namespace app\web\controller;


use app\src\kuaidiniao\helper\KdniaoHelper;
use app\src\order\logic\OrdersExpressLogic;
use app\src\system\logic\DatatreeLogicV2;
use think\Controller;

class Express extends BaseWeb
{

    public function _initialize()
    {
//        parent::_initialize();
    }

    public function index($order_code=''){
        header("Content-type: text/html; charset=utf-8");
        $r = (new OrdersExpressLogic())->getInfo(['order_code'=>$order_code]);

        if(!$r['status']) exit($this->getErrTpl(lang('err_system')));
        if(empty($r['info']))  {
          exit($this->getErrTpl('查询不到该订单的物流信息'));

        }
        if(!$r['info']['expresscode'] || !$r['info']['expressno']){
            exit($this->getErrTpl(lang("err_order_not_ship")));
        }

        $express_code  = $r['info']['expresscode'];
        $express_no    = $r['info']['expressno'];

        return $this->getExpressContent($express_code,$express_no);
//        return $this->showExpress($r['info']['expresscode'],$r['info']['expressno']);
    }

    protected function showExpress($id,$no){

        //重组网页
        $url = 'http://wap.kuaidi100.com/wap_result.jsp?rand='.mt_rand().'&id='.$id.'&postid='.$no.'&queryInput='.$no;
        // echo $url;
        $str = $this->getHttp($url);

        preg_match('/<form(.*?)>([\s\S]*)<\/form>/',$str,$head);
        // dump($head);exit;
        if(!isset($head[2])){
            return $this->innerIframe($id,$no);
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

        $this->assign('list',$list);
        $this->assign('empty',$this->getErrTpl(lang('err_no_express_info')));
        return $this->fetch();
    }

    protected function getErrTpl($con){
        return '<center><h3>'.$con.'</h3></center>';
    }

    protected function getHttp($url) {
        $file_contents = '';

        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($ch, CURLOPT_REFERER, "http://www.kuaidi100.com");
        curl_setopt($ch, CURLOPT_HTTPGET, 1); // 发送一个常规的Post请求
        $file_contents = curl_exec($ch);
        curl_close($ch);

        return $file_contents;
    }

    private function innerIframe($id,$no){
        //内嵌iframe
        $url = 'http://m.kuaidi100.com/index_all.html?type='.$id.'&postid='.$no;
        // echo $url;exit;
        $this->assign('url',$url);
        return $this->fetch('iframe');
    }

    private function getExpressContent($id,$no){
      return $this->innerIframe($id,$no);
    }

    public function kdniao(){

        if(IS_GET){
            $order_code = $this->_param('order_code','');

            $r = (new OrdersExpressLogic())->getInfo(['order_code'=>$order_code]);

            if(!$r['status']){
                echo json_encode(['Success'=>false,'Reason'=>lang('err_system')]) ;
            }
            $info = $r['info'];
            if(empty($info) )  {

                echo json_encode(['Success'=>false,'Reason'=>'该订单没有物流信息']) ;
                exit();
            }

            $express_code  = $r['info']['expresscode'];
            $express_no    = $r['info']['expressno'];
            $express_name    = $r['info']['expressname'];

            if(empty($express_code) || empty($express_no)){
                echo json_encode(['Success'=>false,'Reason'=>lang("err_order_not_ship")]) ;
                exit();
            }
            $this->assign('express_code',$express_code);
            $this->assign('express_name',$express_name);
            return $this->fetch();
        }

        $express_code = $this->_param('express_code','');
        $express_name = $this->_param('express_name','');
//        dump($express_code);
        $map = [
            'code'=>['like','00B___'],
            'name'=>$express_name
        ];
        $result = (new DatatreeLogicV2())->getInfo($map);
        echo KdniaoHelper::getOrderTracesByJson($result['alias'],$express_code);
    }

}