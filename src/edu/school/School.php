<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 17:10
 * app 调研
 */

namespace src\ewt\model;


use src\base\BaseModel as Model;

class School extends Model{
  protected $table = "itboye_school";
  protected $insert = ['create_time'];

  public function setCreateTimeAttr(){
      return time();
  }
}