<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-05-12 11:12:30
 * Description : [Description]
 */

namespace app\admin\controller;

class Answer extends CheckLogin {

  //   // 答案 - 添加
  // public function add(){
  //   $q_id = $this->_param('q_id',0,'需要题目id');
  //   //? question id
  //   $l = new QuestionLogicV2;
  //   $r = $l->getInfo(['id'=>$q_id]);
  //   !$r && $this->error('错误q_id');

  //   if(IS_GET){
  //     $this->assign('q_id',$q_id);

  //     $type = $l->getAnswerTypeByDtree($r['dt_type']);
  //     $this->assign('type',$type);

  //     return $this->boye_display();
  //   }else{
  //     $title   = $this->_param('title','');
  //     $type    = $this->_param('type','','需要答案类型');
  //     $content = strip_tags(trim($this->_param('content_'.$type,'')));
  //     // check answer title and content
  //     $dt_type = (int) $r['dt_type'];
  //     $this->checkTitleAndContent($dt_type,$title,$content);
  //     // ? img
  //     if($type == 'img'){
  //       !is_numeric($content) && $this->error('需要图片id');
  //     }
  //     if($type == 'bool'){
  //       !in_array($title,['T','F','t','f','1','0']) && $this->error('答案名错误');
  //       $title = strtoupper($title);
  //     }
  //     // check answer type
  //     $r = $l->checkAnswerType($q_id,$type);
  //     !$r && $this->error('答案类型与题型不符 或 无需答案');//or q_id error

  //     $sort      = $this->_param('sort',0);
  //     $is_real   = $this->_param('is_real',0);
  //     $real_sort = $this->_param('real_sort',0);
  //     $add = [
  //       'q_id'      =>$q_id,
  //       'title'     =>$title,
  //       'content'   =>$content,
  //       'type'      =>$type,
  //       'is_real'   =>$is_real,
  //       'sort'      =>$sort,
  //       'real_sort' =>$real_sort,
  //       'add_uid'   =>UID,
  //     ];
  //     !(new AnswerLogicV2)->add($add) && $this->error('添加失败');
  //     $this->success("添加成功"); //,url('question/detail',['id'=>$q_id])
  //   }
  // }

  // //快捷添加真假答案
  // public function addbools(){
  //   $q_id   = $this->_param('q_id',0,'需要题目id');
  //   $q_type = intval($this->_param('q_type',0));
  //   if($q_type){ // 对错
  //     $q_arr = ['1'=>'1','0'=>'0'];
  //   }else{ // 真假
  //     $q_arr = ['T'=>'1','F'=>'0'];
  //   }
  //   //? question id
  //   $l = new QuestionLogicV2;
  //   $r = $l->getInfo(['id'=>$q_id]);
  //   !$r && $this->error('错误q_id');
  //   $is_bool = $l->isBoolAnswer($r['dt_type']);
  //   if(!$is_bool) $this->error('此快捷操作只属于判断类题型');

  //   // $answer = trim($this->_param('answer',''));
  //   // $len = (new AnswerLogicV2)->count(['q_id'=>$q_id,'is_real'=>1]);
  //   $add = [];
  //   foreach ($q_arr as $k=>$v) {
  //     $add[] = [
  //       'q_id'      =>$q_id,
  //       'title'     =>$k,
  //       'content'   =>$v,
  //       'add_uid'   =>UID,
  //       'sort'      =>0,
  //       'is_real'   =>0,
  //       'real_sort' =>0,
  //       'type'      =>'bool',
  //     ];
  //   }
  //   if($add && !(new AnswerLogicV2)->addAll($add)){
  //     $this->success('添加失败');
  //   }
  //   $this->success('添加成功',url('question/detail',['id'=>$q_id]));
  // }
  // //快捷添加key-val答案 - 单选类
  // public function addkvs(){
  //   $q_id = $this->_param('q_id',0,'需要题目id');
  //   //? question id
  //   $l = new QuestionLogicV2;
  //   $r = $l->getInfo(['id'=>$q_id]);
  //   !$r && $this->error('错误q_id');
  //   $is_kv = $l->isKvAnswer($r['dt_type']);
  //   if(!$is_kv) $this->error('此快捷操作只属于部分单选类题目');

