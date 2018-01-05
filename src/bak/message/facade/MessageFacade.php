<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-03
 * Time: 15:07
 */

namespace app\src\message\facade;

use app\src\base\helper\ResultHelper;
use app\src\extend\umeng\BoyePushApi;
use app\src\message\config\MessageConfig;
use app\src\message\enum\MessageStatus;
use app\src\message\enum\MessageType;
use app\src\message\logic\MessageBoxLogic;
use app\src\message\logic\MessageLogic;
use app\src\message\sms\JuheSms;
use app\src\message\sms\QCloudSms;
use think\Db;

class MessageFacade{

    //清空 消息
    //param : uid , msg_type
    public function clear($param){
        extract($param);
        if($msg_id){
            $r = Db::table("itboye_message_box")->alias("box")
            ->join(["itboye_message"=>"msg"],"msg.id = box.msg_id","LEFT")
            ->where(['box.to_id'=>$uid,'msg.dtree_type'=>$msg_type,'box.msg_id'=>$msg_id])
            ->update(['msg_status'=>2]);
        }else{
            $r = Db::table("itboye_message_box")->alias("box")
            ->join(["itboye_message"=>"msg"],"msg.id = box.msg_id","LEFT")
            ->where(['box.to_id'=>$uid,'msg.dtree_type'=>$msg_type])
            ->update(['msg_status'=>2]);
        }
        if($r) return returnSuc('操作成功');
        else return returnErr('操作失败');
    }
    /**
     * 发送短信息
     * @param MessageEntity $msg
     * @return array
     */
    public function send(MessageEntity $msg){

        //获取配置 来调用相应的短信服务
        $ret = [];

        switch (MessageConfig::getMsgType()){
            case MessageConfig::TYPE_JUHE:
                $code     = $msg->getCode();
                $limit    = 10;
                $scene    = $msg->getScene();
                $tplValue = "#code#=$code&#limit#=$limit&#scene#=$scene";
                $msg->setTplId("25977");
                $msg->setTplValue($tplValue);
                $ret = (new JuheSms(MessageConfig::getExtraCfg()))->init($msg)->send();
                break;
            case MessageConfig::TYPE_LOCAL:
                $ret = ResultHelper::success(lang('tip_message_your_code_is',['code'=>$msg->getCode()]));
                break;
            case MessageConfig::TYPE_QCLOUD:
                //腾讯短信内容
                //TODO: 更通用处理
                $content = "{1}为您的验证码，请于{2}分钟内填写。如非本人操作，请忽略本短信。";
                $content = str_replace("{1}",$msg->getCode(),$content);
                $content = str_replace("{2}","10",$content);
                $msg->setContent($content);
                $ret = (new QCloudSms(MessageConfig::getExtraCfg()))->init($msg)->send();
                break;
            default:
                break;
        }

        return $ret;
    }

