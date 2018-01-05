<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 13:57
 */

namespace app\admin\controller;


use app\src\banners\logic\BannersLogic;
use app\src\system\logic\DatatreeLogicV2;

class Banners extends Admin
{

    public function index(){

        $map = array(
            "parentid" => getDatatree('BANNERS_TYPE'),
        );

        $result = (new DatatreeLogicV2())->queryNoPaging($map,"","id,name");

        $this -> assign('banners_type',$result);

        $type = $this->_param('type',0);
        if((!$type || !is_numeric($type))&& !empty($result) && is_array($result)) $type = $result[0]['id'];
        $this -> assign('type',$type);
        if($type){
            $map['dt.parentid']= $map['parentid'];
            $map['banner.position']= $type;
            unset($map['parentid']);
            $params['type'] = $type;
            $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
            $order = " create_time desc ";
            $result = (new BannersLogic())->queryWithPosition($map, $page, $order,$params);

        }else{
            $result = array('status'=>true,'info'=>array('show'=>'','list'=>''));
        }


        $this -> assign('show', $result['info']['show']);
        $this -> assign('list', $result['info']['list']);
        return $this -> boye_display();
    }

    public function add(){
        if(IS_GET){
            $type = $this->_param('type',0,'int');
            $this->assign('type',$type);
            return $this -> boye_display();
        }else{
            $title = $this->_param('title','');
            //$url =
            $notes    = $this->_param('notes','');
            $position = $this->_param('position',18);
            $sort     = $this->_param('sort',0);
            if(empty($position)){
                $this->error("配置错误！");
            }
            $url_type = $this->_post('url_type',0);
            $entity = array(
                'uid'=>UID,
                'position'=>$position,
                'storeid'=>-1,
                'title'=>$title,
                'notes'=>$notes,
                'img'=>$this->_param('img',''),
                'url'=>$this->_param('url',''),
                'url_type'=>$url_type,
                'starttime'=>0,
                'endtime'=>0,
                'noticetime'=>0,
                'sort'=>$sort,
            );
            $result = (new BannersLogic())->add($entity);
            if(!$result['status']) $this->error($result['info']);
            $this->success("保存成功！",url('Admin/Banners/index',array('type'=>$position)));

        }
    }


    public function edit(){
        $id = $this->_param('id',0);
        if(IS_GET){
            $result = (new BannersLogic())->getInfo(['id'=>$id]);
            if(!$result['status']){
                $this->error($result['info']);
            }
            $this->assign('checkedID',$result['info']['url_type']);
            $this->assign("vo",$result['info']);
            return $this -> boye_display();
        }else{
            $title = $this->_param('title','');
            //	$url =
            $notes = $this->_param('notes','');
            $position = $this->_param('position',18);
            $sort = $this->_param('sort',0);
            if(empty($position)){
                $this->error("配置错误！");
            }
            $url_type = $this->_post('url_type',0);
            $entity = array(
                'position'=>$position,
                'title'=>$title,
                'notes'=>$notes,
                'img'=>$this->_param('img',''),
                'url'=>$this->_param('url',''),
                'url_type'=>$url_type,
                'sort'=>$sort,
            );

            $result = (new BannersLogic())->saveByID($id,$entity);

            if(!$result['status']){
                $this->error($result['info']);
            }
            $this->success("保存成功！",url('Admin/Banners/index',array('type'=>$position)));
        }
    }

    public function delete(){
        $id = $this->_param('id',0);
        $result = (new BannersLogic())->delete(array('id'=>$id));

        if(!$result['status']){
            $this->error($result['info']);
        }

        $this->success("删除成功！",url('Admin/Banners/index'));

    }

    //批量删除
    public function delete_all(){
        $tids   = $this->_param('ids','');
        if($tids == '' || count($tids) == 0){
            $this -> success('操作成功');
        }
        $result = (new BannersLogic())->delete(['id'=>array('in',$tids)]);

        if($result['status']){
            $this -> success("操作成功");
        }else{
            $this -> error($result['info']);
        }

    }
}