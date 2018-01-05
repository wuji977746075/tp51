<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 16:38
 */

namespace app\domain;

use app\src\user\logic\DriverLogicV2;

/**
 * 司机相关
 * Class DriverDomain
 * @author rainbow <email:977746075@qq.com>
 * @package app\domain
 */
class DriverDomain extends BaseDomain
{
    /**
     * 司机认证申请
     * @Author
     * @DateTime 2016-12-13T15:56:39+0800
     * @return   [type]                   [description]
     */
    public function apply(){
        $this->checkVersion("100");

        $params = $this->parsePost('id_certs,driver_cert,uid|0|int,realname,id_number','');
        $r = (new DriverLogicV2()) -> apply($params);
        $this->exitWhenError($r,true);
    }

    /**
     * 司机认证申请处理
     * @Author
     * @DateTime 2016-12-14T10:26:05+0800
     * @return   [type]                   [description]
     */
    public function verify(){
        $this->checkVersion("100");

        $params = $this->parsePost('id|0|int,op_id|0|int','msg');
        extract($params);
        $logic = new DriverLogicV2();
        if(1===$op_id){
            //通过
            $r = $logic ->pass($id);
        }elseif(2===$op_id){
            //驳回 + 真删除
            $r = $logic ->deny($id,$msg);
        }elseif(-1===$op_id){
            //假删除
            // $r = $logic ->del($id);
            //保护图片
            $this->apiReturnErr(L('err_operate'));
        }elseif(-2===$op_id){
            //真删除
            // $r = $logic ->del($id,true);
            //保护图片
            $this->apiReturnErr(L('err_operate'));
        }else{
            $this->apiReturnErr(Linvalid('op_id'));
        }
        if(!$r['status']) $this->apiReturnErr($r['info']);
        $this->apiReturnSuc($r['info']);
    }
    /**
     * 司机认证申请列表
     * @Author
     * @DateTime 2016-12-14T10:26:10+0800
     * @return   [type]                   [description]
     */
    public function applyList(){
        $this->checkVersion("100");

        $params = $this->parsePost('','current_page|1|int,per_page|10|int,kword,order|id asc,status|-2|int,uid|0|int');
        $this->apiReturnSuc((new DriverLogicV2())->applyList($params));
    }
}