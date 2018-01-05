<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/7
 * Time: 14:49
 */

namespace app\src\user\action;


use app\src\base\action\BaseAction;
use app\src\base\facade\AccountFacade;
use app\src\base\helper\ResultHelper;
use app\src\user\facade\DefaultUserFacade;
use app\src\user\logic\UcenterMemberLogic;

class ResetPwdAction extends BaseAction
{
    public function reset($id,$newPwd = 'itboye'){
        $result = (new UcenterMemberLogic())->getInfo(['id'=>$id]);

        $userInfo = $result['info'];
        
        if(is_array($userInfo)){
            $result = (new AccountFacade(new DefaultUserFacade()))->updatePwd(['id'=>$id],$newPwd);

            return $result;
        }
        return ResultHelper::error('重置失败');
    }
}