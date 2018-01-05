<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-03
 * Time: 15:32
 */

namespace app\domain;
use app\src\message\facade\MessageFacade;
use app\src\message\enum\MessageType;
use app\src\bbs\logic\BbsPostLogicV2;
use app\src\bbs\logic\BbsAttachLogicV2;

/**
 * Class MessageDomain
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\domain
 */
class MessageDomain extends BaseDomain{
    /**
     * 消息添加接口
     * @author hebidu <email:346551990@qq.com>
     */
    public function add(){
        $entity = $this->getParams(['extra','msg_type','summary','uid','to_uid','content','title']);

        $facade = new MessageFacade();
        $result = $facade->addMsg($entity);

        $this->exitWhenError($result,true);
    }

    // 清空或删除 消息
    public function clear(){
        $params = $this->parsePost('uid|0|int,msg_type|0|int,msg_id|0|int','');
        $facade = new MessageFacade();
        $result = $facade->clear($params);

        $this->exitWhenError($result,true);
    }

    /**
     * 消息推送接口 - 0=>-2[广播]/u[单播] u=>u[私聊]/-2[咨询]
     * @author rainbow
     */
    public function push(){
        $params = $this->parsePost('uid|0|int,to_uid,msg_type|0|int,content','extra,summary,title,push|0|int,record|1|int,client,repair|0|int,sound');//必记录
        $facade = new MessageFacade();
        $after_open = [];
        if($params['repair']){
            $after_open['repair'] = $params['repair'];
            unset($params['repair']);
        }
        if($params['sound']){
            $after_open['sound'] = $params['sound'];
            unset($params['sound']);
        }
        $result = $facade->pushMsg($params,$after_open);

        $this->exitWhenError($result,true);
    }

    /**
     * 消息查询接口
     * @author hebidu <email:346551990@qq.com>
     */
    public function query(){
        $uid        = $this->_post('uid',0,lang('uid_need'));
        $msg_type   = $this->_post('msg_type','',lang('type_need'));
        $page_index = $this->_post('page_index',1);
        $page_size  = $this->_post('page_size',10);
        $facade = new MessageFacade();

        $r = $facade->query($uid,$msg_type,$page_index,$page_size);
        if($r['status'] && $msg_type == MessageType::BBS){
            foreach ($r['info']['list'] as &$v) {
                $id = $v['extra'];
                $v['from_name'] = get_nickname($v['from_id']);
                $img = intval((new BbsAttachLogicV2)->getOneField(['pid'=>$id,'rid'=>0],'img'));
                $v['post_img'] = $img;
                if($img){
                    $v['post_content'] = '';
                }else{
                    $v['post_content'] = BbsPostLogicV2::subPureContent((new BbsPostLogicV2)->getOneField(['id'=>$id],'content'));
                }
            } unset($v);
        }
        $this->exitWhenError($r,true);

    }
}