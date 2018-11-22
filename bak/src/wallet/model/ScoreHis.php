<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-06
 * Time: 10:05
 */

namespace app\src\wallet\model;


use think\Model;

class ScoreHis extends Model{

  protected $table = 'itboye_score_his';
  protected $insert = ['create_time'];

  protected function setCreateTimeAttr(){
    return time();
  }
}