<?php
/**
 * openIM 业务入口
 * 出错 throw Exception
 */
namespace src\alibaichuan\action;

use src\alibaichuan\helper\OpenImHelper as Helper;
use src\user\logic\UserProfileLogic as UserExtra;
use src\user\logic\UcenterMemberLogic as User;
use src\alibaichuan\service\OpenImUserService as Service;

class OpenImAction{

  /**
   * User : alibaichuan_id username id mobile email
   * UserExtra : uid realname sex nickname head
   * @Author
   * @DateTime 2017-12-25T09:21:07+0800
   * @param    int $uid [description]
   * @return   array user_info
   * @throws Exception|DbException
   */
  private function getUserInfo($uid){
    $info       = (new User)->getInfo(['id'=>$uid],false,'alibaichuan_id,username,id');
    $info_extra = (new UserExtra)->getInfo(['uid'=>$uid],false,'uid,realname,sex,nickname');
    (empty($info) || empty($info_extra)) && Helper::thr('uid非法');

    $info = array_merge($info->toArray(),$info_extra->toArray());
    return $info;
  }
  /**
   * 注册  openIM 默认规则
   * @Author
   * @DateTime 2017-12-23T13:59:51+0800
   * @param    int $uid [description]
   * @return   string userid
   * @throws   Exception|DbException
   */
  public function reg($uid){
    // ? uid
    $info = $this->getUserInfo($uid);
    // ? 已注册
    $info['alibaichuan_id'] && Helper::thr('重复注册');
    // 默认注册
    $userid = Helper::getOpenId($uid);
    $add = [
      'userid'   => $userid,
      'nick'     => $info['nickname'],
      'password' => Helper::getOpenPass($uid),
      'icon_url' => Helper::getAvatarUrl($uid),
      'name'     => $info['realname'],
    ];
    $userid = (new Service)->add($add);
    // 保存
    (new User)->saveById($uid,['alibaichuan_id'=>$userid]);
    // 成功
    return $userid;
  }

  /**
   * 删除 IM关联
   * @Author
   * @DateTime 2017-12-25T08:59:51+0800
   * @param    string  $uids '$uid1,..'
   * @return   string  suc_msg
   * @throws   Exception|DbException
   */
  public function dels($uids=''){
    $uids = explode(',', $uids);
    count($uids)<1 && Helper::thr('参数非法');
    $userids = '';
    foreach ($uids as $uid) {
      // ? uid
      $info = $this->getUserInfo($uid);
      empty($info['alibaichuan_id']) && Helper::thr('无需删除:'.$uid);
      $userids .= ($userids ? ',':'').$info['alibaichuan_id'];
    }
    $msg = (new Service)->del($userids);
    // 保存
    (new User)->save(['id'=>['in',$uids]],['alibaichuan_id'=>'']);
    // 成功
    return $msg;
  }

  /**
   * 查询 IM用户信息
   * @Author
   * @DateTime 2017-12-25T08:59:51+0800
   * @param    string $uids '$uid1,..'
   * @return   array  user_info[]
   * @throws   Exception|DbException
   */
  public function gets($uids=''){

    $uids = explode(',', $uids);
    count($uids)<1 && Helper::thr('参数非法');
    $userids = '';
    foreach ($uids as $uid) {
      // ? uid
      $info = $this->getUserInfo($uid);
      empty($info['alibaichuan_id']) && Helper::thr('IM信息异常:'.$uid);
      $userids .= ($userids ? ',':'').$info['alibaichuan_id'];
    }

    $r = (new Service)->get($userids);
    return $r;
  }

  /**
   * 更新  openIM 信息
   * @Author
   * @DateTime 2017-12-23T13:59:51+0800
   * @param    int $uid [description]
   * @return   string suc_msg
   * @throws   Exception|DbException
   */
  public function upd($uid,$nick=true,$icon=true,$name=true){
    // ? uid
    $info = $this->getUserInfo($uid);
    empty($info['alibaichuan_id']) && Helper::thr('IM信息异常:'.$uid);
    $userid = $info['alibaichuan_id'];
    $nick===true && $nick = $info['nickname'];
    $icon===true && $icon = Helper::getAvatarUrl($uid);
    $name===true && $name = $info['realname'];
    $r = (new Service)->update($userid,$nick,$icon,$name);
    return $r;
  }
}
