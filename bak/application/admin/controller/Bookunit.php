<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace app\admin\controller;

use app\src\ewt\logic\BookunitLogicV2;
use app\src\category\logic\CategoryPropLogic;
use app\src\ewt\logic\BookunitQuestionLogicV2;
use app\src\ewt\logic\QuestionLogicV2;
use app\src\system\logic\DatatreeLogicV2;

use app\src\goods\logic\ProductLogic;
use app\src\file\logic\AudioFileLogic;

class Bookunit extends Admin{

  // 单元排序
  public function sort(){
    $pid    = $this->_param('pid/d',0);
    $parent = $this->_param('parent/d',0);
    $id     = $this->_param('id/d',0);
    $type   = $this->_param('type/s','');
    $level  = $this->_param('level/d',0);
    if(!in_array($type, ['up','down'])) $this->error('非法操作');
    $up = ($type == 'up' ? true : false);

    // 查询同级单元
    $r = (new BookunitLogicV2)->queryNoPaging(['parent_unit'=>$parent,'book_id'=>$pid],'sort desc','id');
    if(count($r)>1){
      $ids = getArrColumn($r,'id');
      if(!in_array($id, $ids)) $this->error('id错误');
      // 重新排序
      $index = array_search($id, $ids);
      $len = count($ids);
      $change = false;
      if($up){ // 交换前一个
        if($index-1 >= 0){
          $change = true;
          $ids[$index]   = $ids[$index] ^ $ids[$index-1];
          $ids[$index-1] = $ids[$index] ^ $ids[$index-1];
          $ids[$index]   = $ids[$index] ^ $ids[$index-1];
        }
      }else{ // 交换后一个
        if($index+1 < $len){
          $change = true;
          $ids[$index]   = $ids[$index] ^ $ids[$index+1];
          $ids[$index+1] = $ids[$index] ^ $ids[$index+1];
          $ids[$index]   = $ids[$index] ^ $ids[$index+1];
        }
      }
      if($change){
        // 修改sort
        foreach ($ids as $k => $v) {
          (new BookunitLogicV2)->save(['id'=>$v],['sort'=>$len-$k]);
        }
      }
    }
    $this->success('操作成功',url('bookunit/index',['id'=>$id,'pid'=>$pid,'parent'=>$parent,'level'=>$level]));
  }

  // 单元管理 - 列表
  // rsp + : is_tip is_rand sort : 2017-08-11 09:44:17
  public function index(){
    // $params = [];
    $pid = $this->_param('pid/d',0);
    $this->assign('pid',$pid);

    $id = $this->_param('id/d',0);
    $this->assign('id',$id);

    $level = $this->_param('level/d',0);
    $this->assign('level',$level);
    //书名
    $r = (new ProductLogic)->getInfo(['id'=>$pid]);
    !$r['status'] && $this->error($r['info']);
    if(empty($r['info'])) $this->error('id错误');
    $this->assign('book_name',$r['info']['name']);

    $parent = $this->_param('parent/d',0);
    $this->assign('parent',$parent);
    // $map = [
    //     'parent_unit'=>$parent,
    //     'book_id'=>$pid,
    // ];
    // $name = $this->_param('name','');
    // $this->assign('name',$name);
    // if(!empty($name)){
    //     $map['unit_name'] = ['like',"%$name%"];
    //     $params['name'] = $name;
    // }
    // $u = new BookunitLogicV2;
    // if($parent!=0) {
    //     $par = $u->getInfo(['id' => $parent]);
    //     $preparent = $par['parent_unit'];
    //     $level = $par['level']+1;
    // }else{
    //     $preparent=0;
    //     $level=1;
    // }
    // $this->assign('level',$level);
    // $this->assign('preparent',$preparent);
    // $page = ['curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS')];
    // $order = false;
    // $r = $u ->queryWithPagingHtml($map,$page,$order,$params);
    // $this->assign('show',$r['show']);
    // $this->assign('list',$r['list']);

    //查所有单元
    $units = $this->getUnitTree($pid,$parent,$id);
    $units = json_encode($units);
    $this->assign('units',$units);

    return $this->boye_display();
  }

