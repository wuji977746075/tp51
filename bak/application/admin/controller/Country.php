<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-08
 * Time: 16:06
 */

namespace app\admin\controller;


use app\src\system\logic\CountryLogic;
use app\src\system\logic\ProvinceLogic;

class Country extends Admin
{

    public function index(){
        $father = $this->_post('father/d',0);
        $name   = $this->_param('name','');

        $map = [];
        $params = [];
        $order = "id desc";
        if($name){
            $map[] = ['country_name','like','%'.$name.'%'];
            $params['name'] = $name;
        }

        $r = (new CountryLogic)->queryWithPagingHtml($map,$this->pager,$order,$params);

        $this->assign('name',$name);
        $this->assign('list',$r['info']['list']);
        $this->assign('show',$r['info']['show']);
        return $this->show();
    }


    public function delete($success_url = false){
        $id = $this->_param('id',0);
        $result = (new ProvinceLogic())->getInfo(['countryid'=>$id]);
        if($result['status'] && is_array($result['info']) && count($result['info']) > 0){
            $this->error('请先删除该国家下的省级区域数据' , url('Country/index'));
        }

        $result = (new CountryLogic())->delete(['id'=>$id]);

        if($result['status']){
            $this->success('删除成功!', url('Country/index'));
        }else{
            $this->error('删除失败!', url('Country/index'));
        }

    }

    /**
     * 编辑
     */
    public function edit(){
        $id = $this->_param('id',0);
        if(IS_GET){
            $result = (new CountryLogic())->getInfo(['id'=>$id]);

            if($result['status']){
                $this->assign('entity',$result['info']);
                return $this->show();
            }else{
                $this->error($result['info'],url('Country/index'));
            }
            return ;
        }
        $entity = [
            'country_name'=>$this->_param('country_name',''),
            'country_code'=>$this->_param('country_code',''),
            'country_tel_prefix'=>$this->_param('country_tel_prefix',''),
            'py'=>$this->_param('py',''),
        ];

        $result = (new CountryLogic())->saveByID($id,$entity);

        if($result['status']){
            $this->success('修改成功!',url('Country/index'));
        }else{
            $this->error('修改失败!',url('Country/index'));
        }

    }



}