<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-07-07 09:58:13
 * Description : [Description]
 */

namespace src\bbs;

use src\base\BaseAction;
use src\bbs\bbs\BbsLogic;
// use app\src\banners\logic\BannersLogic;
// use app\src\bbs\logic\BbsPostLogicV2;
// use app\src\bbs\logic\BbsReplyLogicV2;
// use app\src\bbs\logic\BbsAttachLogicV2;
// use app\src\bbs\logic\LikeLogicV2;
// use app\src\bbs\logic\ReportLogicV2;
// use app\src\bbs\logic\BbsBanLogicV2;
// use think\Db;
// use app\src\message\enum\MessageType;
// use app\src\message\facade\MessageFacade;
// use app\src\system\logic\SignLogicV2;
// use app\src\user\logic\MemberLogic;
// use app\src\wallet\logic\ScoreHisLogicV2;
// use app\src\system\logic\ConfigLogic;

class BbsAction extends BaseAction{
  public $block = BbsLogic::DEFAULT_BLOCK;

  //签到
  //param ：uid
  public function sign($param){

    extract($param);
    $today = date('Y-m-d');
    $time = strtotime($today);
    $end   = NOW_TIME;
    $r = (new SignLogicV2)->getInfo(['uid'=>$uid,'sign_in_time'=>['between',[$time-86400,$end]]],'sign_in_time desc','sign_in_time as time,continues_signin as last');

    $info = $r;
    $last_time = $info['time'];
    $last_days = 0;
    if($last_time >= $time && $last_time < $time+86400){
      //今天 签到
      return returnErr('重复签到');
    }elseif($last_time >= $time-86400 && $last_time < $time){
      //昨天 签到
      $sign_yesterday = 1;
      $last_days = $info['last'];
    }else{
      $sign_yesterday = 0;
    }

    $score_type = ScoreHisLogicV2::SIGN_ADD;
    $r = (new ScoreHisLogicV2)->getOpScore($score_type,$last_days);
    if(!$r['status']) return $r;
    $sign_score = intval($r['info']);
    $add = [
      'uid'              =>$uid,
      'sign_in_time'     =>NOW_TIME,
      'continues_signin' =>$last_days+1,
    ];

    Db::startTrans();
    $id = (new SignLogicV2)->add($add);
    if(!$id) return returnErr('签到失败',true);
    if($sign_score){
      // + score  + scorelog
      $r = (new ScoreHisLogicV2)->addScore($uid,$sign_score,$score_type,$sign_yesterday ? '连续签到' : '签到');
      if(!$r['status']) return returnErr($r['info'],true);
    }
    Db::commit();
    return returnSuc('积分 +'.$sign_score);
  }
  //签到 - 详情
  //param ：uid
  public function signDetail($param){
    extract($param);
    $today = date('Y-m-d');
    $time  = strtotime($today);
    $start = strtotime(date('Y-m-01'));
    $end   = NOW_TIME;
    $r = (new SignLogicV2)->queryNoPaging(['uid'=>$uid,'sign_in_time'=>['between',[$start,$end]]],'sign_in_time asc','sign_in_time as time,continues_signin as last');
    $list = $r;
    $sign_today = 0;
    $last_days  = 0;
    $list_ret = [];
    $days = date('t');
    for ($i=1; $i <= $days; $i++) {
      if(!isset($list_ret[$i])){
        $list_ret[$i] = [
         'time'=>0,'last'=>0,'day'=>$i,
        ];
      }
    }
    if($list){
      foreach ($list as $v) {
        $v['day'] = (int) date('d',$v['time']);
        $list_ret[$v['day']] = $v;
      }
      $last_sign  = end($list);unset($list);
      $last_time  = $last_sign['time'];
      if($last_time >= $time && $last_time < $time+86400){ //今天 签到
        $sign_today = 1;
        $last_days = $last_sign['last'];
      }elseif($last_time >= $time-86400 && $last_time < $time){ //昨天 签到
        $last_days = $last_sign['last'];
      }
    }
    ksort($list_ret);
    $r = (new ScoreHisLogicV2)->getOpScore(ScoreHisLogicV2::SIGN_ADD,$sign_today ? $last_days-1 : $last_days);
    if(!$r['status']) return $r;
    $sign_score = intval($r['info']);
    $ret = [];
    $r = (new ScoreHisLogicV2)->getUserScore($uid);
    if(!$r['status']) return $r;
    $ret['score'] = intval($r['info']);

    $ret['sign_last_days'] = $last_days;
    $ret['sign_today'] = $sign_today;
    $ret['sign_score'] = $sign_score;
    $ret['sign_info']  = array_values($list_ret);
    return returnSuc($ret);
  }
  //param : uid,pid,rid,reason
  public function report($param){
    extract($param);
    // ? pid
    $r = (new BbsPostLogicV2)->getInfo(['id'=>$pid]);
    if(!$r) return returnErr('错误pid');
    $type      = $rid ? 2 : 1;
    $report_id = $rid ? $rid : $pid;
    $r = (new ReportLogicV2)->getInfo(['uid'=>$uid,'report_id'=>$report_id,'type'=>$type]);
    if($r) return returnErr('重复举报');
    $add = [
      'reason'    =>strip_tags($reason),
      'uid'       =>$uid,
      'type'      =>$type,
      'report_id' =>$report_id,
      'result'    =>'',
      'op_time'   =>0,
      'create_time'=>NOW_TIME
    ];
    $id = (new ReportLogicV2)->add($add);
    if($id) return returnSuc('举报成功');
    else return returnErr('举报失败');
  }
  //param : uid ,pid,rid
  public function like($param){
    extract($param);
    // ? pid
    $r = (new BbsPostLogicV2)->getInfo(['id'=>$pid]);
    if(!$r) return returnErr('错误pid');
    $author = $r['uid'];
    if($rid){
      // ? rid
      $r = (new BbsPostLogicV2)->getInfo(['id'=>$rid,'rid'=>0,'pid'=>$pid]);
      if(!$r) return returnErr('错误rid');
      $author = $r['uid'];
    }
    $type_id = $rid ? LikeLogicV2::BBS_REPLY : LikeLogicV2::BBS_POST;
    $like_id = $rid ? $rid : $pid;

    // ? uid pid 点赞过
    $r = (new LikeLogicV2)->getInfo(['uid'=>$uid,'like_id'=>$like_id,'type_id'=>$type_id]);
    if($like){ // 点赞
      if($r) return returnErr('点赞过了');
      $add = [
        'like_id' =>$like_id,
        'type_id' =>$type_id,
        'uid'     =>$uid,
      ];
      Db::startTrans();
      $id = (new LikeLogicV2)->add($add);
      if(!$id) return returnErr('点赞失败',true);

      //被点赞 +score
      $score_type = ScoreHisLogicV2::BBS_POST_LIKE_ADD;
      $r  = (new ScoreHisLogicV2)->getOpScore($score_type);
      if(!$r['status']) return returnErr($r['info'],true);
      $score = $r['info'];
      $reason = ( $rid ? '评论' : '帖子' ).'被点赞';
      $r = (new ScoreHisLogicV2)->addScore($author,$score,$score_type,$reason);
      if(!$r['status']) return returnErr($r['info'],true);

      Db::commit();
      return returnSuc('点赞成功');
    }else{ // 取消点赞

      // if(!$r) return returnErr('并未点赞');
      // $id = $r['id'];
      // Db::startTrans();
      // if(!(new LikeLogicV2)->delete(['id'=>$id])) return returnErr('取消点赞失败',true);
      //
      // Db::commit();
      return returnSuc('取消成功 - 操作未明确');
    }
  }