    /**
     * 业务 : 记录消息并推送 - 查看接口描述
     *  系统广播系统消息
     *  系统发送各类用户消息
     *  用户发送用户消息
     *  用户发送咨询消息
     *eg:
     * $params = [
     * uid|0|int,           //发送方UID,0为系统
     * to_uid,              //接收UID,-2为全体,多个,分割
     * msg_type|0|int,      //消息类型
     * content||string,     //消息主体,推送主体
     * extra||string,   //可选,消息额外信息
     * summary||string, //可选,消息概述
     * title||string,   //可选,消息标题,推送标题
     * push|0|int,      //可选,是否推送
     * record|1|int,    //可选,是否记录
     * client||string'  //可选,推送的客户端  'driver'/'worker'/其他
     * ];
     * eg: $after_open_extra = ['sound'=>'xx','repair'=>12];
     */
    public function pushMsg(array $params,$after_open_extra=[]){

        extract($params);
        $record = array_key_exists("record",$params)?$params['record']:"";
        $summary = $summary ? $summary : $content;
        $title   = $title ? $title : $content;
        if(!is_numeric($to_uid)) $record = false;
            //多个to_uid 不记录消息
            // $to_uid = implode(',', array_unique(explode(',', $to_uid)));
        // }
        //检查$msg_type
        // $msg_type_string = (new MessageType())->getMsgTypeString($msg_type);
        // if(!$msg_type_string) return returnErr(Linvalid('msg_type'));
        if(!(new MessageType())->isLegalId($msg_type)) return returnErr(Linvalid('msg_type'));

        //记录消息
        if($record){
            $r = $this->addMsg($params);
            if(!$r['status']) return returnErr($r['info']);
            $msg_id = (int)$r['info'];
        }

        //推送消息
        if($push){
            $pushAll = false;
            if($uid === 0 && $to_uid === '-2') $pushAll = true;
            //todo : check uid & to_uid
            $BoyePushApi = new BoyePushApi();
            $after_open  = [
                'type'  => 'go_activity',
                'param' => $msg_type,
                'extra' => $record ? ['id'=>$msg_id] : [],//'uid'=>$to_uid]
            ];

            $after_open['extra'] = array_merge($after_open['extra'],$after_open_extra);
            $body = [
                // 'alert'  =>$summary,
                'alert'  =>$content,
                'ticker' =>$title,
                'title'  =>$title,
                'text'   =>$content
            ];
            if($pushAll){
                $r = $BoyePushApi->sendAll($body,$after_open,$client);
            }else{

                $r = $BoyePushApi->send($to_uid,$body,$after_open,$client);
            }

        }
        if($r['status']){
            return returnSuc(L('success'));
        }else{
            return returnErr($r['info']);
        }
    }
    /**
     * 发送消息
     * @param $entity
     * @return array
     */
    public function addMsg($entity){
        $logic    = new MessageLogic();
        $boxLogic = new MessageBoxLogic();
        $uid     = $entity['uid'];
        $toUid   = $entity['to_uid'];
        $content = $entity['content'];
        $title   = $entity['title'];
        $summary = $entity['summary'];
        $extra   = $entity['extra'];
        $type    = $entity['msg_type'];


        Db::startTrans();
        $flag = true;
        $info = "";
        $result = $logic->add([
            'dtree_type'  =>$type,
            'content'     =>$content,
            'title'       =>$title,
            'create_time' =>time(),
            'send_time'   =>time(),
            'from_id'     =>$uid,
            'summary'     =>$summary,
            'status'      =>1,
            'extra'       =>$extra
        ]);

        if(!$result['status']){
            $flag = false;
            $info = $result['info'];
        }
        $message_id = intval($result['info']);

        $result = $boxLogic->add([
            'to_id'      =>$toUid,
            'msg_status' =>MessageStatus::NOT_READ,
            'msg_id'     =>$message_id
        ]);

        if(!$result['status']){
            $flag = false;
            $info = $result['info'];
        }

        if($flag){
            Db::commit();
            return returnSuc($message_id);
        }else{
            Db::rollback();
            return returnErr($info);
        }
    }

    /**
     * 分页获取消息 - 用户消息和公告 或 仅公告
     * @param $uid        int 消息接收者的id
     * @param $type       int 消息类型
     * @param $page_index int 页码
     * @param $page_size  int 每页大小
     * @return array
     */
    public function query($uid,$type,$page_index=1,$page_size=10,$start_time=0){

        $page_index = max(0,intval($page_index) - 1);
        $page_size  = intval($page_size);
        $where = [
            'box.to_id'=>['in',$uid ? [$uid,-2]:[-2]],
            'msg.status'=>1,
            'box.msg_status' =>['in',[0,1]]
        ];
        if($type>0) $where['msg.dtree_type'] = $type;
        if($start_time>0)  $where['msg.send_time'] = ['gt',$start_time];
        $list = Db::table("itboye_message_box")->alias("box")
            ->join(["itboye_message msg",""],"msg.id = box.msg_id","LEFT")
            ->where($where)
            ->limit($page_index * $page_size,$page_size)
            ->order('msg.send_time desc')
            ->select();
        $count = Db::table("itboye_message_box")->alias("box")
            ->join(["itboye_message msg",""],"msg.id = box.msg_id","LEFT")
            ->where($where)
            ->count();
        return returnSuc(['count'=>$count,'list'=>$list]);
    }

}