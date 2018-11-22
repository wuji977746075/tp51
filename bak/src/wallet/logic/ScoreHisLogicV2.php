<?php


namespace app\src\wallet\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\wallet\model\ScoreHis;
use think\Db;
use app\src\user\logic\MemberConfigLogicV2;
use app\src\system\logic\ConfigLogic;

class ScoreHisLogicV2 extends BaseLogicV2 {


  const SIGN_ADD = 1; // 签到/连续签到
  const PAY_ADD  = 2; // 消费返利
  const PAY_CUT  = 3; // 消费抵扣
  const SYS_ADD  = 4; // 系统发放
  const REG_INVITED_ADD = 5; // 被邀请注册
  const REG_ADD  = 6;      // 新用户注册
  const REALNAME_ADD = 7;  // 实名认证
  const CHARGE_ADD = 8;    // 充值返利
  const SYS_CUT  = 9; // 系统扣除
  const REG_INVITER_ADD  = 10; // 邀请注册推荐人

  const BBS_POST_LIMIT_CUT  = 11; //帖子关闭回复
  const BBS_POST_TOP_CUT    = 12; //帖子置顶
  const BBS_POST_ADD_ADD    = 13; //发帖
  const BBS_POST_LIKE_ADD   = 14; //帖子被点赞
  const BBS_POST_REPEAT_ADD = 15; //帖子被转发
  const BBS_POST_REPLY_ADD  = 16; //帖子被评论

  const REPAIR_DONE_WORKER_ADD = 21; //维修完结-技工
  const REPAIR_DONE_DRIVER_ADD = 22; //维修完结-司机

  //返回所有积分变动类型
  public function getTypes(){
    return [1=>'签到',2=>'消费返利',3=>'消费抵扣',4=>'系统发放',5=>'邀请注册',6=>'注册',7=>'实名认证',8=>'充值返利',9=>'系统扣除',10=>'邀请他人注册',
    11=>'帖子关闭回复',12=>'帖子置顶',13=>'发帖',14=>'帖子被点赞',15=>'帖子被转发',16=>'帖子被评论',
    21=>'维修完结-技工',22=>'维修完结-司机'
    ];
  }

  // 发帖消耗
  // limit:关闭评论 top:置顶
  public function postAddCost($limit,$top){
    // 发帖消耗
    $r = $this->getOpScore(self::BBS_POST_ADD_ADD);
    if(!$r['status']) return $r;
    $score_cut = -intval($r['info']);
    //关闭回复消耗
    if($limit){
      $r = $this->getOpScore(self::BBS_POST_LIMIT_CUT);
      if(!$r['status']) return $r;
      $score_cut += intval($r['info']);
    }
    //置顶消耗
    if($top){
      $r = $this->getOpScore(self::BBS_POST_TOP_CUT);
      if(!$r['status']) return $r;
      $score_cut += intval($r['info']);
    }
    return returnSuc($score_cut);
  }

