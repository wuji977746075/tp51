<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-11
 * Time: 14:54
 */

namespace app\index\controller;


use app\src\aliyuncs\action\ImageDetectAction;
use app\src\base\helper\ConfigHelper;
use app\src\base\helper\ValidateHelper;
use app\src\file\logic\UserPictureLogic;
use app\src\order\config\OrdersConfig;
use app\src\order\logic\OrdersLogic;
use app\src\order\model\Orders;
use think\Controller;

class Task extends Controller
{
    private $now;
    public function index(){
        set_time_limit(0);
        $from = $this->request->get('from');
        $last_call_time = cache('last_call_time');
        $this->now = time();
        //如果从crontab 或者 上次调用时间已经过去了24分钟
        if ($from == 'crontab' || $last_call_time + 24 * 60 < $this->now) {
            //暴恐敏感图像检测
            $this->imageDetect();

            //自动关闭订单的功能
            $this->closeOrders();

            //1. TODO 自动收货功能
            //2. TODO 自动完成功能

            //维修自动完成
            $this->doneRepair();

            //维修自动取消支付
            $this->cancelRepairPay();

            cache('last_call_time',time());
        }
        $str  = '上一次调用时间: '.date("Y-m-d H:i:s",$last_call_time);
        $str .= '<br/>下一次调用时间:'.date("Y-m-d H:i:s",$last_call_time+300);
        echo $str;
    }

    /**
     * 暴恐敏感图像检测
     */
    private function imageDetect()
    {

        $hour = date("H",time());
        if($hour < 6 && $hour > 18){
            //早上6点之前，晚上6点之后不检查
            return ;
        }


        $logic = new UserPictureLogic();
        $map = ['porn_prop' => ['exp', ' > -4 and porn_prop <= -1']];
        $order = "id desc";
        $result = $logic->query($map, ['curpage' => 1, 'size' => 5], $order);
        $action = new ImageDetectAction();
        if (ValidateHelper::legalArrayResult($result) && $result['info']['count'] > 0) {
            $imgList = $result['info']['list'];
            $size = count($imgList);
            foreach ($imgList as $img) {
                $imgUrl = ConfigHelper::getPictureUrl($img['id'], 480);
                $imgUrl = json_encode([$imgUrl]);
                $result = $action->sync($imgUrl);
                if ($result['status']) {
                    $rate = $result['info']['rate'];
                    $logic->saveByID($img['id'], ['porn_prop' => $rate]);
                } else {
                    $logic->saveByID($img['id'], ['porn_prop' => $img['porn_prop'] - 1]);
                }
            }
        }
    }

    /**
     * 关闭订单
     * @author hebidu <email:346551990@qq.com>
     */
    private function closeOrders(){
        //每次处理10个订单
        //1. 查找符合条件的10个订单进行处理
        $elapseFromLastUpdateTime = OrdersConfig::getAutoCloseTimeInterval();

        $logic = new OrdersLogic();
        $time = intval($this->now - $elapseFromLastUpdateTime);
        if($time < 0){
            \think\Log::log('自动关闭订单时间错误'.$time);
        }

        $map = [
            'order_status'=>Orders::ORDER_TOBE_CONFIRMED,
            'pay_status'=>Orders::ORDER_TOBE_PAID
        ];

        $map['updatetime'] = ['lt',$time];

        $result = $logic->query($map,['curpage'=>1,'size'=>20]);
        if(ValidateHelper::legalArrayResult($result) && $result['info']['count'] > 0){
            $orders = $result['info']['list'];
            foreach ($orders as $item){
                $logic->autoCloseOrder($item);
            }
        }
    }

    /**
     * 维修 - 自动完成 - 60min
     */
    private function doneRepair(){
        $timer = time() - 60*60;
        //查询超时的待司机完成维修
        (new \app\src\repair\logic\RepairLogicV2())->autoDone($timer);
    }

    /**
     * 维修 - 自动取消支付[支付中=>待支付] - 15min
     * 有回调时间差漏洞 - 联系客服
     */
    private function cancelRepairPay(){
        $timer = time() - 15*60;
        //查询超时的支付中订单
        (new \app\src\repair\logic\RepairOrderLogicV2())->autoCancel($timer);
    }

    /**
     * 向附近人推送消息
     * get参数： id 订单id 把该订单信息推送给附近的人
     */
    public function sendNewOrderMsg(){

        // set_time_limit(0);
        if(!isset($_GET['id']))   return ;
        if(!isset($_GET['size'])) return ;

        //推送给附近的多少人
        $id   = intval($_GET['id']);
        $size = intval($_GET['size']);
        //附近多近  100公里以内
        // $limit = 500;

        $r = (new \app\src\repair\logic\RepairLogicV2())->pushNearBy($id,$size);
        // dump($r);die();
    }

}