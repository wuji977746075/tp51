<?php
/**
 * Created by PhpStorm.
 * User: boye
 * Date: 2017/3/1
 * Time: 9:23
 */

namespace app\weixin\controller;


use app\src\task\logic\RoleTaskLogic;
use app\src\user\logic\UserMemberLogic;
use app\src\wallet\logic\WalletLogic;
use think\Db;

class Task {

    //定时任务

    public function Task(){
        $task=(new RoleTaskLogic())->deal_task();
    }



}