  //   $answer = $this->_param('answer','');
  //   $answers = explode('|', $answer);
  //   $len = count($answers);
  //   $i = 0; $adds = [];
  //   foreach ($answers as $v) {
  //     $ans = explode(':', $v,2);
  //     count($ans)<2 && $this->error('答案格式错误,请以逗号分隔答案和内容');
  //     $adds[] = [
  //       'q_id'      =>$q_id,
  //       'title'     =>trim($ans[0]),
  //       'content'   =>trim($ans[1]),
  //       'add_uid'   =>UID,
  //       'sort'      =>0,
  //       'is_real'   =>0,
  //       'real_sort' =>0,
  //       'type'      =>'str',
  //     ];
  //   }
  //   if(empty($adds)) $this->error('需要答案');
  //   if(!(new AnswerLogicV2)->addAll($adds)){
  //     $this->success('添加失败');
  //   }
  //   $this->success('添加成功',url('question/detail',['id'=>$q_id]));
  // }
  // //快捷添加纯字符答案 - 无内容字符串
  // public function addstrs(){
  //   $q_id = $this->_param('q_id',0,'需要题目id');
  //   //? question id
  //   $l = new QuestionLogicV2;
  //   $r = $l->getInfo(['id'=>$q_id]);
  //   !$r && $this->error('错误q_id');
  //   $is_str = $l->isStrAnswer($r['dt_type']);
  //   if(!$is_str) $this->error('此快捷操作只属于无内容字符串类题目');

  //   $answer = trim($this->_param('answer',''));
  //   $answers = explode('|', $answer);
  //   // ? 随机
  //   // $answers = shuffle($answers);

  //   $i = -1;$adds = [];
  //   $len = (new AnswerLogicV2)->count(['q_id'=>$q_id,'is_real'=>1]);
  //   $len = $len + count($answers);
  //   foreach ($answers as $v) {
  //     $i ++;
  //     $adds[] = [
  //       'q_id'      =>$q_id,
  //       'title'     =>$v,
  //       'content'   =>'',
  //       'add_uid'   =>UID,
  //       'sort'      =>$len - $i,
  //       'is_real'   =>1,
  //       'real_sort' =>$len - $i,
  //       'type'      =>'str',
  //     ];
  //   }
  //   if(empty($adds)) $this->error('需要答案');
  //   if(!(new AnswerLogicV2)->addAll($adds)){
  //     $this->success('添加失败');
  //   }
  //   $this->success('添加成功',url('question/detail',['id'=>$q_id]));
  // }

  // //批量添加纯字符答案 - 拼写
  // public function adds(){
  //   $q_id = $this->_param('q_id',0,'需要题目id');
  //   //? question id
  //   $l = new QuestionLogicV2;
  //   $r = $l->getInfo(['id'=>$q_id]);
  //   !$r && $this->error('错误q_id');
  //   $is_alpha = $l->isAlphaAnswer($r['dt_type']);
  //   $is_word = $l->isWordAnswer($r['dt_type']);
  //   if(!$is_word) $this->error('此快捷操作只属于拼写类题目');

  //   $answers = trim($this->_param('answers',''));
  //   $answers = explode('|',$answers);
  //   // ? 随机
  //   // $answers = shuffle($answers);
  //   $adds = [];
  //   $has_len = (new AnswerLogicV2)->count(['q_id'=>$q_id,'is_real'=>1]);
  //   $len = count($answers) + $has_len;
  //   $i = -1;
  //   foreach ($answers as $v) {
  //     $i ++;
  //     $answer = trim($v);
  //     if($answer){
  //       $is_alpha && !preg_match('/^[A-Za-z]$/', $answer) && $this->error('需要每个答案为字母');
  //       $adds[] = [
  //         'q_id'      =>$q_id,
  //         'title'     =>$answer,
  //         'content'   =>'',
  //         'add_uid'   =>UID,
  //         'sort'      =>$len-$i,
  //         'is_real'   =>1,
  //         'real_sort' =>$len-$i,
  //         'type'      =>'str',
  //       ];
  //     }
  //   }
  //   empty($adds) && $this->error('未添加答案');
  //   if(!(new AnswerLogicV2)->addAll($adds)){
  //     $this->success('添加失败');
  //   }
  //   $this->success('添加成功',url('question/detail',['id'=>$q_id]));
  // }
  // //编辑/添加
  // public function edit(){
  //   $id = $this->_param('id',0,'答案id');
  //   $r = (new AnswerLogicV2)->getInfo(['id'=>$id]);
  //   !$r && $this->error('未知答案');
  //   $q_id =  $r['q_id'];