  /**
   * [getUnitTree description]
   * @Author
   * @DateTime 2017-05-05T10:31:16+0800
   * @param    int $pid    [description]
   * @param    int $select [1/2级- unit_id]
   * @param    int $unit   [选中的- unit_id]
   * @return   arr [description]
   */
  private function getUnitTree($pid,$select=0,$unit=0){
    $level = 0;$parent = 0;
    $u = new BookunitLogicV2;
    $q = new BookunitQuestionLogicV2;
    $r = $u->queryNoPaging(['book_id'=>$pid],'sort desc,id asc','id,parent_unit as parent,level,unit_name as text,has_answer,is_free,time_limit,is_tip,is_rand,sort');
    $d = [];
    $r = changeArrayKey($r,'id');

    // test字符串需要转义
    foreach ($r as &$v) {
      $v['tags']  = ["序".$v['sort']];
      $v['state'] = [];
      // $v['text'] = str_replace("'", "&#039;", $v['text']); //&#039;
      $v['text'] = htmlentities($v['text'],ENT_QUOTES);
      $id = $v['id'];
      if($select && $id == $select){
        $level  = $v['level'];
        $parent = $v['parent'];
      }
      if($v['level'] == 3){
        $v['que'] = $q->count(['unit_id'=>$id]);
      }
      if($unit && $id == $unit){
        $v['state']['selected'] = true;
      }
    } unset($v);
    foreach ($r as $v) {
      if($v['level']==1 && $v['parent']==0){
        $id = $v['id'];
        $v['nodes'] = [];
        if($level && ($select==$id || $parent==$id)){
        }else{
          $v['state']['expanded'] = false;
        }
        $d[$id] = $v;
      }
    }
    foreach ($r as $v) {
      if($v['level']==2){
        $id = $v['id'];
        $v['nodes'] = [];
        if($level && ($select==$id || $parent==$id)){
        }else{
          $v['state']['expanded'] = false;
        }
        $parent = $v['parent'];
        $d[$parent]['nodes'][$id] = $v;
      }
    }
    foreach ($r as $v) {
      if($v['level']==3){
        $parent = $v['parent'];
        $top = $r[$parent]['parent'];
        $v['is_free'] && $v['tags'][] = '免费';
        $v['has_answer'] && $v['tags'][] = '提交';
        $v['tags'][] = $v['time_limit'] ? $v['time_limit']."秒":"不限时";
        $v['is_tip'] &&  $v['tags'][] = "播报";
        $v['is_rand'] &&  $v['tags'][] = "随机";
        if(isset($v['que']) && $v['que']) $v['tags'][] = $v['que'].'题';
        $d[$top]['nodes'][$parent]['nodes'][] = $v;
      }
    }
    foreach ($d as &$v) {
      if($v['nodes']){
        $v['tags'][]  = '◤'.count($v['nodes']);
        $v['nodes'] = array_values($v['nodes']);
        foreach ($v['nodes'] as &$vv) {
          if($vv['nodes']){
            $vv['tags'][]  = '▼'.count($vv['nodes']);
          }else{
            unset($vv['nodes']);
            // unset($vv['state']);
          }
        }
      }else{
        unset($v['nodes']);
        // unset($v['state']);
      }
    } unset($v);
    return array_values($d);
  }
  /**
   * 单元添加
   * + is_rand is_tip sort : 2017-08-11 09:28:53
   * + audio_id : 2017-08-14 09:32:53
   */
  public function add(){
    if(IS_GET){
      $parent = $this->_param('parent/d',0);
      $pid    = $this->_param('pid/d',0);
      $level  = $this->_param('level/d',0);
      if($level >2) $this->error('最多三级单元');
      $this->assign('pid',$pid);
      $this->assign('level',$level);
      $this->assign('parent',$parent);
      return $this->boye_display();
    }else{
      $parent = $this->_param('parent/d',0);
      $pid    = $this->_param('pid/d',0);
      $level  = $this->_param('level/d',0);
      $is_rand = $this->_param('is_rand/d',0);
      $is_tip  = $this->_param('is_tip/d',1);
      $sort    = $this->_param('sort/d',0);
      $audio_id = $this->_param('audio_id/d',0);
      if($level >2) $this->error('最多三级单元');
      $has_answer = $this->_param('has_answer/d',0);
      $name = addslashes(strip_tags(trim($this->_param('name/s','','需要单元名称'))));
      !in_array($has_answer,[0,1]) && $this->error('是否需要答案?');
      !in_array($is_tip,[0,1]) && $this->error('是否播报题号?');
      !in_array($is_rand,[0,1]) && $this->error('是否打乱返回?');
      $entity = [
        'level'       =>$level+1,
        'book_id'     =>$pid,
        'unit_name'   =>$this->_param('name'),
        'parent_unit' =>$parent,
        'is_free'     =>$this->_param('is_free'),
        'price'       =>0,
        'has_answer'  =>$has_answer,
        'time_limit'  =>$this->_param('time_limit/d',0),
        'is_rand'     =>$is_rand,
        'is_tip'      =>$is_tip,
        'sort'        =>$sort,
        'audio_id'    =>$audio_id,
      ];
      if((new BookunitLogicV2)->add($entity)){
        $this->success("添加成功！",url('Admin/Bookunit/index',['parent'=>$parent,'pid'=>$pid]));
      }else{
        $this->error('添加失败');
      }
    }
  }


