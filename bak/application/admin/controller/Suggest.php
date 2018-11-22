<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 16:49
 */

namespace app\admin\controller;

use app\src\suggest\logic\SuggestLogic;
/**
 * Class
 *
 * @package app\admin\controller
 */
class Suggest extends Admin
{
    public function index(){
        header('Cache-control: private, must-revalidate');
        $map    = [];
        $params = [];

        $uid   = $this->_param('uid', 0);
        $this->assign('uid',$uid);
        $nickname = $uid ? getNickname($uid):'';
        $this->assign('nickname',$nickname);
        if ($uid){
            $map['uid']    = $uid;
            $params['uid'] = $uid;
        }

        $startdatetime = $this->_param('startdatetime','');
        $params['startdatetime'] = $startdatetime;
        $this -> assign('startdatetime', $startdatetime);
        $startdatetime = strtotime($startdatetime);
        $enddatetime   = $this->_param('enddatetime','');
        $params['enddatetime'] = $enddatetime;
        $this -> assign('enddatetime', $enddatetime);
        $enddatetime   = strtotime($enddatetime);
        if($startdatetime){
            if($enddatetime){
                $map['create_time'] = [['EGT', $startdatetime], ['ELT', $enddatetime], 'and'];
            }else{
                $map['create_time'] = ['EGT',$startdatetime];
            }
        }else{
            if($enddatetime){
                $map['create_time'] = ['ELT',$enddatetime];
            }
        }

        $page  = ['curpage' =>$this->_param('p', 1) , 'size' => config('LIST_ROWS')];
        $order = " create_time desc ";
        $r = (new SuggestLogic)->queryWithPagingHtml($map,$page,$order,$params);
        !$r['status'] && $this->error($r['info']);
        $list = $r['info']['list'];
        $show = $r['info']['show'];
        foreach ($list as &$v) {
            $v['nickname'] = getNickName($v['uid']);
        } unset($v);

        $this->assign('params',$params);
        $this->assign('show',$show);
        $this->assign('list',$list);
        return $this->boye_display();
    }
}