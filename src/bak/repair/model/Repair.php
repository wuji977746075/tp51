<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 17:10
 */

namespace app\src\repair\model;


use think\Model;

class Repair extends Model
{
    protected $table = "itboye_repair";
    protected $auto   = ['update_time'];
    protected $insert = ['status' => 0,'repair_status'=>0,'create_time'];

    protected function setUpdateTimeAttr(){
      return time();
    }

    protected function setCreateTimeAttr(){
      return time();
    }
}