  /**
   * 单元编辑
   * + is_rand is_tip sort : 2017-08-11 09:39:52
   * + audio_id : 2017-08-14 09:32:53
   */
  public function edit(){
    if(IS_GET){

      $parent  = $this->_param('parent/d',0);
      $book_id = $this->_param('pid/d',0);
      $id      = $this->_param('id/d',0);
      $this->assign('pid',$book_id);
      $this->assign('parent',$parent);

      $logic = new BookunitLogicV2;
      if($parent!=0) {
          $par = $logic->getInfo(['id' => $parent]);
          $preparent = $par['parent_unit'];
          $level = $par['level'];
      }else{
          $preparent = 0;
          $level = 0;
      }
      $this->assign('level',$level);
      $this->assign('preparent',$preparent);

      $info = $logic->getInfo(['id'=>$id]);
      $audio_id = $info ? $info['audio_id'] : 0;
      if($audio_id){
        $r = (new AudioFileLogic)->getInfo(['id'=>$audio_id]);
        !$r['status'] && $this->error($r['info']);
        empty($r['info']) && $this->error('音频文件缺失');
        $file = $r['info'];
        $file['duration_desc'] = getTimeDesc($file['duration']);
        $this->assign('file',$file);
      }
      $this->assign("info",$info);

      return $this->boye_display();
    }else{
      $parent  = $this->_param('parent/d',0);
      $book_id = $this->_param('pid/d',0);
      $id      = $this->_param('id/d',0);
      $audio_id = $this->_param('audio_id/d',0);

      $has_answer = $this->_param('has_answer/d',0);
      !in_array($has_answer,[0,1]) && $this->error('是否需要答案?');
      $is_free = $this->_param('is_free/d',0);
      !in_array($is_free,[0,1]) && $this->error('是否免费体验?');
      $is_rand = $this->_param('is_rand/d',0);
      !in_array($is_rand,[0,1]) && $this->error('是否打乱返回?');
      $is_tip = $this->_param('is_tip/d',1);
      !in_array($is_tip,[0,1]) && $this->error('是否播报题号?');
      $sort = $this->_param('sort/d',0);
      $e = [
        'unit_name'  => $this->_param('name/s','需要单元名称'),
        'is_free'    => $is_free,
        'has_answer' => $has_answer,
        'is_tip'     => $is_tip,
        'is_rand'    => $is_rand,
        'time_limit' => $this->_param('time_limit/d',0),
        'sort'       => $sort,
        'audio_id'   => $audio_id,
      ];
      if((new BookunitLogicV2)->save(['id'=>$id],$e)){
        $this->success("编辑成功！",url('Admin/Bookunit/index',['parent'=>$parent,'pid'=>$book_id]));
      }else{
        $this->error('修改失败');
      }
    }
  }

  //单元删除
  public function delete(){
    $parent  = $this->_param('parent',0);
    $book_id = $this->_param('pid',0);
    $id      = $this->_param('id',0);

    //? id
    $r = (new BookunitLogicV2)->getInfo(['parent_unit'=>$id]);
    $r && $this->error('请先删除子类目!');
    //? 子类
    // $r['count'] > 0 && $this->error("请先删除子类目!");
    //? 有题目
    $r = (new BookunitQuestionLogicV2)->getInfo(['unit_id'=>$id]);
    $r && $this->error('请先删除类目下的题目关联!');

    $r = (new BookunitLogicV2)->delete(['id'=>$id]);
    !$r && $this->error('删除失败!');

    $this->success("删除成功！",url('Admin/Bookunit/index',['parent'=>$parent,'pid'=>$book_id]));
  }

