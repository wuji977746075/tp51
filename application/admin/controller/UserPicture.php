<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-29 11:33:57
 * Description : [用户图片 关联]
 */

// namespace app\
// use

class UserPicture {
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