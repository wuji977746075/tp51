<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-20
 * Time: 11:07
 */

namespace app\admin\controller;


use app\src\i18n\helper\LangHelper;
use app\src\session\logic\LoginSessionLogic;
use app\src\user\action\UserLogoutAction;
use app\src\user\logic\MemberConfigLogic;

class LoginSession extends Admin
{
    public function index(){
        $uid = $this->_param('uid','',LangHelper::lackParameter('uid'));

        $result = (new LoginSessionLogic())->queryNoPaging(['uid'=>$uid],"expire_time desc");

        $this->assign('list',$result['info']);
        $result = (new MemberConfigLogic())->getInfo(['uid'=>$uid]);
        $this->assign('member_cfg',$result['info']);
        return $this->boye_display();
    }


    public function remove(){
        $uid = $this->_param('uid','',LangHelper::lackParameter('uid'));
        $s_id = $this->_param('s_id','',LangHelper::lackParameter('s_id'));

        $result = (new UserLogoutAction())->logout($uid,$s_id);
        if($result['status']){
            $this->success('操作成功');
        }else{
            $this->success($result['info']);
        }
    }
}