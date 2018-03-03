<?php
namespace app\push\controller;
use GatewayWorker\Lib\Gateway;
/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events{
    /**
    * 当客户端发来消息时触发 - 变更需要重启server3
    * @param int $client 连接id
    * @param mixed $src 具体消息
    */
    public static function onMessage($client, $src){
      try{
        $data = json_decode($src,true);
        if(!is_array($data)){
          $this->pushClient($client,$src);
        }else{
          $from = (int) $data['from'];
          $to   = (int) $data['to'];
          $msg  = (string) $data['msg'];

          // todo : 设备数 限制
          Gateway::bindUid($client, $from);

          if($to){ //用户推送
            if(Gateway::isUidOnline($to)){
              $this->pushUid($from,$to,$msg);
              $this->pushUid(0,$from,'发送成功 ...');
            }else{
              $this->pushUid(0,$from,'该用户不在线 ...');
            }
          }else{ //用户咨询
              $this->pushClient($client,'感谢反馈 ...');
          }
        }

      }catch(\Exception $e){
        $this->pushClient($client,$e->getMessage());
      }
    }
    private function pushClient($client,$msg){
      Gateway::sendToClient($this->client,$msg);
    }
    private function pushUid($from,$to,$msg){
      $data = [
        'from'=>$from,
        'to'  =>$to,
        'msg' =>$msg,
      ];
      Gateway::sendToUid($to,json_encode($data));
    }
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
      // echo $client_id.':  connected ... \n';
      // Gateway::sendToCurrentClient($client_id);
    }
    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public static function onClose($client_id){
      // $logout_message = [
      //   'message_type' => 'logout',
      //   'id'           => $_SESSION['id']
      // ];
      // Gateway::sendToAll(json_encode($logout_message));
    }
    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public static function onError($client_id, $code, $msg)
    {
        echo "error $code $msg\n";
    }
    /**
     * 每个进程启动
     * @param $worker
     */
    public static function onWorkerStart($worker)
    {
    }
}