  //获取操作对应的积分
  public function getOpScore($type=0,$extra=0){

    if($type == self::SIGN_ADD){ //签到  extra:前连续签到数 - ok
      $r = (new ConfigLogic)->getCacheConfig('SCORE_ADD_SIGN',0);
      if(!$r['status']) return $r;
      $base = $r['info'];
      if($extra){
        $r = (new ConfigLogic)->getCacheConfig('SCORE_ADD_LAST_SIGN',0);
        if(!$r['status']) return $r;
        $plus = $r['info'];
        return returnSuc($base+$plus);
      }else{
        return returnSuc($base);
      }
    }elseif($type == self::PAY_CUT){ //消费抵扣 比例
      $r = (new ConfigLogic)->getCacheConfig('SCORE_COST_RATE',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }elseif($type == self::PAY_ADD){ //消费返利 比例
      $r = (new ConfigLogic)->getCacheConfig('SCORE_ADD_BUY',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }elseif($type == self::SYS_ADD){ //系统发放
      return returnErr('无需配置');
    }elseif($type == self::BBS_POST_LIMIT_CUT){ //帖子关闭回复
      $r = (new ConfigLogic)->getCacheConfig('SCORE_CUT_POST_LIMIT',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }elseif($type == self::BBS_POST_TOP_CUT){ //帖子置顶
      $r = (new ConfigLogic)->getCacheConfig('SCORE_CUT_POST_TOP',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }elseif($type == self::BBS_POST_ADD_ADD){ //发帖
      $r = (new ConfigLogic)->getCacheConfig('SCORE_ADD_POST',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }elseif($type == self::BBS_POST_LIKE_ADD){ //帖子被点赞
      $r = (new ConfigLogic)->getCacheConfig('SCORE_ADD_POST_LIKE',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }elseif($type == self::BBS_POST_REPEAT_ADD){ //帖子被转发
      $r = (new ConfigLogic)->getCacheConfig('SCORE_ADD_POST_REPEAT',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }elseif($type == self::REG_INVITED_ADD){ //被邀请注册
      $r = (new ConfigLogic)->getCacheConfig('SCORE_ADD_INVITE_REG',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }elseif($type == self::REG_ADD){ //注册
      $r = (new ConfigLogic)->getCacheConfig('SCORE_ADD_REG',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }elseif($type == self::REALNAME_ADD){ //实名认证
      $r = (new ConfigLogic)->getCacheConfig('SCORE_ADD_REALNAME',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }elseif($type == self::REPAIR_DONE_WORKER_ADD){ //维修完结-技工
      $r = (new ConfigLogic)->getCacheConfig('SCORE_ADD_REPAIR_DONE_WORKER',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }elseif($type == self::REPAIR_DONE_DRIVER_ADD){ //维修完结-司机
      $r = (new ConfigLogic)->getCacheConfig('SCORE_ADD_REPAIR_DONE_DRIVER',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }elseif($type == self::CHARGE_ADD){ //充值返现
      $r = (new ConfigLogic)->getCacheConfig('SCORE_ADD_CHARGE',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }elseif($type == self::BBS_POST_REPLY_ADD){ //帖子被评论
      $r = (new ConfigLogic)->getCacheConfig('SCORE_ADD_POST_REPLY',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }elseif($type == self::REG_INVITER_ADD){ //推荐注册推荐人
      $r = (new ConfigLogic)->getCacheConfig('SCORE_ADD_REG_INVITER',0);
      return $r['status'] ? returnSuc($r['info']) : $r;
    }
    return returnErr('未知积分操作');
  }

  //初始化
  protected function _init(){
      $this->setModel(new ScoreHis());
  }

  // 由系统情况用户积分
  public function clearScore($uid=0,$reason='系统清空积分'){
    $uid = intval($uid);
    $l = (new MemberConfigLogicV2);
    if($uid>0){  // 清空某用户的积分
      $r = $this->getUserScore($uid);
      if(!$r['status']) return $r;
      $before = $r['info'];
      if(false !== $l->save(['uid'=>$uid],['score'=>0])){
        // 记录
        $add = [
          'uid'          =>$uid,
          'before_score' =>$before,
          'plus'         =>0,
          'minus'        =>$before,
          'after_score'  =>0,
          'type'         =>self::SYS_CUT,
          'reason'       =>$reason
        ];
        if($this->add($add)){
        }else{
          return returnErr('记录失败');
        };
      }else{
        return returnErr('扣除积分失败');
      };
    }elseif($uid == -1){ // 清空全体用户积分
      if(false !== $l->save(['score'=>['neq',0]],['score'=>0])){
        // 记录
        $add = [
          'uid'          =>0,
          'before_score' =>0,
          'plus'         =>0,
          'minus'        =>0,
          'after_score'  =>0,
          'type'         =>self::SYS_CUT,
          'reason'       =>$reason
        ];
        if($this->add($add)){
        }else{
          return returnErr('记录失败');
        };
      }else{
        return returnErr('扣除积分失败');
      };
    }else{
      return returnErr('参数非法:uid');
    }

    return returnSuc('操作成功');
  }

  // 由系统给用户发送积分
  public function sendUserScore(array $uids,$score=0,$reason=''){
    $score = (int) $score;
    if($score<=0) return returnErr('积分非法');
    $l = (new MemberConfigLogicV2);
    Db::startTrans();
    if($uids){ //部分用户
      $adds = [];
      foreach ($uids as $v) {
        $r = $this->getUserScore($v);
        if(!$r['status']) return returnErr($r['info'],true);
        $before = $r['info'];
        // 添加积分
        if(false !== $l->setInc(['uid'=>$v],'score',$score)){
          $adds[] = [
            'uid'          =>$v,
            'before_score' =>$before,
            'plus'         =>$score,
            'minus'        =>0,
            'after_score'  =>$before+$score,
            'type'         =>self::SYS_ADD,
            'reason'       =>$reason
          ];
        }else{
          return returnErr('添加失败',true);
        }
        // 添加记录
        if($adds){
          if($this->addAll($adds)){
          }else{
            return returnErr('添加记录失败',true);
          };
        }
      }
    }else{ //全体用户
      // 添加积分
      if(false !== $l->setInc(['uid'=>['gt',0]],'score',$score)){
        // 记录
        $add = [
          'uid'          =>0,
          'before_score' =>0,
          'plus'         =>$score,
          'minus'        =>0,
          'after_score'  =>0,
          'type'         =>self::SYS_ADD,
          'reason'       =>$reason
        ];
        if($this->add($add)){
        }else{
          return returnErr('添加记录失败',true);
        };
      }else{
        return returnErr('添加失败',true);
      };
    }
    Db::commit();
    return returnSuc('发放成功');
  }
  /**
   * 获取用户积分
   */
  public function getUserScore($uid){
    $r = (new MemberConfigLogicV2)->getInfo(['uid'=>$uid]);
    if(!$r) return returnErr('uid错误');
    return returnSuc(intval($r['score']));
  }

  public function isScoreEnough($uid,$score){
    $score = intval($score);
    $r = $this->getUserScore($uid);
    if(!$r['status']) return $r;
    if($r['info']< $score) return returnErr('积分不足');
    return returnSuc('pass');
  }

  // 根据规则 增加或减少用户的积分
  public function changeScoreByRule($uid,$type,$addOrCut,$msg='',$extra=0){
    $type_str = $this->getTypes();
    $msg = $msg ? $msg : (isset($type_str[$type]) ? $type_str[$type] : '');
    // $msg = $msg ? $msg : ($addOrCut ? '获得积分':'消耗积分');
    $r = $this->getOpScore($type,$extra);
    if(!$r['status']) return $r;
    $score = (int) $r['info'];
    if($score > 0){
      if($addOrCut){
        $r = $this->addScore($uid,$score,$type,$msg);
      }else{
        $r = $this->cutScore($uid,$score,$type,$msg);
      }
      // if(!$r['status']) return $r;
      return $r;
    }else{
      return returnSuc('score change:'.$score);
    }
  }
  /**
   * 增加用户积分 + 记录历史
   * score需在前面计算好
   */
  public function addScore($uid,$score=0,$type=0,$reason=''){
    $score = intval($score);
    if($score<1) return returnErr(Linvalid('score'));

    Db::startTrans();
    $r = $this->getUserScore($uid);
    if(!$r['status']) return $r;
    $before = $r['info'];

    $r = (new MemberConfigLogicV2)->setInc(['uid'=>$uid],'score',$score);
    if(!$r) return returnErr('error',true);
    //添加记录
    $add = [
      'uid'    =>$uid,
      'before_score' =>$before,
      'plus'   =>$score,
      'minus'  =>0,
      'after_score'  =>$before+$score,
      'type'   =>$type,
      'reason' =>$reason,
    ];
    $r = (new ScoreHisLogicV2)->add($add);
    if(!$r) return returnErr('error',true);

    Db::commit();
    return returnSuc('pass');
  }
  /**
   * 减少用户积分 + 记录历史 + min:0
   * score需在前面计算好
   */
  public function cutScore($uid,$score=0,$type=0,$reason=''){
    $score = intval($score);
    if($score<1) return returnErr(Linvalid('score'));
    $r = $this->getUserScore($uid);
    if(!$r['status']) return $r;
    $before = $r['info'];

    if($before < $score) return returnErr('用户积分不足');

    Db::startTrans();
    $r = (new MemberConfigLogicV2)->setDec(['uid'=>$uid],'score',$score);
    if(!$r) return returnErr('error',true);
    //添加记录
    $add = [
      'uid'    =>$uid,
      'before_score' =>$before,
      'plus'   =>0,
      'minus'  =>$score,
      'after_score'  =>$before - $score,
      'type'   =>$type,
      'reason' =>$reason,
    ];
    $r = (new ScoreHisLogicV2)->add($add);
    if(!$r) return returnErr('error',true);

    Db::commit();
    return returnSuc('pass');
  }
}