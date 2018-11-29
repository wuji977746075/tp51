<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-03-25 16:33:40
 * Description : [Description]
 */

namespace app\admin\controller;

use app\src\ewt\logic\AnswerLogicV2;
use app\src\ewt\logic\BookunitLogicV2;
use app\src\ewt\logic\BookunitQuestionLogicV2;
use app\src\ewt\logic\QuestionLogicV2;
use app\src\ewt\logic\UserBookLogicV2;
use app\src\ewt\model\UserBook;
use app\src\file\logic\AudioFileLogic;
use app\src\goods\logic\ProductLogic;
use app\src\system\logic\DatatreeLogicV2;

class Question extends Admin{

  // 一个店
  // private $store_id;
  // public function _initialize(){
  //     parent::_initialize();
  //     //从Session 中获取店铺数据
  //     $this->store_id = AdminSessionHelper::getCurrentStoreId();
  //     //从输入获取店铺数据，并赋值 Session
  //     if (empty($this->store_id)) {
  //         $this->store_id = $this->_param('store_id', 0);
  //         AdminSessionHelper::setCurrentStoreId($this->store_id);
  //     }
  //     //从数据库查询店铺数据，并赋值 Session
  //     if (empty($this->store_id)) {
  //         $result = (new StoreLogic())->query();
  //         if ($result['status']) {
  //             $this->store_id = $result['info']['list'][0]['id'];
  //             AdminSessionHelper::setCurrentStoreId($this->store_id);
  //         }
  //     }
  //     //无店铺数据退出
  //     if (empty($this->store_id)) {
  //         $this->error("缺少店铺ID参数！");
  //     }
  // }

  // 添加到 单元
  public function link(){
    $unit_id = $this->_param('unit_id/d',0);
    $q_ids   = $this->_param('q_ids/s','');
    $q_ids   = explode(',', trim($q_ids,','));
    $r = (new BookunitQuestionLogicV2)->addQids($unit_id, $q_ids);
    !$r['status'] && $this->error($r['info'],'');
    $this->success($r['info']);
  }

  // ajax - 查询书籍单元
  public function units(){
    $book_id = $this->_param('book_id/d',0);
    $kword = $this->_param('kword/s','');
    $id    = $this->_param('id/d',0);
    //查询书籍下的全部单元
    $map = ['book_id'=>$book_id,'parent_unit'=>$id];
    if($kword) $map['unit_name'] = ['like','%'.$kword.'%'];
    $r = (new BookunitLogicV2)->queryNoPaging($map,'sort desc,id asc','id,unit_name,level,book_id');
    $this->success('查询成功','',$r);
  }
  // ajax - 查询书籍
  public function books($kword='',$return=false){
    //上架的书籍 //,'store_id'=>$this->$store_id
    $map = ['onshelf'=>1];
    if($kword) $map['name'] = ['like','%'.$kword.'%'];
    $r = (new ProductLogic)->query($map,['curpage'=>1,'size'=>20],false,[],'0 as id,id as book_id,name as unit_name,cate_id');
    if($return){
      return $r;
    }else{
      !$r['status'] && $this->error($r['info']);
      $this->success('查询成功','',$r['info']['list']);
    }
  }

