<?php
/**
 * 周舟 hzboye010@163.com
 * addby sublime snippets
 * 空控制器
 */

namespace app\test\controller;
use think\Controller;
class Error extends Controller{

    public function  _empty(){
    	// exit('回头是岸。。。');
      // action('config/version');
      $this->redirect('Config/version');
    }
}