  //param : pid,rid,page,size
  public function listReply($param){
    extract($param);
    // ? pid
    $r = (new BbsPostLogicV2)->getInfo(['id'=>$pid]);
    if(!$r) return returnErr('错误pid');
    if($rid){
      $r = (new BbsReplyLogicV2)->getInfo(['id'=>$rid],false,'id,pid,rid,to_uid,uid,create_time,content');
      if(!$r) return returnErr('错误rid');
      $info = $r;
      $info['uname'] = get_nickname($info['uid']);
      $info['to_uname'] = get_nickname($info['to_uid']);
      // img
      $r = (new BbsAttachLogicV2)->query(['pid'=>$pid,'rid'=>$rid],['curpage'=>1,'size'=>BbsLogicV2::MAX_REPLY_IMG],false,false,'img');
      $info['img'] = array_keys(changeArrayKey($r['list'],'img'));
      // 时间转换
      $info['create_time_desc'] = getDateDesc($info['create_time'],'Y-m-d H:i:s');
      // 下级回复
      $r = (new BbsReplyLogicV2)->getReply($pid,$rid,$page,$size);
      $info['replys_info']  = $r['list'];
      $info['replys_count'] = $r['count'];
      return $info;
    }else{
      $r = (new BbsReplyLogicV2)->getReply($pid,0,$page,$size);
      $list  = $r['list'];
      $count = $r['count'];
      foreach ($list as &$v) {
        $r = (new BbsReplyLogicV2)->getReply($pid,$v['id'],1,5);
        $v['replys_info']  = $r['list'];
        $v['replys_count'] = $r['count'];
      } unset($v);
      return ['list'=>$list,'count'=>$count];
    }
  }

