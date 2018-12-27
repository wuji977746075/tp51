<?php
/**
 * 题目 答案
 *2017-03-25 17:03:16
 */

namespace src\ewt\ewt;

use src\base\BaseModel as Model;

class Answer extends Model{
  protected $table = "f_ewt_answer";
  protected $insert = ['create_time','update_time'];
  protected $update = ['update_time'];

  public function setCreateTimeAttr(){
      return time();
  }
  public function setUpdateTimeAttr(){
      return time();
  }
}