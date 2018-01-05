<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-08
 * Time: 16:16
 */

namespace app\admin\controller;


use app\src\system\logic\CityLogic;
use app\src\system\logic\ProvinceLogic;

class City extends Admin
{
    protected function _initialize(){
        $this->assignTitle('城市管理');
        parent::_initialize();
    }

    /**
     * show province list
     * @return [type] [description]
     */
    public function index($father=0){
        $p = $this->_param('p',0);
        $r =(new CityLogic())->queryWithPagingHtml(array('father'=>$father),array('curpage'=>$p,'size'=>10),'id desc');
        if($r['status']){
            $this->assign('father',$father);
            $this->assign('list',$r['info']['list']);
            $this->assign('show',$r['info']['show']);
        }else{
            $this->error('系统错误： Province:query:error ');
        }
        return $this->boye_display("default",'Area/index_c');
    }

    public function delete($id){
        $r = (new CityLogic())->delete(['id'=>$id]);
        if(!$r['status']) $this->error($r['info']);
        else $this->success('修改成功');
    }

    public function add($father){
        $r = (new ProvinceLogic())->getInfo(['provinceID'=>$father]);
        if(!$r['status']) $this->error('get error: provinceapi');
        if(IS_GET){
            $this->assign('entry',$r['info']);
            return $this->boye_display("default",'Area/add_c');
        }elseif(IS_AJAX){
            $map               = array();
            $map['cityID'] = $this->_param('cityID',0);
            $map['city']   = $this->_param('city',0);
            $map['father'] = $father;
            $this->upd($map,$father);
        }
    }

    public function edit($id){
        $r = (new CityLogic())->getInfo(['id'=>$id]);

        if(!$r['status']) $this->error('get error: cityapi');
        if(IS_GET){
            $this->assign('entry',$r['info']);
            return $this->boye_display("default",'Area/edit_c');
        }elseif(IS_AJAX){
            $map               = array();
            $map['cityID'] = $this->_param('cityID');
            $map['city']   = $this->_param('city');
            $this->upd($map,$r['info']['father'],$id);
        }
    }

    protected function upd($map,$father,$id=0){
        $pre = substr($father,0,2);
        // dump($map);exit;
        if(!preg_match('/[\x{4e00}-\x{9fa5}]+/u', $map['city'])) $this->error('名称格式错误');
        if(!preg_match('/^'.$pre.'[A-Za-z0-9]{2}00$/', $map['cityID']) || substr($map['cityID'],2,2)=='00') $this->error('编号格式错误');
        unset($pre);
        $where               = array();
        $where['cityID']     = $map['cityID'];
        $where['city']       = $map['city'];
        $where['_logic']     = 'OR';

        $where2['_complex'] = $where;
        $where2['father']   = $father;
        if($id){
            $where2['id']   = array('neq'=>$id);
        }else{
            $where2 = $where;
        }
        $r = (new CityLogic())->getInfo($where2);
        unset($where);
        // dump(M()->getLastSql());exit;
        if(!$r['status']) $this->error('CityApi get error');
        if($r['info']){
            if($r['info']['cityid'] == $map['cityID']) $this->error('该编号已存在');
            if($r['info']['city']   == $map['city']) $this->error('该名字已存在');
        }
        if($id){
            $r = (new CityLogic())->saveByID($id,$map);
            if(!$r['status']) $this->error('修改失败');
            $this->success('修改成功',url('City/index',array('father'=>$father)));
        }else{
            $r = (new CityLogic())->add($map);
            if(!$r['status']) $this->error('添加失败');
            $this->success('修改成功',url('City/index',array('father'=>$father)));
        }
    }

}