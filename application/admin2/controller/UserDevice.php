<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 16:49
 */

namespace app\admin\controller;
use app\src\ewt\logic\UserDeviceLogicV2;

/**
 * Class SunsunUserDevice
 * 用户设备
 * @package app\admin\controller
 */
class UserDevice extends Admin
{
    public function index(){
        $uid   = $this->_param('uid', 0);
        $r = (new UserDeviceLogicV2)->queryNoPaging(['uid'=>$uid]);
        $this->assign('device_list',$r);
        return $this->boye_display();
    }

    // ajax
    public function del(){

        $id = $this->_param('id', 0);
        $r = (new UserDeviceLogicV2)->delete(['id'=>$id]);
        !$r && $this -> error('操作失败','');
        $this -> success('操作成功');
    }
}