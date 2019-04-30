<?php
namespace src\lottery\lottery;

use src\base\BaseLogic;

use Common\Api\Api;
use Admin\Model\GameModel;
use Common\Api\GamePrizegethisApi;
use Common\Api\GamePrizeApi as PrizeLogic;
use Admin\Api\MemberApi;
use Webview\Api\SigninApi;
use Shop\Api\RedEnvelopeApi;
use Common\Api\GameLogApi;
// use Shop\Api\ShoppingCartApi;
// use Admin\Model\GamePrizeModel;
// use Admin\Model\GamePrizegethisModel;

class LotteryLogic extends BaseLogic{
  const MAX_FREE     = 2; //每天签到送免费次数，不累计
  const MAX_CHANGES  = 5; //每天可抽次数上限
  const PRE_TIME     = 5; //每次抽奖间歇,秒
  const SCOREPREROLL = 10;//每次积分抽奖消耗
    /**
     * 一次抽奖
     * @param  int      the user id
     * @param  int      the game id
     * @return array    results
     */
    public function roll($uid,$gid){
        $maxFree      = self::MAX_FREE;
        $maxChange    = self::MAX_CHANGES;
        $free         = fasle;
        $scorePreRoll = self::SCOREPREROLL;
        $r = $this->preRoll($uid,$gid);
        // dump($r);exit;
        if(!$r['status']){
            $r['info'] = '';
            $r['msg']  = $r['msg'];
        }else{
            // S('roll'.$uid,'in...',self::PRE_TIME);
            $freeC    = intval($r['info']['remainF']);
            $score    = intval($r['info']['score']);
            $allC     = intval($r['info']['remainA']);
            $signin   = $r['info']['signin'];
            $gameName = $r['info']['game_name'];
            $status   = true;
            // $msg      = 'default ..';
            $s        = $this->doRoll($gid);
            //开启事务
            $this -> model -> startTrans();
            if(!$s['status']){
                $status   = false;
                $r['info']= '';
                $r['msg'] = $s['msg'];
            }else{
                $r['info']= array();
                //中奖奖项
                $pid = intval($s['info']);
                if($pid){
                    //奖品检查
                    $result = apiCall(GamePrizeApi::FIND,array(array('id'=>$pid)));
                    if(!$result['status']){
                        // S('roll'.$uid,null);
                        return array('status'=>false,'info'=>'','msg'=>'系统错误：get pri err！');
                    }
                    $num   = intval($result['info']['prize_cnt']);//库存
                    $pname = $result['info']['prize_name'];//名字
                    $pnum  = $result['info']['prize_num'];//面额
                    $type  = intval($result['info']['prize_type']);
                    $result = apiCall(GamePrizegethisApi::COUNT,array(array('prize_id'=>$pid)));
                    if(!$result['status']){
                    // S('roll'.$uid,null);
                    return array('status'=>false,'info'=>'','info'=>'系统错误：get his err！');
                    }
                    if($num>0 && $num < intval($result['info'])){
                    // S('roll'.$uid,null);
                    return array('status'=>false,'info'=>'','msg'=>'对不起，'.$pname.'已抽完，本次抽奖无消耗！');
                    }
                }else{
                    $pname = "未中奖";
                    $pnum  = '';
                    $type  = 0;
                }
                $r['info']['prize'] = $pid;//$s['info'];
                $r['msg']  = $s['msg'];
                // $type   = intval($s['info']['prize_type']);
                // $name   = $s['info']['prize_name'];
                // $pid    = intval($s['info']['prize_id']);
                // $num    = intval($s['info']['prize_num']);
                $msgbox = array();
                if($allC>0) $allC  -=1;
                else array('status'=>false,'info'=>'','msg'=>'数据错误：ALLC！请重试');
                if($freeC>0)
                {
                    $froll =  1;//"免费";
                    $freeC -= 1;
                }else{
                    $froll = 0;//"10积分";
                    //10积分抽奖
                    $result = apiCall(MemberApi::GET_INFO,array(array('uid'=>$uid)));
                    if(intval($result['info']['score']) < self::SCOREPREROLL){
                        // S('roll'.$uid,null);
                        return array('status'=>false,'info'=>'','msg'=>'对不起，积分不足！');
                    }
                    $result = apiCall(MemberApi::SET_DEC,array(array('uid'=>$uid),'score',$scorePreRoll));
                    // dump($result);exit;
                    if(!$result['status']){
                        $this -> model ->rollback();
                        return array('status'=>false,'info'=>'','msg'=>'系统错误：score dec！');
                    }
                    $score  -= $scorePreRoll;
                }
                //记录本次抽奖
                $his = '在['.$gameName.']中'.($froll?'免费':'10积分').($type?'抽到':'').'['.$pname.']';
                if($type === 1 || $type === 2) $his.='面额['.$pnum.']';
                if($type === 3)  $his.='编号['.$pnum.']';
                $map = array(
                    'prize_id'                =>$pid
                    ,'game_id'                =>$gid
                    ,'uid'                    =>$uid
                    ,'free'                   =>$froll
                    ,'get_time'               =>NOW_TIME
                    ,'prize_type'             =>$type
                    ,'itboye_getprize_hiscol' =>$his
                );
                $result = apiCall(GamePrizegethisApi::ADD,array($map));
                if(!$result['status'])
                {
                    $status = false;
                    $msg    ='Prize+Record:ERROR';
                }
                //奖品发放
                //1积分：  score[int|the-score-number]
                //2购物券：redev[int|the-redev-number]
                //3商品：  goods[int|the-goods-id]
                //4其他：  other[int|you like]
                $num = $pnum;
                if(1 === $type)
                {
                    //add $num score to user[$uid]
                    $result = apiCall(MemberApi::SET_INC,array(array('uid'=>$uid),'score',$num));
                    // dump($result);exit;
                    if(!$result['status']){
                        $status = false;
                        $msg = '积分发放失败';
                    }
                    $score += $num;
                    $msgbox['title']   = '恭喜您抽中了'.$num.'积分';
                    $msgbox['content'] = '恭喜您在抽奖 - '.$gameName.' 中抽中了'.$name.',您获得了'.$num.'积分,请注意查看！';
                }elseif(3 === $type){
                    //TODO //添加购物车
                    $msgbox['title']   = '恭喜您抽中了编号为'.$num.'的商品';
                    $msgbox['content'] = '恭喜您在抽奖 - '.$gameName.' 中抽中了编号为'.$name.',您将获得编号为'.$num.'的商品,活动结束后工作人员会主动联系您，请保持通信畅通！';
                    //add goods $num to user[$uid]'s shopping cat
                    // $e          = array();
                    // $e['uid']   = $uid;
                    // $e['p_id']  = $num;
                    // $e['count'] = 1;
                    // $e['price'] = 0.1;
                    // $result     = apiCall(ShoppingCartApi::ADD,array($e));
                    // if(!$result['status']){
                    //     $status = false;
                    //     $msg = '商品发放失败';
                    // }
                }elseif(4 === $type){
                    $msgbox['title']   = '恭喜您抽中了编号为'.$num.'的实物';
                    $msgbox['content'] = '您的运气太好了,恭喜您在抽奖 - '.$gameName.'中抽中了实物大奖：'.$name.',活动结束后工作人员会主动联系您，请保持通信畅通！';
                }elseif(2 === $type){
                    $msgbox['title']   = '恭喜您抽中了'.$num.'元购物券';
                    $msgbox['content'] = '恭喜您在抽奖 - '.$gameName.' 中抽中了'.$name.',您获得了'.$num.'元购物券,请注意查看！';
                    //add $num red-envelope to user[$uid]
                    $e                = array();
                    $e['uid']         = $uid;
                    $e['get_time']    = NOW_TIME;
                    $e['money']       = $num;
                    $e['use_status']  = 0;
                    $e['tpl_id']      = 9; // 满100使用
                    if(intval($num)<=10){
                        $e['expire_time'] = 86400*7; //<=10 7天 //购物券【奖品发放】
                    }else{
                        $e['expire_time'] = 86400*3; //>10 3天 //购物券【奖品发放】
                    }
                    $e['notes']       = '中奖购物券'.$num.'元';
                    $result           = apiCall(RedEnvelopeApi::ADD,array($e));
                    if(!$result['status']){
                        $status = false;
                        $msg    = '购物券发放失败';
                    }
                }
                //发送中奖消息  6055=> gameMsg
                $map = array(
                    'uid'      =>$uid
                    ,'summary' =>$msgbox['title']
                    ,'content' =>$msgbox['content']
                );
                unset($msgbox);
                if($type){
                    $result = apiCall(GameLogApi::ADD,array($map));
                    // dump($result);exit;
                    if(!$result['status']){
                        $status = false;
                        $msg    ='MESSAGE+SEND:ERROR';
                    }
                }
                $r['info']['score']   = $score ;
                $r['info']['signin']  = $signin ;
                $r['info']['remainF'] = $freeC ;
                $r['info']['remainA'] = $allC ;
            }
            // dump($r);exit;
            if($status){
                // $r['msg']  = 'commit ';
                $this->model->commit();
            }else{
                $r['status'] = false;
                $r['msg']    = $msg;
                // $r['msg']    = 'rollback ';
                $this->model->rollback();
            }
        }
        // S('roll'.$uid,null);
        return $r;
    }
    // public function test($uid,$pid){

