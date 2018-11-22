<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-14
 * Time: 14:27
 */

namespace app\src\admin\api;


use app\src\admin\api\po\SecurityCodePo;

class SecurityCodeApi extends BaseApi
{
    /**
     * 调用创建验证码接口
     * @param SecurityCodePo $po
     * @return array
     */
    public function create(SecurityCodePo $po){
        $data = [
            'type'=>'By_SecurityCode_create',
            'api_ver'=>'100',
            'notify_id'=>self::getNotifyId(),
            'acceptor'=>$po->getAcceptor(),
            'code_type'=>$po->getCodeType(),
            'code_create_way'=>$po->getCodeCreateWay(),
            'code_length'=>$po->getCodeLength()
        ];

        return $this->callRemote($data);
    }

    public function check(SecurityCodePo $po){

        $data = [
            'type'=>'By_SecurityCode_check',
            'api_ver'=>'100',
            'notify_id'=>self::getNotifyId(),
            'acceptor'=>$po->getAcceptor(),
            'code_type'=>$po->getCodeType(),
            'code'=>$po->getCode()
        ];

        return $this->callRemote($data);
    }
}