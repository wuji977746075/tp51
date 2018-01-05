<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 17:10
 */

namespace app\src\user\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\user\model\DriverVerifyApply;
use app\src\user\facade\DefaultUserFacade;
use app\src\powersystem\logic\AuthGroupAccessLogic;
use app\src\user\enum\RoleEnum;
use think\Db;
use app\src\extend\Page;

use app\src\message\enum\MessageType;
use app\src\message\facade\MessageFacade;
use app\src\wallet\logic\ScoreHisLogicV2;

class DriverLogicV2 extends BaseLogicV2
{
    protected $group_id;
    protected $role_name;

    const VERIFY_ING  = 0;
    const VERIFY_PASS = 1;
    /**
     * @return mixed
     */
    protected function _init()
    {
        $this->setModel(new DriverVerifyApply());
        $this->role_name = RoleEnum::ROLE_Driver;
        $this->group_id  = (new AuthGroupAccessLogic())->getRoleId($this->role_name);
    }


    /**
     * 是否为司机
     * @Author
     * @DateTime 2016-12-16T17:45:08+0800
     * @param    integer                  $uid [description]
     * @return   boolean                       [description]
     */
    public function isDriver($uid=0){
        return (new AuthGroupAccessLogic())->hasRole($uid,$this->role_name);
    }
   /**
     * 业务 - 司机认证申请
     * @Author
     * @DateTime 2016-12-13T16:09:46+0800
     * @param    [type]                   $map [description]
     * @return   [apiReturn]                        [description]
     */
    public function apply(array $map){
        //? id_number
        if(!isIdCard($map['id_number'])){
            return ['status'=>false,'info'=>'身份证格式错误'];
        }
        $uid = $map['uid'];
        $api = new AuthGroupAccessLogic();
        //用户检查 uid+角色
        if(!$api->hasRole($uid,$this->role_name)){
            return returnErr(L('err_account_no_permissions'));
        }
        //是否已通过 - 暂不 - 可重新认证
        //是否已申请
        if($this->isApplyingByUid($uid)){
            return ['status'=>false,'info'=>L('err_has_applyed')];
        }

        Db::startTrans();
        $this->add($map);
        //改变司机角色认证状态
        $r = $api -> save(['uid'=>$uid,'group_id'=>$this->group_id],['is_auth'=>2]);
        if(!$r['status']) return returnErr($r['info'],true);
        Db::commit();
        return returnSuc(L('success'));
    }
    /**
     * 是否审核中
     * @Author
     * @DateTime 2016-12-13T16:16:12+0800
     * @param    int   $id [description]
     * @return   mixed [description]
     */
    private function isApplyingById($id=0){
        return $this ->getInfo(['id'=>$id,'status'=>self::VERIFY_ING]);
    }
    /**
     * 是否审核中
     * @Author
     * @DateTime 2016-12-13T16:16:12+0800
     * @param    int   $id [description]
     * @return   mixed [description]
     */
    private function isApplyingByUid($uid=0){
        return $this ->getInfo(['uid'=>$uid,'status'=>self::VERIFY_ING]);
    }
    /**
     * 司机认证申请列表
     * @Author
     * @DateTime 2016-12-14T10:31:47+0800
     * @param    array                    $params [description]
     * @return   [type]                           [description]
     */
    public function applyList(array $params,$show=false){
        extract($params);
        $map  = [];
        if($status !== -2) $map['status'] = $status;
        if($uid)           $map['uid']    = $uid;
        $page = ['curpage'=>$current_page,'size'=>$per_page];
        return $this ->queryWithPagingHtml($map,$page,$order);
    }