  //显示用户题库 - 添加单元题目关联
  public function question(){
    if(IS_GET){
      $map = [];$params = [];

      $all = $this->_param('all',0);
      $this->assign('all',$all);
      $params['all'] = $all;

      // 单元id
      $id  = $this->_param('id',0);
      $this->assign('id',$id);
      $params['id'] = $id;
      //排除该单元下已有的题目
      // $r = (new BookunitQuestionLogicV2)->queryNoPaging(['unit_id'=>$id],false,'question_id');
      // if($r){
      //     $ids = '';
      //     foreach ($r as $v) {
      //         $ids .= ($ids ? ',':'').$v['question_id'];
      //     }
      //     if($ids) $map['id'] = ['not in',$ids];
      // }
      $book_parent = $this->_param('book_parent',0);
      $this->assign('book_parent',$book_parent);
      $params['book_parent'] = $book_parent;

      $book_id = $this->_param('pid',0);
      $this->assign('pid',$book_id);
      $params['pid'] = $book_id;

      $dt_types = (new DatatreeLogicV2)->getCacheList(['parentid'=>DatatreeLogicV2::QUESTION_TYPE]);
      $this->assign('dt_types',$dt_types);
      //父级
      $parent = $this->_param('parent',0);
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
      //状态
      // $status = $this->_param('status',-1);
      // $this->assign('status',$status);
      // if($status != -1){
      //     $params['status'] = $status;
      //     $map['status'] = $status;
      // }
      $map['status'] = 1;
      if($all){

      }else{
        $map['added_uid'] = UID;
      }
      // $map['parent_id'] = 0;
      //查询
      $page = ['curpage' => $this->_param('p', 1), 'size' => 10];
      $r = (new QuestionLogicV2)->queryWithPagingHtml($map, $page, 'id desc',$params);
      $this->assign('list',$r['list']);
      $this->assign('show',$r['show']);

      return $this->boye_display();
    }else{
      $id   = $this->_param('question_id',0);
      $unit = $this->_param('unit_id',0);

      // $book_parent = $this->_param('book_parent',0);
      // $book_id= $this->_param('pid',0);
      $r = (new BookunitQuestionLogicV2)->addQids($unit,[$id]);
      !$r['status'] && $this->error($r['info']);
      $this->success($r['info']);
    }
  }

  //单元下的题目 - 列表
  public function edquestion(){
    if(IS_GET) {
      $id = $this->_param('id',0);
      $this->assign('unit_id',$id);
      $unit_name = (new BookunitLogicV2)->getField(['id'=>$id],'unit_name');
      if(!$unit_name) $this->error('单元错误');
      $this->assign('unit_name',$unit_name);

      $book_parent = $this->_param('book_parent',0);
      $this->assign('book_parent',$book_parent);

      $book_id= $this->_param('pid',0);
      $this->assign('pid',$book_id);

      $dt_types = (new DatatreeLogicV2)->getCacheList(['parentid'=>DatatreeLogicV2::QUESTION_TYPE]);
      $this->assign('dt_types',$dt_types);

      $p = $this->_param('p',1);
      $map = ['unit_id' => $id];
      $r = (new BookunitQuestionLogicV2)->queryWithPagingHtml($map,['curpage'=>$p,'size'=>10],'sort desc',['id'=>$id,'book_parent'=>$book_parent,'pid'=>$book_id]);

      $this->assign('list',$r['list']);
      $this->assign('show',$r['show']);

      return $this->boye_display();
    }
  }

  //排序 - multi
  public function qsort(){
    $ids = isset($_POST['ids']) ? $_POST['ids'] : [];
    empty($ids) && $this->error('先选择再操作');
    foreach ($ids as $v) {
        $sort = max(intval($this->_param('sort_'.$v,0)),0);
        if(!(new BookunitQuestionLogicV2)->saveByID($v,['sort'=>$sort])) $this->error($v.'排序失败');
    }
    $this->success("排序成功！");
  }

  //删除 - 真
  public function qdel(){
    $id = $this->_param('id',0);
    if(!(new BookunitQuestionLogicV2)->delete(['id'=>$id])) $this->error('删除失败');

    $this->success("删除成功！");
  }

  //删除 - 真 multi
  public function bulkqdel(){
    $ids = isset($_POST['ids']) ? $_POST['ids'] : '';
    empty($ids) && $this->error(L('ERR_PARAMETERS'));
    $ids = implode(',', $ids);
    $map = ['id' => ['in', $ids]];
    if(!(new BookunitQuestionLogicV2)->delete($map)) $this->error('操作失败');

    $this->success("删除成功！");
  }



  //单元下的题目 - 批量添加
  public function addquestion(){

      $ids = $this->_param('ids/a',0);
      $unit = $this->_param('unit_id',0);

      if(!empty($ids)){
        foreach($ids as $v){
          $r = (new BookunitQuestionLogicV2)->addQids($unit,[$v]);
        }
      }
      $this->success($r['info']);

  }






}