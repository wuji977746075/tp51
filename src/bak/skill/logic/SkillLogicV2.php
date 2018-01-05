<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 17:10
 */

namespace app\src\skill\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\system\logic\DatatreeLogicV2;
use app\src\skill\model\WorkerSkill;
use app\src\powersystem\AuthGroupAccessLogic;
use think\Db;
use app\src\user\logic\WorkerLogicV2;

class SkillLogicV2 extends BaseLogicV2
{
    /**
     * @return mixed
     */
    protected function _init()
    {
        $this->setModel(new WorkerSkill());
    }

    /**
     * rewrite
     */
    public function queryNoPaging($map = null, $order = 's.id asc', $fields = 'd.id,d.name,d.code') {

        $query = $this-> getModel()->alias('s');
        $query -> join(['common_datatree d',''],'s.dt_skill_id = d.id');
        if(!empty($map)) $query = $query->where($map);
        if(false !== $order) $query = $query->order($order);
        if(false !== $fields) $query = $query->field($fields);
        $list = $query -> select();

        return $list ? obj2Arr($list) : [];
    }
    /**
     * 业务 - 设置技工技能
     * @return [apiReturn]
     */
    public function set($params){
        extract($params);
        //? 技工
        if(!(new WorkerLogicV2())->isWorker($uid)){
            return returnErr(L('err_account_no_permissions'));
        }
        //? 技能id
        $new_skills = array_unique(explode(',', $skill_ids));
        if(!(new DatatreeLogicV2())->isExistIds($new_skills,getDtreeId('worker_skill'))){
            return returnSuc(Linvalid('skill_ids'));
        }
        //现有技能
        $r = $this->queryNoPaging(['uid'=>$uid]);
        $r = changeArrayKey($r,'id');

        $old_skills = array_keys($r);
        //设置技能
        $add_skills = array_diff($new_skills, $old_skills);
        $del_skills = array_diff($old_skills, $new_skills);
        Db::startTrans();
        if($add_skills){
            $map = [];
            foreach ($add_skills as $v) {
                $map[] = [
                    'uid'=>$uid,
                    'dt_skill_id'=>$v
                ];
            }
            $this->saveAll($map);
        }
        if($del_skills){
            $this->delete(['uid'=>$uid,'dt_skill_id'=>['in',$del_skills]]);
        }
        Db::commit();
        return returnSuc(L('success'));
    }
}