  //param : uid,repeat_id,content,reply_limit,app
  public function repeat($param){
    extract($param);
    // repeat_id
    $r = (new BbsPostLogicV2)->getInfo(['id'=>$repeat_id,'repeat_id'=>0]);
    if(!$r) return returnErr('需要原贴id');
    $title  = $r['title'];
    $author = $r['uid'];
    // block
    $r = (new BbsLogicV2)->checkBlock($this->block);
    if(!$r['status']) return $r;
    $auth = $r['info']['auth'];

    // 检查 禁言
    $r = (new BbsBanLogicV2)->isBan($uid,BbsBanLogicV2::BAN_POST);
    if(false !== $r) return returnErr('禁止发帖,过期时间:'.($r ? date('Y-m-d H:i:s') : '永久'));
    // app
    if(!BbsLogicV2::apps($app)) return returnErr(Linvalid('app'));
    // ? 转发过
    $r = (new BbsPostLogicV2)->getInfo(['uid'=>$uid,'repeat_id'=>$repeat_id,'status'=>['in','0,1']]);
    if($r) return returnErr('您已转发过此贴了');

    $status = intval(!$auth);
    $checkScore = !$auth;
    if($checkScore){ //不审核
      $score_cut = 0;
      if($reply_limit){
        $r = (new ScoreHisLogicV2)->getOpScore(ScoreHisLogicV2::BBS_POST_LIMIT_CUT);
        if(!$r['status']) return $r;
        $score_cut +=$r['info'];
      }
      $r = (new ScoreHisLogicV2)->getOpScore(ScoreHisLogicV2::BBS_POST_REPEAT_ADD);
        if(!$r['status']) return $r;
      $score_add = $r['info'];
    }
    // 过滤词=> ***
    $content = (new BbsLogicV2)->filter($content);
    $add = [
      'uid'         =>$uid,
      'title'       =>$title,
      'status'      =>$status,
      'content'     =>$content,
      'repeat_id'   =>$repeat_id,
      'reply_limit' =>$reply_limit,
      'app'         =>$app,
      'ip'      =>get_client_ip(1),
      'tid'     =>$this->block,
      'special' =>0,
      'top'     =>0,
    ];
    Db::startTrans();
    $id = (new BbsPostLogicV2)->add($add);
    if($checkScore){ //不审核
      // + 积分
      $r = (new ScoreHisLogicV2)->addScore($author,$score_add,ScoreHisLogicV2::BBS_POST_REPEAT_ADD,'帖子被转发');
      if(!$r['status']) return returnErr($r['info'],true);
      // - 积分
      if($score_cut){
        $r = (new ScoreHisLogicV2)->cutScore($uid,$score_cut,ScoreHisLogicV2::BBS_POST_LIMIT_CUT,'帖子关闭评论');
        if(!$r['status']) return returnErr($r['info'],true);
      }
    }
    if(!$id) return returnErr('转发失败',true);
    Db::commit();
    return returnSuc('转发成功');
  }

