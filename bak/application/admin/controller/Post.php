<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\admin\controller;


use app\src\post\logic\VPostInfoLogic;
use app\src\post\logic\PostLogic;
use app\src\system\logic\DatatreeLogicV2;
use app\src\user\logic\MemberLogic;

class Post extends  Admin{

    public function index(){
        $result = (new DatatreeLogicV2())->queryNoPaging(array('parentid' => '21'));
        $this->assign("article_cates",$result);

        $map = [];

        $name = $this->_param("name",'');
        $params = [];
        if($name){
            $params['name'] = $name;
            $map['post_author_name'] = array('like', '%'.$name.'%');
        }

        $cate = $this->_param("cate",'');
        $this->assign("cur_cate",$cate);
        if(!empty($cate)){
            $params['cate'] = $cate;
            //仅查询了下一级子类目
            $result = (new DatatreeLogicV2())->queryNoPaging(array('parentid' => $cate));
            if($result){
                foreach ($result as $v) {
                    $cate .= ','.$v['id'];
                }
                $map['post_category'] = ['in',$cate];
            }else{
                $map['post_category'] = $cate;
            }

        }

        $page  = array('curpage' => $this->_param('p', 1), 'size' => config('LIST_ROWS'));
        $order = " post_modified desc ";
        $result = (new VPostInfoLogic())->queryWithPagingHtml($map, $page, $order, $params);
        if($result['status']){
            $this->assign('name',$name);
            $this->assign('show',$result['info']['show']);
            $this->assign('list',$result['info']['list']);
            return $this->boye_display();
        }else{
            $this->error(L('UNKNOWN_ERR'));
        }
    }


    public function add(){
        if(IS_GET){
            return $this->boye_display();
        }else{
            $post_category = $this->_param('post_category',20);
            $start_time    = $this->_param('start_time',0);
            $end_time      = $this->_param('end_time',0);
            $start_time    = strtotime($start_time);
            $end_time      = strtotime($end_time);
            $jump_url      = $this->_param('jump_url','');

            $member = (new MemberLogic())->getInfo(['uid'=>UID]);

            $entity = array(
                'jump_url'       =>$jump_url,
                'start_time'     =>$start_time,
                'end_time'       =>$end_time,
                'main_img'       =>$this->_param('main_img',''),
                'post_category'  =>$post_category,
                'post_content'   =>$this->_param('post_content',''),
                'post_excerpt'   =>$this->_param('post_excerpt',''),
                'post_title'     =>$this->_param('post_title',''),
                'post_author'    =>UID,
                'post_author_name'    =>$member['info']['nickname'],
                'post_status'    =>$this->_param('post_status','draft'),
                'comment_status' =>$this->_param('commen_status','closed'),
                'post_parent'    =>0,
                'post_type'      =>'post_type',
                'comment_count'  =>0
            );

            $result = (new PostLogic())->add($entity);

            if(!$result['status']){
                $this->error($result['info']);
            }

            $this->success("操作成功！",url('Admin/Post/index'));

        }
    }

    public function edit(){
        $id = $this->_param('id',0);
        if(IS_GET){
            $result = (new PostLogic())->getInfo(array("id" => $id));
            if(!$result['status']){
                $this->error($result['info']);
            }
            $this->assign("vo",$result['info']);
            return $this->boye_display();
        }else{
            $post_category = $this->_param('post_category',20);
            $start_time = $this->_param('start_time','');
            $end_time = $this->_param('end_time','');
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            $user = session("global_user");
            $jump_url = $this->_param('jump_url','');

            $entity = array(
                'jump_url'=>$jump_url,
                'start_time'=>$start_time,
                'end_time'=>$end_time,
                'main_img'=>$this->_param('main_img',''),
                'post_category'=>$post_category,
                'post_author'=>UID,
                'post_content'=>$this->_param('post_content',''),
                'post_excerpt'=>$this->_param('post_excerpt',''),
                'post_title'=>$this->_param('post_title',''),
                'post_status'=>$this->_param('post_status','draft'),
                'comment_status'=>$this->_param('commen_status','closed'),
            );
            $entity['post_author_name'] = $user['username'];
            $result = (new PostLogic())->saveByID($id, $entity);

            if(!$result['status']){
                $this->error($result['info']);
            }

            $this->success("保存成功！",url('Admin/Post/index'));

        }
    }

    public function delete(){
        $id = $this->_param('id',0);

        $result = (new PostLogic())->delete(array("id" => $id));

        if(!$result['status']){
            $this->error($result['info']);
        }

        $this->success("删除成功！");

    }

}