    /**
     * 业务 - 通过司机认证
     * @Author
     * @DateTime 2016-12-13T16:10:19+0800
     * @param    [type]         $id [description]
     * @return   [apiReturn]        [description]
     */
    public function pass($id=0){
        //申请信息
        $info = $this->isApplyingById($id);
        if($info){
            $uid = $info['uid'];
            Db::startTrans();
            //改变司机角色认证状态
            $r = (new AuthGroupAccessLogic()) -> save(['uid'=>$uid,'group_id'=>$this->group_id],['is_auth'=>1]);
            if(!$r['status']) return returnErr($r['info'],true);
            // ?图片信息  (转移到主表?) 暂不 不允许删除通过的记录
            //修改用户 实名认证状态
            $r = (new MemberConfigLogic())->save(['uid'=>$uid],['identity_validate'=>1]);
            if(!$r['status']) return returnErr($r['info'],true);
            //修改用户 实名信息
            $r = (new MemberLogic())->save(['uid'=>$uid],['realname'=>$info['realname'],'idnumber'=>$info['id_number']]);
            if(!$r['status']) return returnErr($r['info'],true);
            // 赠送积分
            $r = (new ScoreHisLogicV2)->changeScoreByRule($uid,ScoreHisLogicV2::REALNAME_ADD,true);
            if(!$r['status']) return returnErr($r['info'],true);
            //修改申请状态
            $this->enable(['uid'=>$uid,'status'=>self::VERIFY_ING]);

            Db::commit();
            //记录信息+推送
            $msg = L('driver_verify_pass');
            $params = [
                'uid'      => 0,
                'to_uid'   => $uid,
                'msg_type' => MessageType::SYSTEM,
                'push'     => 1,
                'record'   => 1,
                'client'   => 'driver',

                'content' => $msg,
                'extra'   => $msg,
                'title'   => $msg,
                'summary' => $msg,
            ];
            $r = (new MessageFacade())->pushMsg($params);
            return returnSuc($r['status'] ? L('success') : $r['info']);
        }else{
            //未申请 或 出错了
            return returnErr(Linvalid('id'));
        }
    }
    /**
     * 业务 - 驳回司机认证 - 并删除记录
     * @Author
     * @DateTime 2016-12-13T16:10:19+0800
     * @param    [int ]         $id    [id]
     * @return   [apiReturn]           [description]
     */
    public function deny($id=0,$msg=''){
        //申请信息
        $info = $this->isApplyingById($id);
        if($info){
            $uid = $info['uid'];
            Db::startTrans();
            //删除申请记录
            $this ->delete(['id'=>$id,'status'=>self::VERIFY_ING]);
            //修改司机认证状态
            $r = (new AuthGroupAccessLogic())->save(['uid'=>$uid,'group_id'=>$this->group_id],['is_auth'=>0]);
            if(!$r['status']) return returnErr($r['info'],true);

            Db::commit();
            //记录信息+推送
            $msg = L('driver_verify_deny',['reason'=>$msg ? $msg : '未知']);
            $params = [
                'uid'      => 0,
                'to_uid'   => $uid,
                'msg_type' => MessageType::SYSTEM,
                'push'     => 1,
                'record'   => 1,
                'client'   => 'driver',

                'content' => $msg,
                'extra'   => $msg,
                'title'   => L('driver_verify_deny2'),
                'summary' => $msg,
            ];
            $r = (new MessageFacade())->pushMsg($params);
            return returnSuc($r['status'] ? L('success') : $r['info']);
        }else{
            //未申请 或 出错了
            return returnErr(Linvalid('id'));
        }
    }
    /**
     * 业务 - 认证申请删除
     * @Author
     * @DateTime 2016-12-14T09:35:24+0800
     * @param    [type]         $id         [description]
     * @param    boolean        $realDelete [description]
     * @return   [apiReturn]                [description]
     */
    public function del($id=0,$realDelete=false){
        $info = $this->isApplyingById($id);
        if($info){
            $uid = $info['uid'];
            if($realDelete){
                //真删除
                $r = $this ->delete(['uid'=>$uid,'group_id'=>$this->group_id]);
            }else{
                //假删除
                $r = $this ->pretendDelete(['uid'=>$uid,'group_id'=>$this->group_id]);
            }
            // if(!$r['status']) return returnErr($r['info']);
            return returnSuc(L('success'));
        }else{
            //未申请 或 出错了
            return returnErr(L('err_operate'));
        }
    }
}