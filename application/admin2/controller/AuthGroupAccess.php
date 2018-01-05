<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/3
 * Time: 14:05
 */

namespace app\admin\controller;


use app\src\admin\helper\AdminFunctionHelper;
use app\src\powersystem\logic\AuthGroupAccessLogic;

class AuthGroupAccess extends Admin
{
    /**
     * 将指定用户添加到指定用户组
     */
    public function addToGroup()
    {

        $uid = $this->_param('uid', '');
        $groupid = $this->_param('groupid', '');
        $extra   = $this->_param('extra', '');
        $prov    = $this->_param('prov', '');
        $city    = $this->_param('city', '');
        $area    = $this->_param('area', '');

        (empty($uid) || empty($groupid)) && $this->error("参数错误");
        (AdminFunctionHelper::isRoot($uid)) && $this->error("不能对超级管理员进行操作");

        $loc_code = '';
        $loc_name = '';
        if ($groupid) {
            $groupid = intval($groupid);
            if($groupid == 7){ //推荐人
                // ? prov
                empty($prov) && $this->error('推荐人至少要指定省份');
                // $loc_code = $area ? $area : ($city ? $city : $prov);
                if($area){
                    $area = explode('-', $area);
                    $loc_code = $area[0];
                    $loc_name = $area[1];
                }
                if($city){
                    $city = explode('-', $city);
                    $loc_code = $loc_code ? $loc_code : $city[0];
                    $loc_name = $city[1].$loc_name;
                }
                if($prov){
                    $prov = explode('-', $prov);
                    $loc_code = $loc_code ? $loc_code : $prov[0];
                    $loc_name = $prov[1].$loc_name;
                }
                // ? extra
                $extra = addslashes(strip_tags(trim($extra)));
                empty($extra) && $this->error("推荐人需要填写推荐码");
                // 是否重复了
                $r = (new AuthGroupAccessLogic)->getInfo(['extra'=>$extra,'group_id'=>$groupid]);
                !$r['status'] && $this->error($r['info']);
                $r['info'] &&  $this->error('推荐码重复了');
            }
        }

        $r = (new AuthGroupAccessLogic)->addToGroup($uid, $groupid, $extra, $loc_code, $loc_name);
        !$r['status'] && $this->error($r['info']);
        $this->success("操作成功~", url('Admin/AuthManage/user', ['groupid' => $groupid]));
    }

    /**
     * 将指定用户从指定用户组移除
     */
    public function delFromGroup()
    {
        $groupid =  $this->_param('groupid', -1);
        $uid = $this->_param('uid', -1);
        if ($groupid === -1 || $uid === -1) {
            $this->error("参数错误！");
        }
        $map = array('uid' => $uid, "group_id" => $groupid);

        $result = (new AuthGroupAccessLogic())->delete($map);

        if ($result['status']) {
            $this->success("操作成功~", url('Admin/AuthManage/user',['groupid' => $groupid]));
        } else {
            $this->error($result['info']);
        }

    }


}