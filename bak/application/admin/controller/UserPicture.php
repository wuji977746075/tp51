<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/2
 * Time: 14:21
 */

namespace app\admin\controller;


use app\src\file\logic\UserPictureLogic;

class UserPicture extends Admin
{
    public function index(){
        $l_prop = intval($this->_param('l_prop', ''));
        $r_prop = intval($this->_param('r_prop', ''));
        $logic = new UserPictureLogic();
        $map = [];
        $param = [];
        if (is_int($l_prop) && is_int($r_prop)) {
            $map['porn_prop'] = ['exp', " <=  $r_prop and `porn_prop` >= $l_prop "];

            $param['l_prop'] = $l_prop;
            $param['r_prop'] = $r_prop;
        }

        $page = ['curpage' => $this->_param('p', 1), 'size' => 10];
        $order = "porn_prop desc";
        $result = $logic->queryWithPagingHtml($map, $page, $order, $param);

        if ($result['status']) {
            $this->assign('l_prop', $l_prop);
            $this->assign('r_prop', $r_prop);
            $this->assign('list', $result['info']['list']);
            $this->assign('show', $result['info']['show']);
        }

        return $this->boye_display();
    }

    public function illegal()
    {

        $id = $this->_param('id', 0);

        $logic = new UserPictureLogic();
        $logic->saveByID($id, ['porn_prop' => 999]);

        $this->success(lang('tip_success'));
    }

    public function legal()
    {

        $id = $this->_param('id', 0);

        $logic = new UserPictureLogic();

        $logic->saveByID($id, ['porn_prop' => 0]);

        $this->success(lang('tip_success'));
    }


}