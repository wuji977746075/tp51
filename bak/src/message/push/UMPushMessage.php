<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-17
 * Time: 14:06
 */

namespace app\src\message\push;


use app\src\extend\umeng\BoyePushApi;
use app\src\message\enum\MessageType;
use app\src\message\interfaces\IMessage;

/**
 * 友盟推送消息
 * Class UMPushMessage
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\message\push
 */
class UMPushMessage implements IMessage
{

    /**
     * @return mixed
     */
    function create()
    {
        
    }

    /**
     * @return mixed
     */
    function send()
    {
        if(!(new MessageType())->isLegalId($msg_type)) return returnErr(Linvalid('msg_type'));

        $BoyePushApi = new BoyePushApi();
        $after_open  = [
            'type'  => 'go_activity',
            'param' => $msg_type,
            'extra' => ['id'=>$msg_id,'uid'=>$to_uid]
        ];
        $body = [
            'ticker' =>$title,
            'alert'  =>$content,
            'title'  =>$title,
            'text'   =>$content
        ];
        if($pushAll){
            $r = $BoyePushApi->sendAll($body,$after_open,$client);
        }else{
            $r = $BoyePushApi->send($to_uid,$body,$after_open,$client);
        }
    }

}