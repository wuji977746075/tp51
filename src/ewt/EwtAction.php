<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-01
 * Time: 14:52
 */

namespace src\ewt;

use src\base\BaseAction;
// use app\src\ewt\logic\SurveyLogicV2;
// use app\src\system\logic\DatatreeLogicV2;
// use think\Db;
/**
 * Class SurveyAction
 * 调查问卷
 * @package app\src\ewt\action
 */
class SurveyAction extends BaseAction{

  //问卷查询 -list - array
  public function queryList(){
    $l = new DatatreeLogicV2;
    $r = array_values($l->getCacheList(['code'=>'00P002']));
    foreach ($r as &$v) {
      $v['child'] = array_values($l->getCacheList(['parentid'=>$v['id']],'id,name,code',0,true));
    }
    return $r;
  }

  //问卷提交 - 全部题 - apiReturn
  public function add(array $arr){
    Db::startTrans();
    foreach ($arr as $k=>$v) {
      if(!$this->addOne($k,$v)) return returnErr('fail',true);
    }
    Db::commit();
    return returnSuc('success');
  }
  //问卷提交 - 单题 - bool
  //$dt_question  哪一题
  //$dt_answer 答案
  public function addOne($dt_question=0,$dt_answer=''){
    $l = new SurveyLogicV2;
    !is_array($dt_answer) && $dt_answer = explode(',', $dt_answer);

    foreach ($dt_answer as $v) {
      $map = ['dt_question'=>$dt_question];
      if(!$v) return false;
      $map['dt_answer'] = $v; //注意循环内外
      if($l->getInfo($map)){
        if(!$l->setInc($map,'cnt')) return false;
      }else{
        $map['cnt'] = 1;
        if(!$l->add($map)) return false;
      }
    }
    return true;
  }
}