<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 11:38
 */

namespace app\admin\controller;

use app\src\log\logic\ApiHisViewLogic;
use think\Log;
class ApiHistory extends Admin
{
    protected function _initialize(){
        parent::_initialize();
        $this->assignTitle('日志管理');
    }
    public function index(){

        $map = [];
        $params = [];
        $page = array('curpage' => $this->_param('p', 0), 'size' =>20);
        $order = " api_cnt desc ";
//        $order = false;

        $result = (new ApiHisViewLogic())->queryWithPagingHtml($map,$page,$order,$params);
        if($result['status']){
            $this->assign('show',$result['info']['show']);
            $this->assign('list',$result['info']['list']);
            return $this->boye_display();
        }else{
            Log::record('INFO:'.$result['info'],'[FILE] '.__FILE__.' [LINE] '.__LINE__);
            $this->error(L('数据查询出错！'));
        }
    }
    public function index2(){

        //get.startdatetime
        $startdatetime = $this->_param('startdatetime',date('Y/m/d H:i',time()-24*3600));
        $enddatetime = $this->_param('enddatetime',date('Y/m/d H:i',time()));
        $startdatetime = urldecode(urldecode($startdatetime));
        $enddatetime   = urldecode(urldecode($enddatetime));

        //分页时带参数get参数
        $params = array(
            'startdatetime'=>$startdatetime,
            'enddatetime'=>$enddatetime
        );
        $startdatetime = strtotime($startdatetime);
        $enddatetime = strtotime($enddatetime);

        if($startdatetime === FALSE || $enddatetime === FALSE){
            $this->error(L('ERR_DATE_INVALID'));
        }

        $map = array();
        $map['call_time'] = array(array('EGT',$startdatetime),array('elt',$enddatetime),'and');
        $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
        $order = " call_time desc ";
        dump($map);
        $result = (new ApiHistoryLogic())->queryWithPagingHtml($map,$page,$order,$params);

        if($result['status']){
            $this->assign('startdatetime',$startdatetime);
            $this->assign('enddatetime',$enddatetime);
            $this->assign('show',$result['info']['show']);
            $this->assign('list',$result['info']['list']);
            return $this->boye_display();
        }else{
            Log::record('INFO:'.$result['info'],'[FILE] '.__FILE__.' [LINE] '.__LINE__);
            $this->error(L('数据查询出错！'));
        }
    }

}