<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 11:33
 */

namespace app\admin\controller;


use app\src\log\logic\OperateLogLogic;
use app\src\system\logic\LogLogic;
use app\src\user\logic\MemberLogic;

class Log extends Admin
{

    protected function _initialize(){
        parent::_initialize();
        $this->assignTitle('日志管理');
    }

    public function index(){

        //get.startdatetime
        $startdatetime = urldecode($this->_param('startdatetime',date('Y/m/d H:i',time()-24*3600)));
        $enddatetime = urldecode($this->_param('enddatetime',date('Y/m/d H:i',time())));

        //分页时带参数get参数
        $params = array(
            'startdatetime'=>$startdatetime,
            'enddatetime'=>$enddatetime
        );

        $startdatetime = strtotime($startdatetime);
        $enddatetime = strtotime($enddatetime);

        if($startdatetime === FALSE || $enddatetime === FALSE){
            LogRecord('INFO:'.$result['info'],'[FILE] '.__FILE__.' [LINE] '.__LINE__);
            $this->error(L('ERR_DATE_INVALID'));
        }

        $map = array();

        $map['timestamp'] = array(array('EGT',$startdatetime),array('elt',$enddatetime),'and');

        $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
        $order = " timestamp desc ";

        $result = (new LogLogic())->queryWithPagingHtml($map,$page,$order,$params);
        //
        if($result['status']){
            $this->assign('startdatetime',$startdatetime);
            $this->assign('enddatetime',$enddatetime);
            $this->assign('show',$result['info']['show']);
            $this->assign('list',$result['info']['list']);
            return $this->boye_display();
        }else{
            $this->error(L('UNKNOWN_ERR'));
        }
    }


    public function clearall(){
        $result = Db::execute("TRUNCATE `itboye_api_call_his`;");
        $this->success("操作成功");
    }


    public function operate(){
        $map=[];
        $params = [];

        $operate_uid = $this->_param('o_uid','');
        $uid = $this->_param('uid','');
        $type = 1;
        if(!empty($uid)){
            $map['uid']=$uid;
            $params['uid']=$uid;
        }
        if(!empty($operate_uid)){
            $map['operate_uid']=$operate_uid;
            $params['o_uid']=$operate_uid;
        }

        $page= array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
        $order = 'createtime desc';
        $result = (new OperateLogLogic())->queryWithPagingHtml($map,$page,$order,$params);
        $data=[];
        if(!empty($result['info']['list'])){
            foreach($result['info']['list'] as $k=>$v){
                $data[$k]=$v;
                $user = (new MemberLogic())->getInfo(['uid'=>$v['uid']]);
                $operate = (new MemberLogic())->getInfo(['uid'=>$v['operate_uid']]);
                $data[$k]['user'] = $user['info'];
                $data[$k]['operate'] = $operate['info'];
            }
        }

        $this->assign('list',$data);
        $this->assign('show',$result['info']['show']);
        return $this->boye_display();
    }

}