  //   if(IS_GET){
  //     $this->assign('id',$id);
  //     $this->assign('info',$r); //答案信息
  //     $this->assign('q_id',$q_id);

  //     $l = new QuestionLogicV2;
  //     $r = $l->getInfo(['id'=>$q_id]);
  //     !$r && $this->error('错误q_id');
  //     $type = $l->getAnswerTypeByDtree($r['dt_type']);

  //     $this->assign('type',$type);
  //     return $this->boye_display();
  //   }else{
  //     $type    = $this->_param('type','','需要答案类型');
  //     $title   = $this->_param('title','');
  //     $content = strip_tags(trim($this->_param('content_'.$type,'')));

  //     //? question_id
  //     $l = new QuestionLogicV2;
  //     $r = $l->getInfo(['id'=>$q_id]);
  //     !$r && $this->error('错误q_id');
  //     $dt_type = (int) $r['dt_type'];

  //     // title ? id
  //     if($l->getAnswerTypeByDtree($dt_type) == 'img'){
  //       !is_numeric($content) && $this->error('需要图片id');
  //     }
  //     //check answer type
  //     $r = $l->checkAnswerType($q_id,$type);
  //     !$r && $this->error('答案类型与题型不符');//or q_id error
  //     //check answer title and content
  //     $this->checkTitleAndContent($dt_type,$title,$content);

  //     $sort      = $this->_param('sort',0);
  //     $is_real   = $this->_param('is_real',0);
  //     $real_sort = $this->_param('real_sort',0);
  //     $save = [
  //       'title'     =>$title,
  //       'content'   =>$content,
  //       'type'      =>$type,
  //       'is_real'   =>$is_real,
  //       'sort'      =>$sort,
  //       'real_sort' =>$real_sort,
  //     ];
  //     !(new AnswerLogicV2)->save(['id'=>$id,'q_id'=>$q_id],$save) && $this->error('修改失败');
  //     $this->success("修改成功",url('question/detail',['id'=>$q_id]));
  //   }
  // }

  // //检查问题的 答案主体和内容
  // private function checkTitleAndContent($dt_type,$title,$content=''){
  //   $dt_type = (int) $dt_type;
  //   if($dt_type ==6233){ //? 字母
  //     !preg_match('/^[a-zA-Z]{1}$/', $title) && $this->error('此类题型答案为单个字母');
  //   }
  // }

  // //答案 是否真实
  // public function real($id){
  //   $is_real = intval($this->_param('is_real',0));
  //   if(!(new AnswerLogicV2)->saveByID($id,['is_real'=>$is_real])) $this->error('操作失败');

  //   $this->success('操作成功');
  // }

  // //答案 多排序  sort and real_sort
  // public function sort(){
  //   $ids = isset($_POST['ids']) ? $_POST['ids'] : [];
  //   empty($ids) && $this->error('先选择再操作');
  //   foreach ($ids as $v) {
  //       $sort = max(intval($this->_param('sort_'.$v,0)),0);
  //       $real_sort = max(intval($this->_param('real_sort_'.$v,0)),0);
  //       if(!(new AnswerLogicV2)->saveByID($v,['sort'=>$sort,'real_sort'=>$real_sort])) $this->error($v.'排序失败');
  //   }
  //   $this->success("排序成功！");

  // }

  // //答案 - 多真删除
  // public function dels(){
  //   $ids = isset($_POST['ids']) ? $_POST['ids'] : '';
  //   empty($ids) && $this->error(L('ERR_PARAMETERS'));
  //   $ids = implode(',', $ids);
  //   $map = ['id' => ['in', $ids]];
  //   if(!(new AnswerLogicV2)->delete($map)) $this->error('操作失败');

  //   $this->success("删除成功！");
  // }

  // //答案 - 真删除
  // public function del(){
  //   $id = $this->_param('id',0);
  //   !(new AnswerLogicV2)->delete(['id'=>$id]) && $this->error('删除失败');
  //   $this->success("删除成功！");
  // }
}