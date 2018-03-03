<?php
namespace app\push\controller;
use Workerman\Worker;
use GatewayWorker\Register;
class Run1{
  /**
     * 构造函数
     * @access public
     */
    public function __construct(){

        // client <=> gateway <=> businessworker

        //初始化各个GatewayWorker
        //1,初始化register
        //Register类基于基础的Worker。Gateway进程和BusinessWorker进程启动后分别向Register进程注册自己的通讯地址，Gateway进程和BusinessWorker通过Register进程得到通讯地址后，就可以建立起连接并通讯了。
        new Register('text://0.0.0.0:1238');
         //运行所有Worker;
        Worker::runAll();
    }
}