<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-07-05 17:22:34
 * Description : [Description]
 */

namespace app\domain;
use app\src\bbs\logic\BbsLogicV2;
use app\src\bbs\logic\BbsPostLogicV2;
use app\src\bbs\logic\BbsReplyLogicV2;
use app\src\bbs\action\BbsAction;

/**
 * [simple_description]
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\
 * @example
 */
class BbsDomain extends BaseDomain {

  // 论坛 设置/板块/max_post_img/max_reply_img
  public function index(){
    $this->checkVersion("100");
    $size = $this->_post('size/d',10);
    $uid  = $this->_post('uid/d',0);
    $r = (new BbsAction) -> index(['size'=>$size,'uid'=>$uid]);
    $this->exitWhenError($r,true);
  }

  // 帖子 列表 + attach
  public function listPost(){
    $this->checkVersion("100");
    $page = $this->_post('page/d',1);
    $size = $this->_post('size/d',10);
    $uid  = $this->_post('uid/d',0);
    $kword= $this->_post('kword/s','');
    $r = (new BbsAction) -> listPost(['page'=>$page,'size'=>$size,'uid'=>$uid,'kword'=>strip_tags($kword)]);
    $this->apiReturnSuc($r);
  }

  // 帖子 详情 + attach
  public function detail(){
    $this->checkVersion("100");
    $id  = $this->_post('id/d',0,Llack('id'));
    $uid = $this->_post('uid/d',0);

    $r  = (new BbsAction) -> detail(['id'=>$id,'uid'=>$uid]);
    $this->exitWhenError($r,true);
  }

  // 回复 列表
  public function listReply(){
    $this->checkVersion("100");
    $params = $this->parsePost('uid|0|int,pid|0|int','rid|0|int,page|1|int,size|10|int');
    $r = (new BbsAction) -> listReply($params);
    $this->apiReturnSuc($r);
  }

  // 点赞
  public function like(){
    $this->checkVersion("100");
    $params = $this->parsePost('uid|0|int,pid|0|int','rid|0|int,like|1|int');
    $r = (new BbsAction) -> like($params);
    $this->exitWhenError($r,true);
  }
  // 举报
  public function report(){
    $this->checkVersion("100");
    $params = $this->parsePost('uid|0|int,pid|0|int,reason','rid|0|int');
    $r = (new BbsAction) -> report($params);
    $this->exitWhenError($r,true);
  }

  // 转发
  public function repeat(){
    $this->checkVersion("100");
    $params = $this->parsePost('uid|0|int,content,app,repeat_id|0|int','reply_limit|0|int');
    $r = (new BbsAction) -> repeat($params);
    $this->exitWhenError($r,true);
  }

  // 发帖 + 置顶(看ui,积分) + 禁止回复 + 检查(板块开闭,禁言,积分,attach)
  public function addPost(){
    $this->checkVersion("100");
    $params = $this->parsePost('uid|0|int,content,title,app','img||mulint,reply_limit|0|int,top|0|int');
    $r  = (new BbsAction) -> addPost($params);
    $this->exitWhenError($r,true);
  }

  //回复 + 检查(板块开闭,过滤,禁言,帖子禁止,attach)
  public function addReply(){
    $this->checkVersion("100");
    $params = $this->parsePost('uid|0|int,content,app,pid|0|int','to_uid|0|int,rid|0|int,img||mulint');
    $r  = (new BbsAction) -> addReply($params);
    $this->exitWhenError($r,true);
  }

  // 我的 帖子
  public function myPost(){
    $this->checkVersion("100");

    $page = $this->_post('page/d',1);
    $size = $this->_post('size/d',10);
    $uid  = $this->_post('uid/d',0);
    $r = (new BbsAction) -> myPost(['page'=>$page,'size'=>$size,'uid'=>$uid]);
    $this->exitWhenError($r,true);
  }

  // 删除 自己的帖子
  public function delPost(){
    $this->checkVersion("100");

    $pid = $this->_post('pid/d',0);
    $uid = $this->_post('uid/d',0);
    $r = (new BbsAction) -> delPost(['pid'=>$pid,'uid'=>$uid]);
    $this->exitWhenError($r,true);
  }
  // 删除 自己的评论
  public function delReply(){
    $this->checkVersion("100");

    $rid = $this->_post('rid/d',0);
    $uid = $this->_post('uid/d',0);
    $r = (new BbsAction) -> delReply(['rid'=>$rid,'uid'=>$uid]);
    $this->exitWhenError($r,true);
  }

  // 签到
  public function sign(){
    $this->checkVersion("100");
    $uid = $this->_post('uid/d',0);
    $r = (new BbsAction) -> sign(['uid'=>$uid]);
    $this->exitWhenError($r,true);
  }
  // 签到详情
  public function signDetail(){
    $this->checkVersion("100");
    $uid = $this->_post('uid/d',0);
    $r = (new BbsAction) -> signDetail(['uid'=>$uid]);
    $this->exitWhenError($r,true);
  }
}