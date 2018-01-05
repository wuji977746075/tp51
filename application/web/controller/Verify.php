<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-13
 * Time: 16:21
 */

namespace app\web\controller;

use think\Controller;

class Verify extends Controller{

    public function random(){
        $verify = new \app\src\verify\think\Verify();

        $config = array('fontSize' => 22, // 验证码字体大小
            'length' => 4, // 验证码位数
            'useNoise' => false, // 关闭验证码杂点
            'imageW' => '240', 'imageH' => '40');

        return $verify -> entry(1);
    }

    public function index(){
        return $this->fetch();
    }
}