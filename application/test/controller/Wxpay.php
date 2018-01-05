<?php
/**
 * 周舟 hzboye010@163.com
 * addby sublime snippets
 * 微信支付 测试类
 */

namespace app\test\controller;

use app\extend\BoyeService;

class Wxpay extends Ava{
    //微信登陆
    public function preOrder(){
        if(IS_AJAX){
          $data = [
            'code'        => input('post.code',''),
            'desc'        => input('post.desc',''),
            'total_price' => input('post.total_price',''),
            'api_ver'     => $this->api_ver,
            'type'        => 'BY_Wxpay_preOrder',
          ];

          $service = new BoyeService();
          $result = $service->callRemote("",$data,false);
          return $this->parseResult($result);
        }else{
            $this->assign('type','BY_Wxpay_preOrder');
            $this->assign('field',[
                ['api_ver','100',LL('need-mark api version')],
                ['code','T1458179774U21P4',LL('need-mark pay code')],
                ['desc','',L('desc')],
                ['total_price','',L('total_price')],
            ]);
            return $this->fetch('ava/test');
        }
    }
}