  //param : uid,img,app,content,to_uid,pid,rid
  //评论帖子作者获得积分
  public function addReply($param){
    extract($param);
    // block
    $r = (new BbsLogicV2)->checkBlock($this->block);
    if(!$r['status']) return $r;
    // pid
    $r = (new BbsPostLogicV2)->getInfo(['id'=>$pid,'status'=>1]);
    if(!$r) return returnErr('非正常贴');
    if($r['reply_limit']) return returnErr('该贴禁止任何回复');
    $author = $r['uid'];
    $post_author = $r['uid'];
    //rid author(rid)
    if($rid){
      $r = (new BbsReplyLogicV2)->getInfo(['id'=>$rid]);
      if(!$r) return returnErr('错误rid');
      $author = $r['uid'];
    }
    // 检查 禁言
    $r = (new BbsBanLogicV2)->isBan($uid,BbsBanLogicV2::BAN_REPLY);
    if(false !== $r) return returnErr('禁止回复,过期时间:'.($r ? date('Y-m-d H:i:s') : '永久'));
    //过滤
    $content = (new BbsLogicV2)->filter($content);
    //该用户上次回复的内容
    $r = (new BbsReplyLogicV2)->getInfo(['content'=>$content,'uid'=>$uid,'pid'=>$pid,'rid'=>$rid]);
    if($r) return returnErr('禁止重复灌水');
    // app
    if(!BbsLogicV2::apps($app)) return returnErr(Linvalid('app'));
    // 检查图片数量
    $imgs = [];
    $max  = BbsLogicV2::MAX_REPLY_IMG;
    $imgs_src = $img ? array_unique(explode(',', $img)) : [];
    foreach ($imgs_src as $v) {
      if($v && $v>0) $imgs[] = intval($v);
    }
    if(count($imgs)>$max) return returnErr('图片最多'.$max.'张');
    $to_uid = $to_uid ? $to_uid : $author;
    $add = [
      'content'=>$content,
      'uid'    =>$uid,
      'to_uid' =>$to_uid,
      'pid'=>$pid,
      'rid'=>$rid,
      'app'=>$app,
      'tid'=>$this->block,
      'ip' =>get_client_ip(1),
    ];
    Db::startTrans();
    $id = (new BbsReplyLogicV2)->add($add);
    if($id){
      $r = (new BbsAttachLogicV2)->setImgs($pid,$imgs,$id);
      if(!$r['status']) return returnErr($r['info'],true);
      if($uid != $to_uid){
        // 发送论坛消息
        $msg = [
          'uid'      =>$uid,
          'to_uid'   =>$to_uid,
          'title'    =>$rid ? '新回复' : '新评论',
          'content'  =>strip_tags($content),
          'extra'    =>$pid,
          'summary'  =>'',
          'msg_type' =>MessageType::BBS,
        ];
        $r = (new MessageFacade)->addMsg($msg);
        if(!$r['status']) return returnErr($r['info'],true);
      }
      if($uid != $post_author){
        // 获得积分
        $r = (new ScoreHisLogicV2)->changeScoreByRule($post_author,ScoreHisLogicV2::BBS_POST_REPLY_ADD,true);
        if(!$r['status']) return returnErr($r['info'],true);
      }

      Db::commit();
      return returnSuc('回复成功');
    }else{
      Db::rollback();
      return returnErr('回复失败');
    }

  }

