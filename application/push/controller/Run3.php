<?php
namespace app\push\controller;
use Workerman\Worker;
use GatewayWorker\Gateway;
class Run3{
  /**
     * 构造函数
     * @access public
     */
    public function __construct(){
        // client <=> gateway <=> businessworker

        //3,初始化 gateway 进程
        // Gateway类用于初始化Gateway进程。Gateway进程是暴露给客户端的让其连接的进程。所有客户端的请求都是由Gateway接收然后分发给BusinessWorker处理，同样BusinessWorker也会将要发给客户端的响应通过Gateway转发出去。
        // Gateway类基于基础的Worker。可以给Gateway对象的onWorkerStart、onWorkerStop、onConnect、onClose设置回调函数，但是无法给设置onMessage回调。Gateway的onMessage行为固定为将客户端数据转发给BusinessWorker。
        $gateway = new Gateway("websocket://0.0.0.0:8282");

        //20秒内需请求一次
        $gateway->pingInterval = 10;
        $gateway->pingNotResponseLimit = 2; // 0不要求回应
        $gateway->pingData = '';// '{"uid":0,"msg":"","type":"ping"}'; // ""要求客户端定时ping

        $gateway->name = 'WebIMGateway';
        $gateway->count = 4;
        $gateway->lanIp = '127.0.0.1';
        $gateway->startPort = 2900;
        $gateway->registerAddress = '127.0.0.1:1238';

         //运行所有Worker;
        Worker::runAll();
    }
}