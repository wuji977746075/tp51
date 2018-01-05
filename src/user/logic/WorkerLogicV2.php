<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 17:10
 */

namespace app\src\user\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\user\model\WorkerVerifyApply;
use app\src\user\facade\DefaultUserFacade;
use app\src\powersystem\logic\AuthGroupAccessLogic;
use app\src\powersystem\model\AuthGroupAccess;
use app\src\user\enum\RoleEnum;
use app\src\user\model\Member;
use app\src\user\model\UcenterMember;
use app\src\skill\logic\SkillLogicV2;
use think\Db;

use app\src\message\enum\MessageType;
use app\src\message\facade\MessageFacade;
use app\src\wallet\logic\ScoreHisLogicV2;

class WorkerLogicV2 extends BaseLogicV2
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
        $this->setModel(new WorkerVerifyApply());
        $this->role_name = RoleEnum::ROLE_Skilled_worker;
        $this->group_id = (new AuthGroupAccessLogic())->getRoleId($this->role_name);
    }

    public function getWorkers(array $map, $page = ['curpage'=>1,'size'=>10], $order = 'uid desc', $params = false, $fields = 'm.*' ) {
        $map['c.group_id'] = $this->group_id;
        $model = new AuthGroupAccess();
        $query = $model->alias('c')
        ->join(["common_member m",""],'m.uid = c.uid','left')
        ->where($map)->order($order)->field($fields);
        $start = max(intval($page['curpage'])-1,0)*intval($page['size']);
        $list = $query -> limit($start,$page['size']) -> select();

        $count = $model ->alias('c')
        ->join(["common_member m",""],'m.uid = c.uid','left')
        -> where($map) -> count();

        // $model = new Member();
        // $query = $model->alias('m')
        // ->join(["common_auth_group_access c",""],'m.uid = c.uid','left')
        // ->where($map)->order($order)->field($fields);
        // $start = max(intval($page['curpage'])-1,0)*intval($page['size']);
        // $list = $query -> limit($start,$page['size']) -> select();

        // $count = $model ->alias('m')
        // ->join(["common_auth_group_access c",""],'m.uid = c.uid','left')
        // -> where($map) -> count();
        return ["count" => $count, "list" => Obj2Arr($list)];

    }
    //获取技工信息
    public function getWorkerInfo($uid,$field='realname,nickname,uid',$skill=true){
        $r = (new Member())->where(['uid'=>$uid])->field($field)->find();
        if($r && $skill){
            $r = $r->getData();
            $r['mobile'] = '';
            //查询技能
            $r['worker_skills'] = (new SkillLogicV2())->queryNoPaging(['uid'=>$uid]);
            $r2 = (new UcenterMember())->where(['id'=>$uid])->field('mobile')->find();
            if($r2){
                $r['mobile'] = $r2->getData()['mobile'];
            }
        }
        return $r;
    }
    /**
     * 业务 - 技工认证申请
     * @Author
     * @DateTime 2016-12-13T16:09:46+0800
     * @param    [type]       $map  [description]
     * @return   [apiReturn]        [description]
     */
    public function apply(array $map){
        //? id_number
        if(!isIdCard($map['id_number'])){
            return ['status'=>false,'info'=>Linvalid('id_number')];
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
            return returnErr(L('err_has_applyed'));
        }
        Db::startTrans();
        $this->add($map);
        //改变技工角色认证状态
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
     * 是否为技工
     * @Author
     * @DateTime 2016-12-16T17:45:51+0800
     * @param    integer                  $uid [description]
     * @return   boolean                       [description]
     */
    public function isWorker($uid=0){
        return (new AuthGroupAccessLogic())->hasRole($uid,$this->role_name);
    }
    /**
     * 技工认证申请列表
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
     * 业务 - 通过技工认证
     * @Author
     * @DateTime 2016-12-13T16:10:19+0800
     * @param    [type]     $id [description]
     * @return   [apiReturn]    [description]
     */
    public function pass($id=0){
        //申请信息
        $info = $this->isApplyingById($id);
        if($info){
            $uid = $info['uid'];

            Db::startTrans();
            //改变技工角色认证状态
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
            $msg = L('worker_verify_pass');
            $params = [
                'uid'      => 0,
                'to_uid'   => $uid,
                'msg_type' => MessageType::SYSTEM,
                'push'     => 1,
                'record'   => 1,
                'client'   => 'worker',

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
     * 业务 - 驳回技工认证 - 并删除记录
     * @Author
     * @DateTime 2016-12-13T16:10:19+0800
     * @param    [int ]      $id    [id]
     * @return   [apiReturn]        [description]
     */
    public function deny($id=0,$msg=''){
        //申请信息
        $info = $this->isApplyingById($id);
        if($info){
            $uid = $info['uid'];

            Db::startTrans();
            //删除申请记录
            $this ->delete(['id'=>$id,'status'=>self::VERIFY_ING]);

            //修改技工认证状态
            $r = (new AuthGroupAccessLogic())->save(['uid'=>$uid,'group_id'=>$this->group_id],['is_auth'=>0]);
            if(!$r['status']) return returnErr($r['info'],true);

            Db::commit();
            //记录信息+推送
            $msg = L('worker_verify_deny',['reason'=>$msg ? $msg : '未知']);
            $params = [
                'uid'      => 0,
                'to_uid'   => $uid,
                'msg_type' => MessageType::SYSTEM,
                'push'     => 1,
                'record'   => 1,
                'client'   => 'worker',

                'content' => $msg,
                'extra'   => $msg,
                'title'   => L('worker_verify_deny2'),
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
     * @param    [type]  $id         [description]
     * @param    boolean $realDelete [description]
     * @return   [apiReturn]  [description]
     */
    public function del($id=0,$realDelete=false){
        $info = $this->isApplyingById($id);
        if($info){
            $uid = $info['uid'];
            if($realDelete){
                //真删除
                $this ->delete(['uid'=>$uid,'group_id'=>$this->group_id]);
            }else{
                //假删除
                $this ->pretendDelete(['uid'=>$uid,'group_id'=>$this->group_id]);
            }
            return returnSuc(L('success'));
        }else{
            //未申请 或 出错了
            return returnErr(L('err_operate'));
        }
    }
}