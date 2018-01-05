<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-02
 * Time: 16:04
 */

namespace app\src\powersystem\logic;


use app\src\base\logic\BaseLogic;
use app\src\powersystem\model\AuthGroupAccess;
use app\src\user\enum\RoleEnum;
use app\src\user\logic\MemberLogic;
use think\Db;

class AuthGroupAccessLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new AuthGroupAccess());
    }
    /**
     * 根据uid获取用户的所有角色
     * @param  int      $uid
     * @return array    user role infos
     */
    public function getRolesByUID($uid=0){
        return $this->queryNoPaging(['uid'=>$uid],false,'group_id,is_auth');
    }

    /**
     * 新增角色
     * @param $uid
     * @param $role_code
     * @return int
     */
    public function addRole($uid, $role_code)
    {

        $role_id = $this->getRoleID($role_code);

        if (!$this->hasRole($uid, $role_code)) {

            $result = $this->add(['uid' => $uid, 'group_id' => $role_id]);

            return $result;
        }

        return $role_id;
    }

    public function getRoleID($role_code){
        $role_id = -1;
        switch ($role_code) {
            case RoleEnum::ROLE_Driver:

                $role_id = RoleEnum::getRoleIDBy($role_code);
                break;
            case RoleEnum::ROLE_Skilled_worker:

                $role_id = RoleEnum::getRoleIDBy($role_code);
                break;
            default:
                break;
        }
        return $role_id;
    }

    /**
     * 判断某用户是否拥有某角色
     * @param $uid
     * @param $role_code
     * @return bool
     */
    public function hasRole($uid,$role_code){

        $role_id = $this->getRoleID($role_code);

        if($role_id > 0){

            $r = $this->getInfo(['uid'=>$uid,'group_id'=>$role_id]);
            //bug-fix
            if(!$r['status'] || empty($r['info'])) return false;
            else return true;
        }

        return false;
    }

    /**
     * @param $uid
     * @return array
     */
    public function queryGroupInfo($uid){
        $uid = intval($uid);
        $result = Db::table("common_auth_group_access")->alias(" aga ")
            ->join(["common_auth_group"=>"ag"], "ag.id = aga.group_id and ag.status = 1","LEFT")
            ->field("aga.uid , ag.title,ag.notes ,ag.id")->where(" aga.uid = $uid")->select();
         return $this->apiReturnSuc($result);
    }

    /**
     * 把用户添加到用户组,支持批量添加用户到用户组
     * @param int $uid 用户id
     * @param string $group_id 用户组id
     * 示例: 把uid=1的用户添加到group_id为1,2的组
     * 1. AuthGroupModel->addToGroup(1,'1,2');
     * 2. AuthGroupModel->addToGroup(1,array('1','2'));
     * @return array
     */
    public function addToGroup($uid, $group_id)
    {

        if (empty($group_id)) {
            return $this->apiReturnErr("用户组非法!");
        }

        $member = new MemberLogic();
        $result = $member->getInfo(array('uid' => $uid));
        if ($result['status'] === false) {
            return $this->apiReturnErr("编号为{$uid}的用户不存在！");
        }

        $group_id = is_array($group_id) ? $group_id : explode(',', trim($group_id, ','));

        if (count($group_id) > 1) {
            //批量添加时，删除旧数据
            $this->delete(array("uid" => $uid));
        } else {
            $result = $this->getInfo(array('group_id' => $group_id[0], 'uid' => $uid));

            if ($result['status'] && is_array($result['info'])) {
                return $this->apiReturnErr("已经添加过了！");
            } elseif ($result['status'] === false) {
                return $this->apiReturnErr($result['info']);
            }
        }
        $listEntity = array();
        foreach ($group_id as $g) {
            if (is_numeric($uid) && is_numeric($g)) {
                array_push($listEntity, array('group_id' => $g, 'uid' => $uid));
            }
        }

        if (count($listEntity) == 0) {
            return $this->apiReturnErr("添加失败！");
        }

        $result = $this->getModel()->saveAll($listEntity);

        return $this->apiReturnSuc($result);

    }

}