  //param : uid,img,title,content,reply_limit,top,app
  //限制每日发帖数量,发帖审核通过获得积分 2017-09-07 14:22:02
  public function addPost($param){
    extract($param);
    // title检查
    $title = (new BbsLogicV2)->filter($title);
    $r = (new BbsPostLogicV2)->checkTitle($title);
    if(!$r['status']) return returnErr($r['info']);
    $r = (new BbsLogicV2)->checkBlock($this->block);
    if(!$r['status']) return $r;
    $auth = $r['info']['auth'];
    $status = intval(!$auth);
    $checkScore = !$auth;

    // 发帖数 限制
    $post_today_num = (new BbsPostLogicV2)->count(['status'=>['in',[0,1]],'create_time'=>['egt',strtotime(date('Y-m-d'))]]);
    $r = (new ConfigLogic)->getCacheConfig('BBS_POST_DAILY_LIMIT',0);
    if(!$r['status']) return $r;
    $post_daily_limit = intval($r['info']);
    if($post_today_num >= $post_daily_limit){
      return returnErr('每日发帖数不得超过'.$post_daily_limit);
    }
    if($checkScore){ //不审核
      // 发帖消耗(-发帖获得+置顶消耗+关闭回复消耗)
      $r = (new ScoreHisLogicV2)->postAddCost($reply_limit,$top);
      if(!$r['status']) return $r;
      $score_cut = intval($r['info']);
      if($score_cut >0){
        // 积分检查
        $r = (new ScoreHisLogicV2)->isScoreEnough($uid,$score_cut);
        if(!$r['status']) return $r;
      }
    }
    // 检查 禁言
    $r = (new BbsBanLogicV2)->isBan($uid,BbsBanLogicV2::BAN_POST);
    if(false !== $r) return returnErr('禁止发帖,过期时间:'.($r ? date('Y-m-d H:i:s') : '永久'));
    // 过滤词=> ***
    $content = (new BbsLogicV2)->filter($content);
    // app
    if(!BbsLogicV2::apps($app)) return returnErr(Linvalid('app'));
    // 检查图片数量
    $imgs = [];
    $max  = BbsLogicV2::MAX_POST_IMG;
    $imgs_src = $img ? array_unique(explode(',', $img)) : [];
    foreach ($imgs_src as $v) {
      if($v && $v>0) $imgs[] = intval($v);
    }
    if(count($imgs)>$max) return returnErr('图片最多'.$max.'张');

    $add = [
      'title'     =>$title,
      'content'   =>$content,
      'uid'       =>$uid,
      'status'    =>$status,
      'top'       =>$top,
      'special'   =>0,
      'repeat_id' =>0,
      'app'       =>$app,
      'ip'        =>get_client_ip(1),
      'tid'       =>$this->block,
      'reply_limit'=>$reply_limit,
    ];
    Db::startTrans();
    $id = (new BbsPostLogicV2)->add($add);
    if(!$id) return returnErr('发帖失败',true);
    //add imgs
    $r = (new BbsAttachLogicV2)->setImgs($id,$imgs);
    if(!$r['status']) return returnErr($r['info'],true);
    // 检查 积分
    if($checkScore){ //不审核
      $typ = ScoreHisLogicV2::BBS_POST_ADD_ADD;
      $msg = '发帖'.($reply_limit ? ',关闭评论' : '').($top ? ',置顶' : '');
      if($score_cut>0){
        $r = (new ScoreHisLogicV2)->cutScore($uid,$score_cut,$typ,$msg);
      }elseif($score_cut<0){
        $r = (new ScoreHisLogicV2)->addScore($uid,-intval($score_cut),$typ,$msg);
      }
      if(!$r['status']) return returnErr($r['info'],true);
    }
    Db::commit();
    return returnSuc('发帖成功');
  }

