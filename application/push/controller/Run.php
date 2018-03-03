<?php
namespace app\push\controller;
use Workerman\Worker;
use GatewayWorker\Register;
use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
class Run{
  /**
     * 构造函数
     * @access public
     */
    public function __construct(){

        // client <=> gateway <=> businessworker

        //由于是手动添加，因此需要注册命名空间，方便自动加载，具体代码路径以实际情况为准
        // \think\Loader::addNamespace([
        //     'Workerman' => VENDOR_PATH . 'Workerman/workerman',
        //     'GatewayWorker' =>VENDOR_PATH . 'Workerman/gateway-worker/src',
        // ]);

        //初始化各个GatewayWorker
        // windows 需要分开1,2,3启动

        //1,初始化register
        //Register类基于基础的Worker。Gateway进程和BusinessWorker进程启动后分别向Register进程注册自己的通讯地址，Gateway进程和BusinessWorker通过Register进程得到通讯地址后，就可以建立起连接并通讯了。
        new Register('text://0.0.0.0:1238');

        //2,初始化 bussinessWorker 进程
        //BusinessWorker类基于基础的Worker。BusinessWorker是运行业务逻辑的进程，BusinessWorker收到Gateway转发来的事件及请求时会默认调用Events.php中的onConnect onMessage onClose方法处理事件及数据，开发者正是通过实现这些回调控制业务及流程。
        $worker = new BusinessWorker();
        $worker->name = 'WebIMBusinessWorker';
        $worker->count = 4;
        $worker->registerAddress = '127.0.0.1:1238';
        //设置处理业务的类,此处制定Events的命名空间
        $worker->eventHandler = '\app\push\controller\Events';

        //3,初始化 gateway 进程
        // Gateway类用于初始化Gateway进程。Gateway进程是暴露给客户端的让其连接的进程。所有客户端的请求都是由Gateway接收然后分发给BusinessWorker处理，同样BusinessWorker也会将要发给客户端的响应通过Gateway转发出去。
        // Gateway类基于基础的Worker。可以给Gateway对象的onWorkerStart、onWorkerStop、onConnect、onClose设置回调函数，但是无法给设置onMessage回调。Gateway的onMessage行为固定为将客户端数据转发给BusinessWorker。
        $gateway = new Gateway("websocket://0.0.0.0:8282");
        $gateway->name = 'WebIMGateway';
        $gateway->count = 4;
        $gateway->lanIp = '127.0.0.1';
        $gateway->startPort = 2900;
        $gateway->registerAddress = '127.0.0.1:1238';

         //运行所有Worker;
        Worker::runAll();
    }
}