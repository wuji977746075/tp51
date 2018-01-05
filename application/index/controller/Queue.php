<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-10
 * Time: 17:21
 */
namespace  app\index\controller;

use app\src\jobs\Hello;
use think\controller\Rest;

class  Queue extends Rest {
    public function test(){
        $data = [];
        $queue = "test";
//        \think\Queue::push(new Hello(), $data, $queue);
        \think\Queue::push("app\\src\\jobs\\Hello", $data, $queue);
        echo "push";
    }
}