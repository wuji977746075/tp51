<?php
/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/1/5
 * Time: 下午3:23
 */

namespace app\admin\controller;

use app\src\base\helper\ConfigHelper;
use app\src\powersystem\logic\AuthGroupLogic;
use app\src\repair\logic\RepairLogicV2;
use app\src\repair\logic\RepairOrderLogicV2;
use app\src\system\logic\DatatreeLogicV2;
use app\src\user\logic\VUserInfoLogic;
use app\src\user\logic\MemberLogic;

class Repair extends Admin
{

    //维修订单 - 管理
    public function order(){
        $map = [];

        $uid = $this->_param('uid',0);
        $this->assign('uid',$uid);
        $nickname = '';
        if($uid){
            $map['uid'] = $uid;
            $nickname = (new MemberLogic())->getOneInfo($uid);
        }
        $this->assign('nickname',$nickname);

        $order_code = $this->_param('order_code','');
        $this->assign('order_code',$order_code);
        if($order_code) $map['order_code'] = ['like','%'.$order_code.'%'];

        $pay_status = $this->_param('pay_status','');
        $this->assign('pay_status',$pay_status);
        if($pay_status !== '') $map['pay_status'] = $pay_status;

        $page = ['curpage' => $this->_param('p', 1), 'size' => 15];
        $r = (new RepairOrderLogicV2())->queryWithPagingHtml($map, $page, 'id desc');
        $this->assign('list',$r['list']);
        $this->assign('show',$r['show']);
        return $this->boye_display();
    }

    /**
     * 维修订单列表(待委派)
     */
    public function orderNew(){

        $p = $this->_param('p', 1);
        $RepairLogic = new RepairLogicV2;

        //半小时之后才需要后台委托
        $elapseTime = 1800;

        $map = [
            'repair_status' => 0,
            'worker_uid' => 0,
            'create_time'=>['lt',time()-$elapseTime]
        ];

        $page = ['curpage' => $p, 'size' => 15];

        $order = ['create_time' => 'asc'];

        $result = $RepairLogic->queryWithPagingHtml($map, $page, $order);
        foreach ($result['list'] as &$val){
            $DataTreeLogic = new DatatreeLogicV2;
            //维修类型
            $info = $DataTreeLogic->getInfo(['id' => $val['repair_type']]);
            $val['repair_type'] = !is_null($info) ? $info['name'] : '--';
            //车辆类型
            $info = $DataTreeLogic->getInfo(['id' => $val['vehicle_type']]);
            $val['vehicle_type'] = !is_null($info) ? $info['name'] : '--';
            if(!empty($val['images'])){
                $images = explode(',', $val['images']);
                foreach ($images as &$img){
                    $img = ConfigHelper::getPictureUrl($img, 200);
                }
                $val['images'] = $images;
            }

        }


        $this->assign('list', $result['list']);
        $this->assign('show', $result['show']);

        return $this->boye_display();
    }


    /**
     * 委派订单
     */
    public function orderDelegate(){

        if(IS_AJAX){
            $id = $this->_param('id');
            $uid = $this->_param('uid');

            if(empty($id)){
                $this->error('id错误');
            }

            if(empty($uid)){
                $this->error('师傅id错误');
            }

            $RepairLogic = new RepairLogicV2;
            $result = $RepairLogic->take($uid, $id);

            if($result['status']){
                $this->error($result['info']);
            }else{
                $this->success(L('RESULT_SUCCESS'));
            }

        }else{

            $id = $this->_param('id');
            if(empty($id)){
                $this->error('id错误');
            }
            $mobile   = $this->_param('mobile', '');
            $nickname   = $this->_param('nickname', '');
            $u_group   = 7;
            $params     = [
                'id' => $id
            ];
            $map        = null;
            if(!empty($mobile)){
                $map['mobile'] = array('like', "%" . $mobile  . "%");
                $params['mobile'] = $mobile;
            }
            if(!empty($nickname)){
                $map['nickname'] = array('like', "%" . $nickname  . "%");
                $params['nickname'] = $nickname;
            }

            if(!empty($u_group)){
                $map['u_group'] = $u_group;
                $params['u_group'] = $u_group;
            }

            $page = array('curpage' => $this->_param('p',0), 'size' => 15);
            $order = " reg_time desc ";
            $result = (new VUserInfoLogic)->queryWithPagingHtml($map, $page, $order);

            if ($result['status']) {
                $this -> assign('mobile',$mobile);
                $this -> assign("u_group",$u_group);
                $this -> assign("nickname",$nickname);
                $this -> assign("show", $result['info']['show']);
                $this -> assign("list", $result['info']['list']);
                $result = (new AuthGroupLogic)->queryNoPaging();
                if($result['status']){
                    $this->assign('user_group',$result['info']);
                }

                $this->assign('id', $id);
                return $this -> boye_display();
            } else {
                $this -> error($result['info']);
            }

        }
    }
}