  // param : id uid
  public function detail($param){
    extract($param);
    $r  = $this -> getDetail($id,$uid,1);
    if(!$r['status']) return $r;
    //views + 1
    (new BbsPostLogicV2)->setInc(['id'=>$id],'views',1);
    return $r;
  }
  // param : id uid status
  // 注意: 有相互调用 和 自身调用,统计放外面,只做查询
  public function getDetail($id,$uid,$status=false){

    $field = 'id,uid,title,create_time,update_time,content,top,special,reply_limit,views,repeat_id';
    $map = ['id'=>$id];
    if(false !== $status) $map['status'] = $status;
    $info = (new BbsPostLogicV2)->getInfo($map,false,$field);
    if(!$info) return returnErr('帖子错误或审核中');

    $info['uname'] = get_nickname($info['uid']);
    // 回复统计
    $r = (new BbsReplyLogicV2)->countReply($id);
    $info['replys_count'] = $r[0];
    $info['replys_direct_count'] = $r[1];
    // 图片 (max:10)
    $r = (new BbsAttachLogicV2)->query(['pid'=>$id,'rid'=>0],['curpage'=>1,'size'=>BbsLogicV2::MAX_POST_IMG],false,false,'img');
    $info['img'] = array_keys(changeArrayKey($r['list'],'img'));
    $info['has_like'] = (new LikeLogicV2)->hasLikePost($uid,$id);
    $info['likes'] = (new LikeLogicV2)->countPost($id);
    // 时间转换
    $info['create_time_desc'] = getDateDesc($info['create_time'],'Y-m-d H:i:s');
    $info['update_time_desc'] = getDateDesc($info['update_time'],'Y-m-d H:i:s');
    //转发信息
    if($info['repeat_id'] > 0){ //转发
      $r = $this->getDetail($info['repeat_id'],$uid);
      $info['repeat_count'] = (new BbsPostLogicV2)->count(['repeat_id'=>$info['repeat_id']]);
      if(!$r['status']) return $r;
      $info['repeat_info'] = $r['info'];
    }else{
      $info['repeat_info']  = ['temp'=>''];
      $info['repeat_count'] = (new BbsPostLogicV2)->count(['repeat_id'=>$info['id']]);
    }
    return returnSuc($info);
  }

  //param : uid,pid
  //删除帖子，api只能删自己的
  public function delPost($param){
    extract($param);
    $r = (new BbsPostLogicV2)->save(['uid'=>$uid,'id'=>$pid],['status'=>-1]);
    if($r) return returnSuc('删除成功');
    else returnErr('删除失败');
  }

  //param : uid,rid
  //删除回复，api只能删自己的
  public function delReply($param){
    extract($param);
    $r = (new BbsReplyLogicV2)->delete(['uid'=>$uid,'id'=>$rid]);
    if($r) return returnSuc('删除成功');
    else returnErr('删除失败');
  }

  // => 论坛消息列表
  //param : uid,page,size
  // public function replyMe($param){
  //   extract($param);
  //   $r = (new BbsPostLogicV2)->replyMe($uid,$page,$size);
  //   return returnSuc($r);
  // }

