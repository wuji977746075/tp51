<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-03
 * Time: 15:11
 */

namespace app\src\message\enum;


class MessageType
{
    /**
     * 系统消息
     */
    const SYSTEM = 6042;
    const SYSTEM_MESSAGE = "system_message";
    /**
     * 维修消息
     */
    const REPAIR = 6180;
    const REPAIR_MESSAGE = "repair_message";
    /**
     * 其它消息
     */
    const OTHER = 6074;
    const OTHER_MESSAGE = "other_message";

    /**
     * 推送消息
     */
    const PUSH = 6010;
    const PUSH_MESSAGE = "push_message";
    /**
     * 订单消息
     */
    const ORDER = 6043;
    const ORDER_MESSAGE = "order_message";
    /**
     * 物流消息
     */
    const LOGISTICS = 6048;
    const LOGISTICS_MESSAGE = "logistics_message";
    /**
     * 私信
     */
    const PERSONAL = 6078;
    const PERSONAL_MESSAGE = "personal_message";
    /**
     * 论坛
     */
    const BBS = 6221;
    const BBS_MESSAGE = "bbs_message";

    public function isLegalId($id){
        return in_array($id,[self::SYSTEM,self::OTHER,self::REPAIR,self::BBS]);
    }

    public function getMsgTypeString($id){
        $id = intval($id);
        if($this->isLegalId($id)){
            if($id === self::SYSTEM)    return self::SYSTEM_MESSAGE;
            elseif($id === self::PUSH)  return self::PUSH_MESSAGE;
            elseif($id === self::ORDER) return self::ORDER_MESSAGE;
            elseif($id === self::LOGISTICS) return self::LOGISTICS_MESSAGE;
            elseif($id === self::PERSONAL)  return self::PERSONAL_MESSAGE;
            elseif($id === self::REPAIR) return self::REPAIR_MESSAGE;
            elseif($id === self::OTHER)  return self::OTHER_MESSAGE;
            elseif($id === self::BBS)    return self::BBS_MESSAGE;
            else return '';
        }else{
            return '';
        }
    }
}