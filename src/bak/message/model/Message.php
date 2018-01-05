<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-03
 * Time: 15:06
 */

namespace app\src\message\model;


use think\Model;

class Message extends Model
{
    /**
     * 公告消息
     */
    const TYPE_FOR_SYSTEM = 0;

    /**
     * 故障消息
     */
    const TYPE_FOR_FAULT= 1;

    /**
     * 注册成功消息
     */
    const TYPE_FOR_REGISTER_SUCCESS = 2;

    const MESSAGE_PUSH    = "推送消息";
    const MESSAGE_SYSTEM  = "系统消息";
    const MESSAGE_ORDER   = "订单通知";
    const MESSAGE_EXPRESS = "物流通知";
    const MESSAGE_PRIZE   = "中奖消息";
    const MESSAGE_OTHER   = "其他消息";
    const MESSAGE_CALL    = "私信消息";
    const MESSAGE_BBS     = "论坛消息";

    const MESSAGE_SYSTEM_ACTIVITY = 'system_message';
    const MESSAGE_ORDER_ACTIVITY  = 'order_message';
    const MESSAGE_BBS_ACTIVITY    = 'bbs_message';
    const MESSAGE_CALL_ACTIVITY   = 'call_message';

}