<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-13
 * Time: 16:21
 */

namespace app\admin\controller;

use app\src\admin\api\po\SecurityCodePo;
use app\src\admin\api\SecurityCodeApi;
use app\src\admin\controller\BaseController;
use app\src\admin\helper\AdminSessionHelper;
use app\src\securitycode\enum\CodeCreateWayEnum;
use app\src\securitycode\model\SecurityCode;
use app\src\verify\think\DbVerify;

class Verify extends BaseController {

    /**
     * 创建验证码
     */
    public function random(){

        $session_id = AdminSessionHelper::getSessionId();
        $po = new SecurityCodePo();

        $po->setAcceptor($session_id);
        $po->setCodeCreateWay(CodeCreateWayEnum::ALPHA_AND_NUMBER);
        $po->setCodeLength(4);
        $po->setCodeType(SecurityCode::TYPE_FOR_LOGIN);

        $api = new SecurityCodeApi();
        $result = $api->create($po);
        if($result['status']){
            $verify = new DbVerify();
            $verify->entry($result['info']);
        }else{
            dump($result['info']) ;
            exit;
        }
    }

}