  //题目 - 管理
  public function index(){
    $l = new AnswerLogicV2;
    $q = new QuestionLogicV2;
    $map = [];$params = [];

    $dt_types = (new DatatreeLogicV2)->getCacheList(['parentid'=>DatatreeLogicV2::QUESTION_TYPE]);
    $this->assign('dt_types',$dt_types);
    //父级
    $id  = $this->_param('id',0);
    if($id){
      $parent = $q->getField(['id'=>$id],'parent_id');
      $map['id'] = $id;
    }else{
      $parent = $this->_param('parent',0);
    }
    $this->assign('parent',$parent);
    $map['parent_id'] = $parent;
    $params['parent'] = $parent;
    //分类
    $dt_type = $this->_param('dt_type',0);
    $this->assign('dt_type',$dt_type);
    if($dt_type){
      $map['dt_type'] = $dt_type;
      $params['dt_type'] = $dt_type;
    }
    //搜索词
    $kword = $this->_param('kword','');
    $this->assign('kword',$kword);
    if($kword){
      $params['kword'] = $kword;
      $map['note'] = ['like','%'.$kword.'%'];
    }

    // 是否查询全部
    $all = intval($this->_param('all',0));
    $this->assign('all',$all);
    $params['all'] = $all;
    if($all){
    }else{
      $map['added_uid'] = UID;
    }

    // 查询第一页的书籍
    $r = $this->books('',true);
    !$r['info'] && $this->error($r['info']);
    $this->assign('books',$r['info']['list']);
    //状态
    $status = $this->_param('status',-2);
    $this->assign('status',$status);
    if($status != -2){
      $params['status'] = $status;
      $map['status'] = $status;
    }else{
      $map['status'] = ['in',[0,1]];
    }
    //查询
    $page = ['curpage' => $this->_param('p', 1), 'size' => 10];
    $r = $q->queryWithPagingHtml($map, $page, $parent ? 'sort desc' : 'create_time desc',$params);
    //答案统计 和小题个数
    foreach ($r['list'] as &$v) {
      $q_id = (int) $v['id'];
      $v['child_number'] = 0;
      $v['child_number_public'] = 0;
      $v['answer_number'] = 0;
      $v['answer_number_real'] = 0;
      $type = (int) $v['dt_type'];
      $has_child = intval($q->hasChild($type));
      $v['has_child'] = $has_child;
      if(!$parent && $has_child){ //外层且有小题
        $v['child_number'] = $q->count(['parent_id'=>$q_id,'status'=>['in',[0,1]]]);
        $v['child_number_public'] = $q->count(['parent_id'=>$q_id,'status'=>1]);
      }else{
        $v['answer_number'] = $l->count(['q_id'=>$q_id]);
        $v['answer_number_real'] = $l->count(['q_id'=>$q_id,'is_real'=>1]);
      }
    }
    $this->assign('list',$r['list']);
    $this->assign('show',$r['show']);
    return $this->boye_display();
  }