  //param : uid,page,size
  public function myPost($param){
    extract($param);

    $field = 'id,uid,title,create_time,update_time,content,top,special,reply_limit,views,repeat_id';
    $r = (new BbsPostLogicV2)->query(['uid'=>$uid,'status'=>1],['curpage'=>$page,'size'=>$size],'create_time desc',false,$field);
    $list  = $r['list'];
    $count = $r['count'];
    foreach ($list as &$v) {
      $v['uname'] = get_nickname($v['uid']);
      // 回复统计
      $r = (new BbsReplyLogicV2)->countReply($v['id']);
      $v['replys_count'] = $r[0];
      $v['replys_direct_count'] = $r[1];
      // 图片 (max:10)
      $r = (new BbsAttachLogicV2)->query(['pid'=>$v['id'],'rid'=>0],['curpage'=>1,'size'=>BbsLogicV2::MAX_POST_IMG],false,false,'img');
      $v['img'] = array_keys(changeArrayKey($r['list'],'img'));
      $v['content'] = BbsPostLogicV2::subPureContent($v['content']);

      $v['has_like'] = (new LikeLogicV2)->hasLikePost($v['uid'],$v['id']);
      $v['likes'] = (new LikeLogicV2)->countPost($v['id']);
      $v['create_time_desc'] = getDateDesc($v['create_time'],'Y-m-d');
      // 转发信息
      if($v['repeat_id']>0){
        $r = $this->getDetail($v['repeat_id'],$uid);
        if(!$r['status']) return $r;
        $r['info']['content'] = BbsPostLogicV2::subPureContent($r['info']['content']);
        $v['repeat_info'] = $r['info'];
        $v['repeat_count'] = $r['info']['repeat_count'];
      }else{
        $v['repeat_info'] = ['temp'=>''];
        $v['repeat_count'] = 0;
      }
    } unset($v);
    return returnSuc(['list'=>$list,'count'=>$count]);
  }
  // param : size , page , uid , kword
  public function listPost($param){

    extract($param);
    $map = ['tid'=>$this->block,'status'=>1];
    if($kword) $map['title'] = ['like','%'.$kword.'%'];
    $field = 'id,uid,title,create_time,update_time,content,top,special,reply_limit,views,repeat_id';
    $r = (new BbsPostLogicV2)->query($map,['curpage'=>$page,'size'=>$size],'create_time desc',false,$field);
    $list  = $r['list'];
    $count = $r['count'];
    foreach ($list as &$v) {
      $v['uname'] = get_nickname($v['uid']);
      // 回复统计
      $r = (new BbsReplyLogicV2)->countReply($v['id']);
      $v['replys_count'] = $r[0];
      $v['replys_direct_count'] = $r[1];
      // 图片 (max:10)
      $r = (new BbsAttachLogicV2)->query(['pid'=>$v['id'],'rid'=>0],['curpage'=>1,'size'=>BbsLogicV2::MAX_POST_IMG],false,false,'img');
      $v['img'] = array_keys(changeArrayKey($r['list'],'img'));
      $v['content'] = BbsPostLogicV2::subPureContent($v['content']);

      $v['has_like'] = (new LikeLogicV2)->hasLikePost($uid,$v['id']);
      $v['likes'] = (new LikeLogicV2)->countPost($v['id']);
      // 时间转换
      $v['create_time_desc'] = getDateDesc($v['create_time'],'Y-m-d');
      // 转发信息
      if($v['repeat_id']>0){
        $r = $this->getDetail($v['repeat_id'],$uid);
        if(!$r['status']) return $r;
        $r['info']['content'] = BbsPostLogicV2::subPureContent($r['info']['content']);
        $v['repeat_info'] = $r['info'];
        $v['repeat_count'] = $r['info']['repeat_count'];
      }else{
        $v['repeat_info'] = ['temp'=>''];
        $v['repeat_count'] = 0;
      }
    } unset($v);

    return ['count'=>$count,'list'=>$list];
  }
  // param : size , uid
  // 调用 listPost
  public function index($param){
    extract($param);
    $ret = [];
    // banner
    $map = ['position' => BbsLogicV2::BANNER_POSITION];
    $order = " sort desc ";
    $field ='url,url_type,notes,img,title';
    $r = (new BannersLogic)->queryNoPaging($map,$order,$field);
    if(!$r['status']) return $r;
    $ret['banners'] = $r['info'];
    // 置顶帖
    $r = (new ConfigLogic)->getCacheConfig('APP_TOP_POST_NUM');
    if(!$r['status']) return $r;
    $post_size = intval($r['info']);
    $field = 'id,uid,title,create_time,update_time,views';
    $r = (new BbsPostLogicV2)->query(['tid'=>$this->block,'top'=>1,'status'=>1],['curpage'=>1,'size'=>$post_size],'update_time desc',false,$field);
    $list  = $r['list'];
    $count = $r['count'];
    foreach ($list as &$v) {
      $v['uname'] = get_nickname($v['uid']);
      // 回复统计
      $r = (new BbsReplyLogicV2)->countReply($v['id']);
      $v['replys_count'] = $r[0];
      $v['replys_direct_count'] = $r[1];
      // 时间描述
      $v['update_time_desc'] = getDateDesc($v['update_time'],'Y-m-d');;
    } unset($v);
    $ret['posts_top'] = $list;
    $ret['posts_top_count'] = $count;

    $r = $this->listPost(['page'=>1,'size'=>$size,'uid'=>$uid,'kword'=>'']);
    $ret['posts_all'] = $r['list'];
    $ret['posts_all_count'] = $r['count'];
    return returnSuc($ret);
  }
}