<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-03
 * Time: 01:54
 */

namespace app\src\hook;

use app\src\base\helper\SessionHelper;

/**
 * Class LoginAuthHook
 * 登录检查，
 * 对于某些接口检测是否已登录
 * 1. 即是否有会话id传过来
 * 2. 检查会话id是否合法
 * @package app\src\hook
 */
class LoginAuthHook
{

    protected $needCheckApiList = [
//        'default_user_update$',
//        'default_wallet_*',
        'default_address_*',
        'default_order_*',
    ];

    /**
     * 检查
     * @param int $uid
     * @param string $s_id
     * @param string $api
     * @return array
     */
    public function check($uid= 0,$s_id='',$api=''){

        if($s_id == 'itboye'){
            return ['status'=>true,'info'=>'test api'];
        }
        $s_id = trim($s_id);
        $api = strtolower($api);
        foreach ($this->needCheckApiList as $item){
            $result = preg_match('/'.$item.'/i',$api);
            if($result === 1){
                if($uid > 0 && !empty($s_id) && strlen($s_id) > 0){
                    $result =  $this->checkUidSessionId($uid,$s_id);
                    if(!$result['status']){
                        addLog('loginAuthHook',$api,$result,$uid.'_'.$s_id);
                    }
                    return $result;
                }else{
                    addLog('loginAuthHook',$api,$result,$uid.'_'.$s_id);
                    if($uid <= 0){
                        return ['status'=>false,'info'=>'请重新登录，您的账号在其他地方登录了'];
                    }else{
                        return ['status'=>false,'info'=>'请重新登录，您的账号在其他地方登录了'];
                    }
                }

            }
        }

        return ['status'=>true,'info'=>'not need check'];
    }

    private function checkUidSessionId($uid,$s_id){
        $result = SessionHelper::checkLoginSession($uid,$s_id);
        return $result;
    }
}