  //题目 - 设置题面 录音 和 答案
  public function detail(){
    $l = new QuestionLogicV2;
    $id   = $this->_param('id',0);
    $r = $l->getInfo(['id'=>$id]);
    !$r && $this->error('未知id:'.$id);
    //题目dt_type
    $type = (int) $r['dt_type'];
    //内容类型
    $con_type = $l->getContentType($type);
    if(IS_GET){ //view
      $this->assign('id',$id);
      $this->assign('info',$r);
      $this->assign('cancel',url('admin/question/index',['parent'=>$r['parent_id']]));
      $this->assign('dt_type_name',(new DatatreeLogicV2)->getNameById($type,60));
      $this->assign('con_type',$con_type);
      if($con_type == 'img'){
        $imgs = $r['content'] ? explode(',', rtrim($r['content'],',')) : [];
        $this->assign('imgs',$imgs);
      }
      //? 表格
      $this->assign('is_table',($l->getStrType($type)  == 'table')  ? 1 : 0);
      //? 对话
      $this->assign('is_dialog',($l->getStrType($type) == 'dialog') ? 1 : 0);
      //? 答案是否为单词 - 拼写类
      $this->assign('is_word_answer',$l->isWordAnswer($type) ? 1 : 0);
      //? 答案是否为无内容的字符串 - 选词类
      $this->assign('is_str_answer',$l->isStrAnswer($type) ? 1 : 0);
      //? 答案是否为key-val - 单选类
      $this->assign('is_kv_answer',$l->isKvAnswer($type) ? 1 : 0);
      //? 答案是否为t/f - 判断类
      $this->assign('is_bool_answer',$l->isBoolAnswer($type) ? 1 : 0);
      //查询文件信息
      $audio_id = $r['audio_id'];
      $this->assign('audio_id',$audio_id);
      if($audio_id){
        $r = (new AudioFileLogic)->getInfo(['id'=>$audio_id]);
        !$r['status'] && $this->error($r['info']);
        empty($r['info']) && $this->error('音频文件缺失');
        $file = $r['info'];
        $file['duration_desc'] = getTimeDesc($file['duration']);
        $this->assign('file',$file);
      }
      //查询答案 - 按顺序大小
      $r = (new AnswerLogicV2)->queryNoPaging(['q_id'=>$id],'sort desc');
      $this->assign('list',$r);
      //查询真实答案 - 按顺序大小
      $r = (new AnswerLogicV2)->queryNoPaging(['q_id'=>$id,'is_real'=>1],'real_sort desc');
      $real_answers = getArrColumn($r,'title');
      $this->assign('real_answers',$real_answers);

      return $this->boye_display();
    }else{ //save
      //audio + content
      $map = [];
      $audio_id = max(intval($this->_param('audio_id',0)),0);
      $content  = trim($this->_param('content',''));
      $question = trim($this->_param('question',''));
      $map['question'] = $question;
      $is_dialog = ($l->getStrType($type) == 'dialog') ? 1 : 0;
      $is_multi = intval((new QuestionLogicV2)->isMulti($type));
      // if($audio_id)
      $map['audio_id'] = $audio_id;
      if($con_type == 'str'){ // 文本
        if($is_multi){
        }else{ // 非多段结构
          if(false !== strpos($content,'{{tr}}')){
            $this->error('非多段结构题型');
          }
        }
        $content = preg_replace('/^{{tr}}|{{tr}}$/','',$content);
        if($is_dialog){
          //每段必须包含 一个 {{td}}
          $temp = explode('{{tr}}', $content);
          foreach ($temp as &$v) {
            $pos = strpos($v,'{{td}}');
            if(false === $pos){
              $this->error('对话每段必须包含{{td}}');
            }
            $str_l = substr($v, 0,$pos);
            $str_r = substr($v, $pos+6);
            //过滤{{td}}前的标志
            $str_l = preg_replace('/{{co}}|{{br}}|{{tr}}/','',$str_l);
            //过滤{{td}}后的标志
            $str_r = preg_replace('/{{td}}/',' ',$str_r);
            $v = $str_l.'{{td}}'.$str_r;
          } unset($v);
          $content = implode('{{tr}}', $temp);
        }
        // 去strip_tags
        // 换行 => ''
        // $content = nl2br($content);
        // $content = str_replace('<br />', '{{b}}', $content);
      }else{ //图片  '1,2,' ... 信息重复
        //转为 multi int
      }
      $map['content'] = $content;
      !$l->save(['id'=>$id],$map) && $this->error('操作失败');

      $this->success('操作成功');
    }
  }

