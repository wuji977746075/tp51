<?php

/**
 * Created by PhpStorm.
 * User: zhoujinda
 * Date: 2015/12/23
 * Time: 16:03
 */
namespace app\admin\controller;

use app\src\message\facade\MessageFacade;
use app\src\message\logic\MessageBoxLogic;
use app\src\message\logic\MessageLogic;
use app\src\system\logic\DatatreeLogicV2;
use app\src\email\action\EmailSendService;
use app\src\user\logic\UcenterMemberLogic;
use think\Log;
class Message extends Admin{

    protected function initialize(){
        parent::initialize();
        $this->assignTitle('消息管理');
    }


    public function count(){
        return ajaxReturnSuc('','',(new MessageLogic)->countNew());
    }

    public function index(){

        $startdatetime = $this->_param('startdatetime',date('Y/m/d H:i',time()-24*3600*30));
        $enddatetime   = $this->_param('enddatetime',date('Y/m/d H:i',time()));
        $msg_type      = $this->_param('msg_type','');
        $startdatetime = urldecode($startdatetime);
        $enddatetime = urldecode($enddatetime);
        $this->assign('msg_type',$msg_type);

        $startdatetime = urldecode($startdatetime);
        $enddatetime = urldecode($enddatetime);

        $to_uid = $this->_param('to_uid',-2);



        //分页时带参数get参数
        $params = array(
            'startdatetime' =>$startdatetime,
            'enddatetime'   =>$enddatetime,
            'msg_type'      =>$msg_type
        );

        $startdatetime = strtotime($startdatetime);
        $enddatetime = strtotime($enddatetime);

        if($startdatetime === FALSE || $enddatetime === FALSE){
            $this->error(L('ERR_DATE_INVALID'));
        }

        $map = array();

        if(!empty($to_uid) && $to_uid != -2){
            $map['to_id'] = $to_uid;
        }

        $map['send_time'] = array(array('EGT',$startdatetime),array('elt',$enddatetime),'and');
        if(!empty($msg_type)){
            $map['dtree_type'] = $msg_type;
        }

        $field = 'm.id,title,content,to_id,name,from_id,summary,send_time';
        $page = array('curpage' => $this->_param('p', 0), 'size' => 15);
        $order = " m.id desc ";

        $result = (new MessageLogic())->queryMessage($map,$page,$order,$params,$field);

        //
        if($result['status']){
            $this->assign('startdatetime',$startdatetime);
            $this->assign('enddatetime',$enddatetime);
            $this->assign('show',$result['info']['show']);
            $this->assign('list',$result['info']['list']);

            $result = (new DatatreeLogicV2())->queryNoPaging(['parentid'=>6009]);

            $this->assign('msg_types',$result);

            return $this->boye_display();
        }else{
            $this->error(L('UNKNOWN_ERR'));
        }
    }


