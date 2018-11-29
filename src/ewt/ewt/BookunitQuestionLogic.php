<?php
/**
 * @author rainbow 2017-03-25 17:03:01
 */
namespace src\ewt\ewt;

use think\Db;
use src\base\BaseLogic;
// use src\extend\Page;
// use src\ewt\logic\QuestionLogicV2;
// use src\ewt\model\BookunitQuestion;

class BookunitQuestionLogic extends BaseLogic{
  // 单元 添加 题目
  // 可以重复添加 2017-08-21 15:38:44
  public function addQids($unit=0,array $ids){
    if(!$unit || !$ids) return returnErr('参数错误');

    $suc = '';$err = '';
    foreach ($ids as $v) {
      $id = intval($v);
      // ? bid 发布
      $r = (new QuestionLogicV2)->getInfo(['id'=>$id]);
      if(empty($r)){
        return returnErr('题目错误');
      }
      if(intval($r['status']) != 1){
        return returnErr('需要发布状态');
      }
      $q_type = intval($r['dt_type']);//要添加的题目类型
      // ? 单元的题型 - 已指定了吗
      // $r = (new BookunitQuestionLogicV2)->getQuestionType(['unit_id'=>$unit]);
      // if($r && $r!=$q_type){
      //    return returnErr('同单元需要相同的题目类型');
      // }
      // 可以重复添加
      // if((new BookunitQuestionLogicV2)->getInfo(['unit_id'=>$unit,'question_id'=>$id])){
      //   $err = $v.'(重复),';
      // }else{
        $entity = [
          'unit_id'     => $unit,
          'question_id' => $id,
          'sort'        => 0,
        ];
        if((new BookunitQuestionLogicV2)->add($entity)){
          $suc .= $v.',';
        }else{
          $err = $v.'(错误),';
        }
      }
      return returnSuc(($suc ? '成功:'.$suc: '').($err ? '错误:'.$err: ''));
  }
  // 获取单元的题目类型
  public function getQuestionType(array $map){
    $model = $this->getModel();
    $r = $model->alias('bq')->join(['itboye_ewe_question'=>'q'],'q.id = bq.question_id','left')->field('q.dt_type')->where($map)->find();
    if($r){
      $r = $r->getData();
      return (int) $r['dt_type'];
    }
    return 0;
  }

  // 查询问题详情 含小题和答案
  public function getInfoWithQuestion(array $map,$order = 'q.sort desc',$field='q.*'){
    $map['q.status'] = 1;
    $query = $this->getModel()->alias('bq')
      ->join(["itboye_ewe_question"=>"q"],"q.id = bq.question_id","LEFT")
      ->field($field)->where($map);
    $list = $query -> find();
    $list = $list ? obj2Arr($list) : [];
    $q = new QuestionLogicV2;
    foreach ($list as &$vv) {
      $q_id    = $vv['id'];
      $dt_type = $vv['dt_type'];
      $r = $q->parseContent($vv['content'],$dt_type);
      if(!$r['status']) return $r;
      $vv['content'] = $r['info'];
      //附加答案
      $vv = array_merge($vv,$q->getAnswers($q_id));
      //? 小题
      $vv['child'] = [];
      if($dt_type == 6227){ //完型填空
        $r = $q->getChildren(['parent_id'=>$q_id,'status'=>1],false);
        if(!$r['status']) return $r;
        $vv['child'] = $r['info'];
      }
    }
    return returnSuc($list);
  }

  // 查询单元下的问题列表 含小题和答案 题号1+
  public function queryWithAllQuestion(array $map,$order = 'bq.sort desc',$field='q.*,a.path as audio_path,a.duration as audio_duration'){
    $map['q.status'] = 1;
    $query = $this->getModel()->alias('bq')
      ->join(["itboye_ewe_question"=>"q"],"q.id = bq.question_id","LEFT")
      ->join(["itboye_audio_file"=>"a"],"a.id = q.audio_id","LEFT")
      ->field($field)->where($map)->order($order);
    $list = $query -> select();
    $list = $list ? obj2Arr($list) : [];
    $q = new QuestionLogicV2;
    $i = 0;
    foreach ($list as &$vv) {
      $i ++;
      $vv['title'] = $i; //题号
      $q_id    = $vv['id'];
      $dt_type = $vv['dt_type'];
      $r = $q->parseContent($vv['content'],$dt_type);
      if(!$r['status']) return $r;
      $vv['content'] = $r['info'];
      $vv['audio_path'] = $vv['audio_id'] ? config('site_url').$vv['audio_path'] : '';
      //附加答案
      $vv = array_merge($vv,$q->getAnswers($q_id));
      //? 小题
      $vv['child'] = [];
      if($dt_type == 6227){ //完型填空
        $r = $q->getChildren(['parent_id'=>$q_id,'status'=>1],'sort desc');
        if(!$r['status']) return $r;
        $vv['child'] = $r['info'];
      }
    }
    return returnSuc($list);
  }

  private function getQuery(){

    $query = Db::table("itboye_ewe_bookunit_question")->alias("bkq")
      ->field("bkq.sort,que.question,que.title,bkq.id as id,que.dt_type,que.note,que.status,bkq.question_id")
        ->join(["itboye_ewe_question"=>"que"],"que.id = bkq.question_id","LEFT");
    return $query;
  }

  public function queryNoPagingWithQuestion($map = null,$order=false,$field=false){
    $field = $field ? $field : "bq.sort,q.question,q.title,bq.id as id,q.dt_type,q.note";
    $query = Db::table("itboye_ewe_bookunit_question")->alias("bq")
        ->join(["itboye_ewe_question"=>"q"],"q.id = bq.question_id","LEFT")
        ->field($field);
    $query = $query->where($map)->order($order)->select();
    return $query;
  }

  public function queryWithPagingHtml($map = null, $page = ['curpage' => 1, 'size' => 10], $order = false, $params = false, $fields = false)
  {
    $query = $this->getQuery();
    if(!is_null($map)) $query = $query->where($map);
    if(false !== $order) $query = $query->order($order);
    $start = max(intval($page['curpage'])-1,0)*intval($page['size']);
    $list = $query
        //-> limit($start,$page['size'])
        -> select();
    $query = $this->getQuery();
    $count = $query -> where($map) -> count();

    // 查询满足要求的总记录数
    $Page = new Page($count, $page['size']);

    //分页跳转的时候保证查询条件
    if ($params !== false) {
        foreach ($params as $key => $val) {
            $Page -> parameter[$key] = urlencode($val);
        }
    }
    // 实例化分页类 传入总记录数和每页显示的记录数
    $show = $Page -> show();
    return (["count"=>$count, "list" => $list ,"show" => $show]);
  }

}