    // }
    /**
     * 抽奖前检查
     * @param  [type] $uid [description]
     * @param  [type] $gid [description]
     * @return [arr]      [description]
     */
    public function preRoll($uid,$gid){
//次数验证
        $flag = true;
        $preTime   = self::PRE_TIME;
        $maxFree   = self::MAX_FREE;
        $maxChange = self::MAX_CHANGES;
        $r         = ['signin'=>false,'remainF'=>0,'remainA'=>0];

        $r = (new MemberApi)->getInfo(['uid'=>$uid]);
        !$r && $this->err('非法用户');
//查询抽奖信息
        $r = $this->getInfo(['id' => $gid]);
        !$r && $this->err('无此抽奖');
        if( (NOW_TIME < $r['start_time']) || (NOW_TIME > $r['end_time']) ){
            $this->err('抽奖已失效');
        }

//查询今天已抽奖次数
        $time0    = strtotime(Date('Y-m-d',NOW_TIME));
        $time1    = $time0 + 3600*24;
        $bettoday = ['between',[$time0,$time1]];
        $map      = [
            'uid'      => $uid,
            'get_time' => $bettoday
        ];
        $result = apiCall(GamePrizegethisApi::COUNT,array($map));
        $r['info']['remainA'] = $maxChange - intval($result['info']);
        if($flag && $r['info']['remainA']<0){
            $r['msg'] = '数据错误：ALLC,请明天再试';
            return $r;
        }
//今天是否签到
        $map    = array(
            'uid'           => $uid
            ,'sign_in_time' => $bettoday
        );
        $result = apiCall(SigninApi::GET_INFO,array($map));
        if($result['status']){
            $info = $result['info'];
//是否签到
            if($info){
                $r['info']['signin']  = true;
//查询今天剩余免费次数
                $map = array(
                    'uid'        => $uid
                    ,'get_time'  => $bettoday
                    ,'free'      => 1
                );
                $result = apiCall(GamePrizegethisApi::COUNT,array($map));
                if($result['status']){
                    $m = $maxFree - intval($result['info']);
                    if($flag &&  $m<0){
                        $r['msg'] = '数据错误：FREE,请明天再试';
                        return $r;
                    }
                    $r['info']['remainF'] = $m;
                }else{
                    $r['msg']           = 'GamePrizegethisApi : ERROR';
                    return $r;
                }
            }else{
                $r['info']['signin']  = false;
                $r['info']['remainF'] = 0;
            }
            $r['status']        = true;
//查询用户积分
            $map = ['uid' => $uid];
            $rr = (new MemberApi)->getInfo($map);
            $r['score'] = $r['score'] ? $rr['score'] : 0;
        }else{
            $r['msg']           = 'SigninApi : ERROR';
        }
//抽奖次数判断
        if($maxChange <= intval($result['info'])){
            $r['msg']   = '抽奖次数已达每日上限';
            return $r;
        }
//策略1,查询缓存凭据
        // if(S('roll'.$uid)){
        // $this->err($preTime.'秒内只允许抽奖一次'.S('roll'.$uid));
        // }
        // echo '2323';exit;
//ok.策略2,查询最近一次抽奖时间是否太近
        $r = (new GetHisApi)->find(['uid'=> $uid],'get_time desc');
        // dump($result);exit;
        if($r){
            $last_time = intval($r['get_time']);
            if($last_time > NOW_TIME - $preTime){
                $this->err($preTime.'秒内只允许抽奖一次');
            }
        }else{
            $this->err('查询API: 故障');
        }
//策略3,查询5秒内是否抽过奖
        // $time5 = NOW_TIME - 5;
        // $bet5  = ['gt',$time5];
        // $map   = [
        //   'uid'       => $uid,
        //   'get_time' => $bet5
        // ];
        // $r = (new GetHisLogic)->count($map);
        // // $r['msg'] = M()->getLastSql();
        // // return $r;
        // if($r){
        //   // dump($r);exit;;
        //   intval($r)>0 && $this->err('秒内只允许抽奖一次');
        // }else{
        //   $this->err('查询API: 故障');
        // }
        $r = (new GameLogic)->getInfo(['id'=>$gid]);
        if($r) $r['game_name'] = $r['name'];
        return $r;
    }
    /**
     * 抽奖主体
     * @param  [int]    $gid[the game id]
     * @return [arr]    [本次抽奖结果]
     */
    protected function doRoll($gid){
        $data = [];
        $arr  = [];
        $r = (new PrizeLogic)->getPrize(['game_id'=>$gid],'probability asc');
        !$r && $this->err('GamePrizeApi : ERROR');

        $prizes = $r;
        $n      = 1;
        foreach($prizes as $k=>$v) {
            // $val = intval($v['cnt'])*intval($v['value']);
            if($v['probability']>0){
                $tmp = [$v['id'],intval($v['probability'] * 1000)];
                $tmp[2] = [$n,$n+$tmp[1]-1];
                $arr[] = $tmp;
                $n += $tmp[1];
            }
        }
        $prize_id = $this->getRand($arr); //根据概率获取奖项id
        // foreach($prizes as $k=>$v){ //获取前端奖项位置
        //     if($v['id'] == $prize_id){
        //          $prize_site = $k;
        //          break;
        //     }
        // }
        // $data['prize_name'] = $prizes[$prize_site]['prize_name'];
        // $data['prize_num']  = $prizes[$prize_site]['prize_num'];
        // $data['prize_type'] = $prizes[$prize_site]['prize_type'];
        // $data['prize_site'] = $prize_site;
        // $data['prize_id'] = $prize_id;
        return $prize_id;
    }
    /**
     * 获取roll点结果
     * @param  [type] $proArr [description]
     * @return [int]          [0|未中奖,]
     */
    protected function getRand($proArr) {
        // dump($proArr);exit;
        $randNum = mt_rand(1, 1000); //roll点
        foreach ($proArr as $v) {
            $zones = $v[2];
            if($randNum>=$zones[0] && $randNum<=$zones[1]){
                return $v[0];
            }
        }
        return 0;
    }

    /**
     * 奖品类型
     * 1积分   => 直接发放
     * 2购物券 => 直接发
     * 3商品   => 0.1元加入用户购物车
     * 4其他   => 工作人员联系
     * @return [type] [description]
     */
    public function getPrizeCats(){
        return [1=>'积分',2=>'购物券',3=>'商品',4=>'其他'];
        // $cat = D('Datatree')->where(array('code'=>'PrizeCat'))->find();
        // return D('Datatree')->where(array('parentid'=>$cat['id']))->select();
    }
}