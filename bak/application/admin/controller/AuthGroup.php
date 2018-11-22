<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace app\admin\controller;

use app\src\powersystem\logic\AuthGroupAccessLogic;
use think\Log;
use app\src\powersystem\logic\AuthGroupLogic;
class AuthGroup extends Admin{

    public function index(){
        $map = null;
        $page = ['curpage'=>$this->_param('p'),'size'=>config('LIST_ROW')];
        $order = " id asc ";

        $r = (new AuthGroupLogic())->queryWithPagingHtml($map,$page,$order);
        !$r['status'] && $this->error($r['info']);

        $this->assign("show",$r['info']['show']);
        $this->assign("list",$r['info']['list']);
        return $this->boye_display();
    }

    public function add(){
        if(IS_POST){
            $entity = array(
                'title'=>$this->_param('title','','trim'),
                'notes'=>$this->_param('notes','','trim')
            );
            $result = (new AuthGroupLogic())->add($entity);
            if($result['status']){
                $this->success('操作成功',url('AuthGroup/index'));
            }else{
                $this->success('操作失败');
            }
        }else{
            return $this->boye_display();
        }

    }


    public function writeRules(){
        $groupid = $this->_param('groupid',-1);
        $modulename = $this->_param('modulename','');
        $map = array();

        $rules = $this->_param('rules','');
        if(is_array($rules)){
            $rules = implode(",", $rules);
            $rules = $rules.',';
        }
//		$result = apiCall('Admin/AuthGroup/writeRules',array($groupid,$rules));
        $result = (new AuthGroupLogic())->writeRules($groupid,$rules);
        if($result['status']){
            $this->success("操作成功~页面将自动跳转");
        }else{
            Log::record($result['info'], __FILE__.__LINE__);
            $this->error($result['info']);
        }
    }

    public function writeMenuList(){

        $groupid = $this->_param('groupid',-1);
        $menulist = $this->_param('menulist','');
        if($menulist == ","){
            $menulist = "";
        }
//		$result = apiCall('Admin/AuthGroup/writeMenuList',array($groupid,$menulist));
        $result = (new AuthGroupLogic())->writeMenuList($groupid,$menulist);

        if($result['status']){
            $this->success("操作成功~页面将自动跳转");
        }else{
            Log::record($result['info'], __FILE__.__LINE__);
            $this->error($result['info']);
        }
    }

    public function delete(){
        $id = $this->_param('id',0);
        $accessLogic = new AuthGroupAccessLogic();
        $result = $accessLogic->getInfo(['group_id'=>$id]);
        if($result['status'] && !empty($result['info'])){
            $this->error('请先【回收用户】',url('Admin/AuthGroup/index'));
        }
        $logic = new AuthGroupLogic();
        $result = $logic->delete(['id'=>$id]);
        if($result['status']){
            $this->success('操作成功!',url('Admin/AuthGroup/index'));
        }else{
            $this->error('操作失败',url('Admin/AuthGroup/index'));
        }
    }

    /**
     * 从用户中回收该用户组
     */
    public function recycleAllUser(){
        $id = $this->_param('id',0);

        $accessLogic = new AuthGroupAccessLogic();
        $result = $accessLogic->delete(['group_id'=>$id]);
        if($result['status']){
            $this->success('回收成功!',url('Admin/AuthGroup/index'));
        }else{
            $this->error('操作失败',url('Admin/AuthGroup/index'));
        }
    }

    public function edit(){
        $id = $this->_param('id',0);
        if(IS_GET){
            $result = (new AuthGroupLogic())->getInfo(['id'=>$id]);
            $this->assign("entity",$result['info']);
            return $this->boye_display();
        }
        $title = $this->_param('title','');
        $notes = $this->_param('notes','');

        $entity = [
            'title'=>$title,
            'notes'=>$notes
        ];

        $result = (new AuthGroupLogic())->saveByID($id,$entity);
        if($result['status']){
            $this->success('保存成功',url('Admin/AuthGroup/index'));
        }else{
            $this->error('保存失败',url('Admin/AuthGroup/edit',array('id'=>$id)));
        }

    }
}
