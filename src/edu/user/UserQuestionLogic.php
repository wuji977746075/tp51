<?php
/**
 * @author rainbow 2016-12-15 15:38:20
 */
namespace src\ewt\logic;

use src\base\logic\BaseLogic;
use src\ewt\model\UserQuestion;

class UserQuestionLogic extends BaseLogic{

  //查询用户的答题题目列表+题目状态
  public function queryWithQuestion(array $map,$order=false,$field=false){
    $model = $this->getModel();
    $list = $model->alias('uq')
    ->join(['itboye_ewe_question'=>'q'],'uq.question_id  = q.id','left')
    ->join(['itboye_ewe_bookunit'=>'b'],'uq.bookunit_id = b.id','left')
    ->where($map)->order($order)->field($field)->select();
    $list = $list ? Obj2Arr($list) : [];
    return $list;
  }
}