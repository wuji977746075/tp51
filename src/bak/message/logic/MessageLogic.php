<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-03
 * Time: 15:06
 */

namespace app\src\message\logic;


use app\src\base\logic\BaseLogic;
use app\src\extend\Page;
use app\src\message\facade\MessageFacade;
use app\src\message\model\Message;
use app\src\system\logic\DatatreeLogicV2;
use app\src\user\logic\UcenterMemberLogic;
use think\Db;

class MessageLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new Message());
    }

    // todo : 暂时随机返回
    public function countNew(){
        $num = mt_rand(0,100);
        session('_msg_num',$num);
        return $num;
    }
    private function getQuery(){
        return  Db::table("itboye_message")->alias("m")
            ->join(["itboye_message_box"=>"mb"]," m.id = mb.msg_id ","LEFT")
            ->join(["common_datatree"=>"mc"], "m.dtree_type = mc.id",'left');
    }

    public function queryMessage($map,$page = array('curpage'=>0,'size'=>10),$order = false, $params = false,$field=false){

        $query = $this->getQuery();
        $list = $query->where($map)->order($order)->field($field)
            -> page($page['curpage'] . ',' . $page['size'])->select();
        $count = $this->getQuery()->where($map)->order($order)->field($field)-> count();

        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);

        //分页跳转的时候保证查询条件
        if ($params !== false) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        return $this -> apiReturnSuc(array("show" => $show, "list" => $list));

    }

    public function getMessageByID($id){
        $map = array('m.id'=>$id);
        $list = $this->getQuery()->where($map)
            ->find();

        return $this -> apiReturnSuc($list);
    }

    public function recordMessage($entity=array('title'=>'','to_id'=>0,'dtree_type'=>0,'from_id'=>0,'summary'=>'','send_time'=>0,'status'=>1,'extra'=>'')){
        if(empty($entity)){
            return $this -> apiReturnErr('entity参数缺失');
        }

        $message = array(
            'dtree_type' =>$entity['dtree_type'],
            'title'      =>$entity['title'],
            'content'    =>$entity['content'],
            'send_time'  =>$entity['send_time'],
            'from_id'    =>$entity['from_id'],
            'summary'    =>$entity['summary'],
            'extra'      => $entity['extra'],
            'status'     =>$entity['status']
        );

        $result = $this->add($message);

        if(!$result['status']){
            return $this -> apiReturnErr($result['info']);
        }else{
            $id = $result['info'];
        }

        $message_box = array(
            'to_id'      => $entity['to_id'],
            'msg_status' =>0,
            'msg_id'     =>$id
        );

        $result = (new MessageBoxLogic())->add($message_box);

        return $result;
    }

    /**
     * 发送推送消息
     */
    public function pushMessage($message=array('alert'=>0,'ticker'=>' ','title'=>' ','text'=>' '),$uid,$pushAll=false,$record=false,$after_open=false){

        $param = array(
            'alert' => $message['alert'],
            'ticker' => $message['ticker'],
            'title' => $message['title'],
            'text' => $message['text' ]
        );

        if($record){
            //记录消息
            $dtree_type = 0;
            $result = (new DatatreeLogicV2())->getInfo(['name'=>L('MESSAGE_PUSH')]);

            $dtree_type = !empty($result )?$resul ['id']:0;

            if($pushAll){
                $to_id = -2;
            }else{
                $to_id = $uid;
            }

            $entity = array(
                'title'=>$param['title'],
                'content'=>$param['text'],
                'to_id'=>$to_id,
                'dtree_type'=>$dtree_type,
                'from_id'=>0,//0:系统
                'summary'=>'',
                'send_time'=>time(),
                'status'=>1
            );
            $result = (new MessageLogic())->recordMessage($entity);

            if($result['status']){
                $msg_id = $result['info'];
            }else{
                return $this->apiReturnErr($result['info']);
            }

        }

//        vendor('BoyePushApi',APP_PATH.'Api/Vendor/Umeng/','.class.php');
//
//        $BoyePushApi = new BoyePushApi();
//
//        if($pushAll){
//            //添加系统消息参数,after_open 跳转
//            $after_open = array(
//                'type' => 'go_activity',
//                'param' => MessageModel::MESSAGE_SYSTEM_ACTIVITY,
//                'extra' => array('id'=>$msg_id)
//            );
//            $result = $BoyePushApi->sendAll($param,$after_open);
//        }else{
//            if($after_open){
//                $result = $BoyePushApi->send($uid,$param,$after_open);
//            }else{
//                $result = $BoyePushApi->send($uid,$param);
//            }
//        }

        if($result['status']){

            return $this->apiReturnSuc('发送成功！');

        }else{
            return $this->apiReturnErr($result['info']);
        }
    }

    /**
     * 根据消息ID发送推送消息
     */
    public function pushMessageByMessageID($id){

        //查找消息内容
        $result = $this->getMessageByID($id);
        if($result['status']){
            $message_info = $result['info'];
        }else{
            return $this->apiReturnErr($result['info']);
        }

        //推送消息
        $uid = $message_info['to_id'];
        $pushAll = false;
        if($uid==-2){
            $pushAll = true;
        }
        $message = array(
            'alert'=>$message_info['content'],
            'ticker'=>$message_info['title'],
            'title'=>$message_info['title'],
            'text'=>$message_info['content']
        );

        $result = $this->pushMessage($message,$uid,$pushAll);

        if($result['status']){
            return $this->apiReturnSuc('推送成功!');
        }else{
            return $this->apiReturnErr($result['info']);
        }

    }

    /**
     * 记录并推送指定消息类型消息
     */
    public function pushMessageAndRecordWithType($msg_type,$message=array('title'=>' ','content'=>' ','summary'=>' ','extra'=>''),$uid=0,$pushAll=false,$after_open=false){

        //记录消息
//        $result = apiCall(DatatreeApi::GET_INFO, array(array('name' => $msg_type)));
        $result = (new DatatreeLogicV2())->getInfo(['name' => $msg_type]);

        if (!empty($result)) {
            $dtree_type = !empty($result['info']) ? $result['info']['id'] : 0;
        } else {
            $dtree_type = 0;
        }

        if ($pushAll) {
            $to_id = -2;
        } else {
            $to_id = $uid;
        }

        $entity = array(
            'title'      => $message['title'],
            'content'    => $message['content'],
            'to_id'      => $to_id,
            'dtree_type' => $dtree_type,
            'from_id'    => 0,//0:系统
            'summary'    => $message['summary'],
            'extra'      => $message['extra'],
            'send_time'  => time(),
            'status'     => 1
        );
//        $result = apiCall(MessageApi::RECORD_MESSAGE, array($entity));
        $result = (new MessageLogic())->recordMessage($entity);

        if ($result['status']) {

            $msg = array(
                'alert'  => $message['content'],
                'ticker' => $message['title'],
                'title'  => $message['title'],
                'text'   => $message['content']
            );

            $result = $this->pushMessage($msg, $uid, $pushAll, false, $after_open);
            if ($result['status']) {
                return $this->apiReturnSuc('成功!');
            } else {
                return $this->apiReturnErr($result['info']);
            }


        } else {
            return $this->apiReturnErr($result['info']);
        }
    }
}