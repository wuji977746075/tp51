<?php
 /**
  * Author      : rainbow <hzboye010@163>
  * DateTime    : 2017-03-25 16:33:40
  * Description : [Description]
  */

namespace app\admin\controller;

use app\src\base\helper\ConfigHelper;
use app\src\ewt\logic\TestpaperLogicV2;
use app\src\system\logic\DatatreeLogicV2;
use app\src\ewt\logic\QuestionLogicV2;
use app\src\ewt\logic\TestpaperQuestionLogicV2;
class Testpaper extends Admin{
    //试卷 - 管理
    public function index(){
        $params = [];
        $map = [];
        $title = $this->_param('title','');
        $this->assign('title',$title);
        if(!empty($name)){
          $map['title'] = ['like',"%$title%"];
          $params['title'] = $title;
        }
        $creator = $this->_param('creator','');
        $this->assign('creator',$creator);
        if(!empty($creator)){
          $map['creator'] = ['like',"%$creator%"];
          $params['creator'] = $creator;
        }
        $dt_types = (new DatatreeLogicV2)->queryNoPaging(['parentid'=>6234]);
        $this->assign('dt_types',$dt_types);
        $bk = new TestpaperLogicV2;

        $page = ['curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS')];
        $order = false;
        $r = $bk ->queryWithPagingHtml($map,$page,$order,$params);

        $this->assign('show',$r['show']);
        $this->assign('list',$r['list']);
        return $this->boye_display();
    }


    public function add(){
        if(IS_GET){
            $dt_types = (new DatatreeLogicV2)->queryNoPaging(['parentid'=>6234]);
            $this->assign('dt_types',$dt_types);
            return $this->boye_display();
        }else{

            $entity = array(
                'title'=>$this->_param('title',0),
                'creator'=>UID,
                'time_limit'=>$this->_param('time_limit',0),
                'dt_class'=>$this->_param('dt_class',0),
                'summary' => $this->_param('summary')
            );
            $result =(new TestpaperLogicV2())->add($entity);
            if($result){
                $this->success("添加成功！",url('Admin/Testpaper/index'));
            }else{
                $this->error($result['info']);
            }
        }

    }



    public function edit(){
        if(IS_GET){
            $dt_types = (new DatatreeLogicV2)->queryNoPaging(['parentid'=>6234]);
            $this->assign('dt_types',$dt_types);
           $id = $this->_param('id',0);
            $paper=(new TestpaperLogicV2())->getInfo(['id'=>$id]);
            $this->assign('paper',$paper);
            return $this->boye_display();
        }else{
            $id = $this->_param('id',0);
            $entity = array(
                'title'=>$this->_param('title',0),
                'creator'=>UID,
                'time_limit'=>$this->_param('time_limit',0),
                'dt_class'=>$this->_param('dt_class',0),
                'summary' => $this->_param('summary')
            );
            $result =(new TestpaperLogicV2())->saveByID($id,$entity);
            if($result){
                $this->success("添加成功！",url('Admin/Testpaper/index'));
            }else{
                $this->error($result['info']);
            }
        }

    }


    public function delete(){

        $id = $this->_param('id',0);

        $result = (new TestpaperLogicV2())->delete(array('id'=>$id));
        if($result){
            $this->success("删除成功！",url('Admin/Testpaper/index'));
        }else{
            $this->error($result);
        }

    }

    //显示用户题库 - 添加单元题目关联
    public function question(){
        if(IS_GET){
            $id = $this->_param('id',0);
            $this->assign('test_id',$id);
            $map = [];$params = [];

            $params['test_id']=$id;
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
//            $status = $this->_param('status',-1);
//            $this->assign('status',$status);
//            if($status != -1){
//                $params['status'] = $status;
//                $map['status'] = $status;
//            }
            $map['status']    = 1;
            //  $map['added_uid'] = UID;
            // $map['parent_id'] = 0;
            //查询
            $page = ['curpage' => $this->_param('p', 1), 'size' => 10];
            $r = (new QuestionLogicV2)->queryWithPagingHtml($map, $page, 'id desc',$params);
            $this->assign('list',$r['list']);
            $this->assign('show',$r['show']);

            return $this->boye_display();
        }else{
            $id   = $this->_param('question_id',0);
            $test_id = $this->_param('test_id',0);
            //? 发布
            $r = (new QuestionLogicV2)->getInfo(['id'=>$id]);
            empty($r) && $this->error('题目错误');

            //? 是否已添加
            // (new TestpaperQuestionLogicV2)->getInfo(['question_id'=>$id,'test_id'=>$test_id,'dt_part'=>$dt_part]);

            intval($r['status'])!=1 && $this->error('需要发布状态');
            $q_type = intval($r['dt_type']);//要添加的题目类型
            //? 单元的题型 - 已指定了吗
            // $r = (new BookunitQuestionLogicV2)->getQuestionType(['unit_id'=>$unit]);
            // if($r && $r!=$q_type) $this->error('同单元需要相同的题目类型');

            $entity = [
                'test_id'     =>$test_id,
                'question_id' =>$id,
                'sort'        =>$this->_param('sort',0)
            ];
            if((new TestpaperQuestionLogicV2)->add($entity)){
                $this->success("添加成功！");
            }else{
                $this->error('添加失败!');
            }
        }

    }

    //单元下的题目 - 列表
    public function edquestion(){
        if(IS_GET) {
            $id = $this->_param('id',0);
            $dt_class = $this->_param('dt_class',0);
            $page = ['curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS')];
            $this->assign('test_id',$id);
            $dt_types = (new DatatreeLogicV2)->getCacheList(['parentid'=>DatatreeLogicV2::QUESTION_TYPE]);
            $this->assign('dt_types',$dt_types);
            if($dt_class) {
                $dt_parts = (new DatatreeLogicV2)->getCacheList(['parentid' => $dt_class]);
                $this->assign('dt_parts', $dt_parts);
            }else{
                $this->error('试卷类型未知!');
            }
            $map = ['test_id' => $id];
            $params = [
                'id'=>$id,
                'dt_class'=>$dt_class,
            ];
            $r = (new TestpaperQuestionLogicV2)->queryWithPagingHtml($map,$page,'',$params);
            $this->assign('list',$r['list']);
            $this->assign('show',$r['show']);
            return $this->boye_display();

        }
    }

    public function qsave(){
        $id      = $this->_param('id',0);
        $sort    = $this->_param('sort',0);
        $score   = floatval($this->_param('score',0));
        $dt_part = $this->_param('dt_part',0);
        $save = [
            'sort'    =>$sort,
            'score'   =>intval($score*10),
            'dt_part' =>$dt_part
        ];
        $r = (new TestpaperQuestionLogicV2)->saveByID($id,$save);
        if(!empty($r)){
            $this->success("编辑成功！");
        }else{
            $this->error($r['info']);
        }
    }

    public function qdel(){

        $id = $this->_param('id',0);

        $result = (new TestpaperQuestionLogicV2())->delete(array('id'=>$id));
        if($result){
            $this->success("删除成功！");
        }else{
            $this->error($result);
        }
    }

    public function bulkqdel()
    {

        $ids = $_POST['ids'];
        if ($ids === -1) {
            $this->error(L('ERR_PARAMETERS'));
        }
        $ids = implode(',', $ids);
        $map = array('id' => array('in', $ids));

        $result = (new TestpaperQuestionLogicV2())->delete($map);

        if($result){
            $this->success("删除成功！");
        }else{
            $this->error($result);
        }

    }

}