    /**
     * 删除
     */
    public function delete(){

        $map = array('msg_id' => $this->_param('id', -1));
        // $result = apiCall(MessageBoxApi::DELETE,array($map));
        $result = (new MessageBoxLogic())->delete($map);

        if($result['status']){
            $map = array('id' => $this->_param('id', -1));
            // $result = apiCall(MessageApi::DELETE,array($map));
            $result =(new MessageLogic())->delete($map);
            if($result['status']){
                $this -> success(L('RESULT_SUCCESS'));
            }else{
                Log::record('[INFO]' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
                $this->error($result['info']);
            }
        }else{
            Log::record('[INFO]' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
            $this->error($result['info']);
        }

    }

    /**
     * 批量删除
     */
    public function bulkDelete(){
        $ids = $this->_param('ids/a',[]);

        if ($ids === -1) {
            $this -> error(L('ERR_PARAMETERS'));
        }

        $map = array('msg_id' => array('in',$ids));

        $result = (new MessageBoxLogic())->delete($map);

        if($result['status']){
            $map = array('id' => array('in', $ids));

            $result = (new MessageLogic())->delete($map);

            if($result['status']){
                $this -> success(L('RESULT_SUCCESS'));
            }else{
                Log::record('[INFO]' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
                $this->error($result['info']);
            }
        }else{
            Log::record('[INFO]' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
            $this->error($result['info']);
        }

    }

    /**
     * 推送消息
     */
    public function pushMessage(){
        $id = $this->_param('id',-1);
        if($id <0) $this->error('参数缺失');

        $r = (new MessageLogic())->pushMessageByMessageID($id);
        !$r['status'] && $this->error($r['info']);
        $this->success($r['info']);

    }

    /**
     * 消息发送页面
     * 1. 向所有人发送消息
     * 2. 向单个人发送消息
     */
    public function send(){
        if(IS_GET){
            $r = (new DatatreeLogicV2())->queryNoPaging(['parentid'=>6009]);
            $this->assign('msg_types',$r);
            return $this->boye_display();
        }else{
            $notice_type= $this->_param('notice_type/a',[]);
            $dtree_type = $this->_param('dtree_type','','请选择消息类型');
            $content    = $this->_param('content','');
            $title      = $this->_param('title','');
            $summary    = $this->_param('summary','');
            $to_all_uid = $this->_param('to_all_uid','');
            $to_id      = $this->_param('to_uid','');
            $msg_status = $this->_param('msg_status','');

            $from_id = intval(UID);
            $status  = 1;
            $entity = [
                'dtree_type'  => $dtree_type,
                'content'     => $content,
                'title'       => $title,
                'summary'     => $summary,
                'status'      => $status,
                'from_id'     => $from_id,
                'create_time' => NOW_TIME,
                'send_time'   => NOW_TIME
            ];

            $r = (new MessageLogic())->add($entity);
            !$r['status'] && $this->error($r['info']);
            $msg_id = $r["info"];

            $mbox = [
                'msg_id'     => $msg_id,
                'to_id'      => $to_id,
                'msg_status' => $msg_status,
            ];
            $r = (new MessageBoxLogic())->add($mbox);
            !$r['status'] && $this->error($r['info']);
            if($to_all_uid == -2) $to_id = -2;
            $this->noticeMessage($title,$summary,$content,$notice_type,$dtree_type,$msg_id,$to_id);
            // dump("stop");
            $this->success("操作成功!");//, url('Message/index'));
        }
    }

    /**
     * 消息通知
     * @param $title
     * @param $summary
     * @param $content
     * @param $notice_type
     * @param $dtree_type
     * @param $msg_id
     * @param $to_id
     */
    private function noticeMessage($title,$summary,$content,$notice_type,$dtree_type,$msg_id,$to_id){
        $facade = new MessageFacade();

        foreach ($notice_type as $type){
            if($type == "1"){
                $entity = [
                    'uid'      =>-1,
                    'to_uid'   =>$to_id,
                    'msg_type' =>$dtree_type,
                    'content'  =>$content,
                    'extra'    =>'',
                    'summary'  =>$summary,
                    'title'    =>$title,
                    'push'     =>1,
                    'record'   =>1,
                ];
                // dump($entity);
                // app推送
                if($to_id<1 && !$to_id == -2){
                    $this->error(Linvalid('to_id'));
                }
                $facade->pushMsg($entity);

            }elseif($type == "2"){
                //短信通知
                return $this->error('暂未开通');
                $r=(new UcenterMemberLogic())->getInfo(['id'=>$to_id]);

            }elseif($type == "3"){
                //邮箱通知
                if($to_id == -2){
                   return $this->error('无法群发邮件');
                }
                $r=(new UcenterMemberLogic())->getInfo(['id'=>$to_id]);
                if($r['status']){
                    $email=$r['info']['email'];
                    return (new EmailSendService())->send($email,$title,$content);
                }
            }
        }
    }

}