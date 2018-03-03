<?php
namespace app\push\controller;
use Workerman\Worker;
use GatewayWorker\BusinessWorker;
class Run2{
  /**
     * 构造函数
     * @access public
     */
    public function __construct(){

        // client <=> gateway <=> businessworker

        //2,初始化 bussinessWorker 进程
        //BusinessWorker类基于基础的Worker。BusinessWorker是运行业务逻辑的进程，BusinessWorker收到Gateway转发来的事件及请求时会默认调用Events.php中的onConnect onMessage onClose方法处理事件及数据，开发者正是通过实现这些回调控制业务及流程。
        $worker = new BusinessWorker();
        $worker->name = 'WebIMBusinessWorker';
        $worker->count = 4;
        $worker->registerAddress = '127.0.0.1:1238';
        //设置处理业务的类,此处制定Events的命名空间
        $worker->eventHandler = '\app\push\controller\Events';

         //运行所有Worker;
        Worker::runAll();
    }
}