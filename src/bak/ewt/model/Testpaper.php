<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 17:10
 * app 调研
 */

namespace app\src\ewt\model;

use think\Model;

class Testpaper extends Model{
  protected $table = "itboye_ewe_testpaper";

  protected $insert = ['create_time','update_time'];
  protected $update = ['update_time'];

  public function setCreateTimeAttr(){
      return time();
  }
  public function setUpdateTimeAttr(){
      return time();
  }
}