  //题目 - 添加/修改
  //todo : 小题判断写死了
  //添加 音频间歇 2017-07-31 09:23:04
  public function set(){
    $parent = $this->_param('parent',0);
    $id = $this->_param('id',0); //? 修改
    if(IS_GET){ //view
      $this->assign('parent',$parent);
      $this->assign('id',$id);
      $dt_types = (new DatatreeLogicV2)->getCacheList(['parentid'=>DatatreeLogicV2::QUESTION_TYPE]);
      $this->assign('dt_types',$dt_types);
      if($id){
        $info = (new QuestionLogicV2)->getInfo(['id'=>$id]);
        if(!$info) $this->error('错误id');
        $dt_type = $info['dt_type'];
      }else{
        $dt_type = 0;
        $info = [];
      }
      $this->assign('dt_type',$dt_type);
      $this->assign('info',$info);
      return $this->boye_display();
    }else{ //save
      // $title          = $this->_param('title/s','');
      $dt_type        = $this->_param('dt_type/d',0,'需要dt_type');
      $question       = $this->_param('question/s','');
      $note           = $this->_param('note/s','');
      $status         = $this->_param('status/d',0);
      // $audio_id       = $this->_param('audio_id/d',0);
      $origin_article = $this->_param('origin_article/s','');
      $knowledge      = $this->_param('knowledge/s','');
      $relax          = $this->_param('relax/d',0);
      $istransform    = $this->_param('istransform',0);
      $relax<0 && $this->error('音频间歇非法');
      if($parent){ //? +小题
        $r = (new QuestionLogicV2)->getField(['id'=>$parent],'dt_type');
        if($r != 6227) $this->error('暂只允许完形填空添加小题');
        if($dt_type != 6226) $this->error('完形填空的小题暂只能为单项选择 (文本)');
      }
      $map = [
          'title'          =>'',//$title,
          'question'       =>$question,
          'dt_type'        =>$dt_type,
        // 'audio_id'       =>$audio_id,
          'origin_article' =>$origin_article,
          'knowledge'      =>$knowledge,
          'note'           =>$note,
          'relax'          =>$relax,
      ];
      if($id){ //edit
        if(!(new QuestionLogicV2)->save(['id'=>$id],$map)) $this->error('操作失败');
      }else{ //add
        $map['added_uid'] = UID;
        $map['do_cnt']    = 0;
        $map['audio_id']  = 0;
        $map['come_from'] = '';
        $map['status']    = $status;
        $map['content']   = '';
        $map['parent_id'] = $parent;
        $map['istransform'] = $istransform;
        if(!(new QuestionLogicV2)->add($map)) $this->error('操作失败');
      }
      $this->success('操作成功',url('admin/question/index',['parent'=>$parent]));
    }
  }



  //小题 sort
  public function sort(){
    $ids = isset($_POST['ids']) ? $_POST['ids'] : [];
    empty($ids) && $this->error('先选择再操作');
    foreach ($ids as $v) {
      $sort = max(intval($this->_param('sort_'.$v,0)),0);
      if(!(new QuestionLogicV2)->saveByID($v,['sort'=>$sort])) $this->error($v.'排序失败');
    }
    $this->success("排序成功！");

  }

  //题目 - 多假删除
  public function dels(){
    $ids = isset($_POST['ids']) ? $_POST['ids'] : '';
    empty($ids) && $this->error(L('ERR_PARAMETERS'));
    $ids = implode(',', $ids);
    $map = ['id' => ['in', $ids]];
    if(!(new QuestionLogicV2)->save($map,['status'=>-1])) $this->error('操作失败');

    $this->success("删除成功！");
  }

  //题目 - 变态(0起草/1发布/-1假删除)
  public function status($id=0,$status=-2){
    if(in_array($status,[0,1,-1])){
      if(!(new QuestionLogicV2)->save(['id'=>$id],['status'=>$status])) $this->error('操作失败 或 未知更改');
      $this->success('操作成功');
    }else{
      $this->error('非法操作');
    }
  }

  //题目 - 真删除
  public function delete($id){
    if(!(new QuestionLogicV2)->delete(['id'=>$id])) $this->error('删除失败');
    $this->success('删除成功');
  }


  //批量添加题目
  public function addques(){
    $array=[2,3,4,5,6,7,8,9,10,11,12];
    $answer = ['A-[字母]','evening-晚上','G-[字母]','F-[字母]','E-[字母]','C-[字母]',' afternoon-中午','B-[字母]','HB-铅笔','D-[字母]','CD-光盘'];
    foreach($array as $k=>$v){
      $note = 'S-0024-'.$v;
      $entity = [
          'note'=>$note,
        'title'=>'',
        'question'=>'',
        'come_from'=>'',
        'knowledge'=>'',
        'content'=>'',
        'create_time'=>time(),
        'update_time'=>time(),
        'added_uid'=>'215',
        'dt_type' =>'6203',
        'audio_id'=>'',
        'origin_article'=>$answer[$k],
        'do_cnt'=>0,
        'parent_id'=>0,
        'status'=>1,
        'sort'=>13-$v,
        'relax'=>1
      ];
      (new QuestionLogicV2())->add($entity,'id');
    }
  }



}