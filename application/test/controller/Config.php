<?php
/**
 * 周舟 hzboye010@163.com
 * addby sublime snippets
 * 获取app配置 测试类
 */
namespace app\test\controller;

use ApiService;

class Config extends Ava{
    //接口配置
    public function app(){
        $type = 'BY_Config_app';
        if(IS_POST){
            $data = $this->getPost($type,'');
            $r = (new ApiService)->callRemote("",$data,false);
            return $this->parseResult($r);
        }else{
            $this->assign('type',$type);
            $this->assign('field',[
                ['api_ver',100,LL('need-mark api version')],
            ]);
            return $this->fetch('ava/test');
        }

    }

    //最新版本
    public function version(){
        $type = 'BY_Config_version';
        if(IS_POST){
            $data = $this->getPost($type,'app_type');
            $r = (new ApiService)->callRemote("",$data,false);
            return $this->parseResult($r);
        }else{
            $this->assign('type',$type);
            $this->assign('field',[
                ['api_ver',100,LL('need-mark api version')],
                ['app_type','ios',LL('need-mark app-type')],
            ]);
            return $this->fetch('ava/test');
        }
    }
    //获取position
    public function position(){
        $type = 'BY_Config_position';
        if(IS_POST){
            $data = $this->getPost($type,'id,code');
            $r = (new ApiService)->callRemote("",$data,false);
            return $this->parseResult($r);
        }else{
            $this->assign('type',$type);
            $this->assign('field',[
                ['api_ver',100,LL('need-mark api version')],
                ['id','',L('datatree-id')],
                ['code','house_orientation',L('datatree-code')],
            ]);
            return $this->fetch('ava/test');
        }
    }
}