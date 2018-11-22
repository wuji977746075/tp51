<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/7
 * Time: 15:27
 */

namespace app\admin\controller;
use app\src\user\logic\VUserInfoLogic;

/**
 * Class Repairer
 * 技工管理
 * @package app\admin\controller、
 */
class Repairer extends Admin
{

    public function index(){


        $mobile   = $this->_param('mobile', '');
        $nickname   = $this->_param('nickname', '');
        $params     = [];
        $map        = [];
        if(!empty($mobile)){
            $map['mobile'] = array('like', "%" . $mobile  . "%");
            $params['mobile'] = $mobile;
        }
        if(!empty($nickname)){
            $map['nickname'] = array('like', "%" . $nickname  . "%");
            $params['nickname'] = $nickname;
        }

        $map['u_group'] = 7;

        $p = $this->_param('p',0);
        $page = array('curpage' => $p, 'size' => 10);
        $order = " reg_time desc ";
        $result = (new VUserInfoLogic())->queryWithPagingHtml($map, $page, $order,$params);

        $params['p'] = $p;
        $this -> assign('params',$params);
        $this -> assign('mobile',$mobile);
        $this -> assign("nickname",$nickname);
        $this -> assign("show", $result['info']['show']);
        $this -> assign("list", $result['info']['list']);

        return $this->boye_display();
    }

    public function edit(){
        if(IS_GET){



            return $this->boye_display();
        }
    }

}