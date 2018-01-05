<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 16:38
 */

namespace app\domain;

use app\src\user\logic\WorkerLogicV2;
use app\src\system\logic\DatatreeLogicV2;
use app\src\skill\logic\SkillLogicV2;

/**
 * 技工相关
 * Class WorkerDomain
 * @author rainbow <email:977746075@qq.com>
 * @package app\domain
 */
class WorkerDomain extends BaseDomain
{
    /**
     * 技工认证申请
     * @Author
     * @DateTime 2016-12-13T15:56:39+0800
     * @return   [type]                   [description]
     */
    public function apply(){
        $this->checkVersion("100");

        $params = $this->parsePost('status|-1|int,id_certs,uid|0|int,realname,id_number','');
        $r = (new WorkerLogicV2()) -> apply($params);
        $this->exitWhenError($r,true);
    }

    /**
     * 查看技能 返回全部all_skills 和 用户的user_skills
     * @Author
     * @DateTime 2016-12-16T11:02:20+0800
     * @return   [type]                   [description]
     */
    public function skill($uid=0){
        $this->checkVersion("100");

        $params = $this->parsePost('','uid|0|int');
        $uid    = $params['uid'];
        $rsp = [];
        //全部技能
        $r = (new DatatreeLogicV2())->getCacheList(['parentid'=>getDtreeId('worker_skill')]);
        $rsp['all_skills'] = array_values($r);
        if($uid){
            $r = (new SkillLogicV2())->queryNoPaging(['uid'=>$uid]);
            $rsp['user_skills'] = $r;
        }
        $this->exitWhenError(returnSuc($rsp),true);
    }

    /**
     * 设置技工技能
     * @Author
     * @DateTime 2016-12-16T11:13:35+0800
     * @param    [type]                   $uid [description]
     */
    public function setSkill(){
        $this->checkVersion("100");

        $params = $this->parsePost('uid|0|int,skill_ids','');
        $r = (new SkillLogicV2())->set($params);
        $this->exitWhenError($r,true);
    }
    /**
     * 技工认证申请处理
     * @Author
     * @DateTime 2016-12-14T10:26:05+0800
     * @return   [type]                   [description]
     */
    public function verify(){
        $this->checkVersion("100");

        $params = $this->parsePost('id|0|int,op_id|0|int','msg');
        extract($params);
        $logic = new WorkerLogicV2();
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
     * 技工认证申请列表
     * @Author
     * @DateTime 2016-12-14T10:26:10+0800
     * @return   [type]                   [description]
     */
    public function applyList(){
        $this->checkVersion("100");

        $params = $this->parsePost('','page|1|int,size|10|int,kword,order|id asc,status|-2|int');
        ;
        $this->apiReturnSuc((new WorkerLogicV2()) ->applyList($params));
    }
}