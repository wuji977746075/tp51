<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 17:10
 */

namespace app\src\user\model;

use think\Model;

class WorkerVerifyApply extends Model
{
    protected $table  = "itboye_worker_verify_apply";
    protected $auto   = ['update_time'];
    protected $insert = ['status' => 0,'create_time'];

    protected function setUpdateTimeAttr()
    {
        return time();
    }
    protected function setCreateTimeAttr()
    {
        return time();
    }
}