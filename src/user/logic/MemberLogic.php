<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 17:10
 */

namespace app\src\user\logic;


use app\src\base\logic\BaseLogic;
use app\src\user\model\Member;
use app\src\extend\Page;
use think\Db;
class MemberLogic extends BaseLogic
{
    protected $tablePrefix = "common_";
    /**
     * @return mixed
     */
    protected function _init()
    {
        $this->setModel(new Member());
    }

    public function isExistUid($uid,$field='uid'){
      $r = $this->getModel()->where(['uid'=>$uid])->field($field)->find();
      return $r ? $r->getData() : false;
    }

    public function getOneInfo($uid,$field='nickname'){
      $r = $this->getModel()->where(['uid'=>$uid])->field($field)->find();
      return $r ? $r->getData()[$field] : '';
    }


    /**
     * 根据id保存数据
     */
    public function saveByID($uid, $entity) {
        unset($entity['id']);

        return $this -> save(array('uid' => $uid), $entity);
    }

    /**
     * 与 queryWithGroup 类似，这个是较早版本
     * @param $map
     * @param $page
     * @param bool $params
     * @return array
     */
    public function queryByGroup($map,$page,$params=false){
        $fields = "m.uid,m.nickname,m.status";
        $groupId = $map['group_id'];
        $map = array('a.group_id'=>$groupId,'m.status'=>array('gt',0));
        $order = 'm.uid asc';

        $list = Db::table("common_member")->alias("m")
            ->field($fields)
            ->join(["common_auth_group_access"=>"a"],"a.uid = m.uid","left")
            ->order($order)
            ->page($page['curpage'] . ',' . $page['size'])
            ->where($map)
            ->select();

        $count = Db::table("common_member")->alias("m")
            ->field($fields)
            ->join(["common_auth_group_access"=>"a"],"a.uid = m.uid","left")
            ->order($order)
            ->where($map)
            ->count();

        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);
        //分页跳转的时候保证查询条件
        if ($params !== false) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }
        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        return $this -> apiReturnSuc(array("show" => $show, "list" => $list));
    }

    public function queryWithGroup($nicknameOrUid = '',$add_uid=0,$group_id=0, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false, $fields = false){

        $map = array();
        if(!empty($nicknameOrUid)){
            $map['nickname']=array('like','%'.$nicknameOrUid.'%');
        }
        $map['add_uid'] = $add_uid;
        $map['group_id'] = $group_id;
        $list = $this->getModel()->alias("m")->join(" __AUTH_GROUP_ACCESS__ as  ac on m.uid = ac.uid  ","RIGHT")
            ->where($map)
            -> page($page['curpage'] . ',' . $page['size']) ->select();

        if ($list === false) {
            $error = $this -> getModel() -> getDbError();
            return $this -> apiReturnErr($error);
        }

        $count = $this -> getModel() -> where($map) -> count();
        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);

        //分页跳转的时候保证查询条件
        if ($params !== false) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        return $this -> apiReturnSuc(array("show" => $show, "list" => $list));

    }

}