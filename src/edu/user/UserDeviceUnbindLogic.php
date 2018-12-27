<?php
/*rainbow 2017-08-18 14:44:19*/
namespace src\ewt\logic;

use src\base\logic\BaseLogic;
use src\ewt\model\UserDeviceUnbind;
use src\base\helper\ConfigHelper;
use src\user\logic\MemberConfigLogic;

class UserDeviceUnbindLogic extends BaseLogic{

  //多长时间后可以再次解绑
  public function lastUnbindTime($uid){
    $lastCount = $this->countLastUnbind($uid);
    if($lastCount){ // 次数有剩下
      return 0;
    }else{
      $now = time();
      // 周期内解绑次数用完
      $limit_cycle = (int) ConfigHelper::getValue('app_device_cycle');
      $limit = (int) ConfigHelper::getValue('app_device_cycle_limit');
      // $r = (new MemberConfigLogic)->getInfo(['uid'=>$uid]);
      // if(!$r['status']) return -1;
      // $limit = (int) $r['info']['login_device_cnt'];

      if($limit <1) return -1; // $limit=0忽略limit查询(查询bug)
      // 最近的第$limit次解绑 的时间
      $r = $this->query(['uid'=>$uid],['curpage'=>1,'size'=>$limit],'create_time desc',[],'create_time');
      if($r['count']<1) return -1;
      $last_time = intval(end($r['list'])['create_time']);

      return 86400*$limit_cycle - $now + $last_time;
    }
  }

  // 还可解绑几次
  public function countLastUnbind($uid){
    $now = time();
    $limit_cycle = (int) ConfigHelper::getValue('app_device_cycle');
    $limit = (int) ConfigHelper::getValue('app_device_cycle_limit');
    // $r = (new MemberConfigLogic)->getInfo(['uid'=>$uid]);
    // if(!$r['status']) return 0;
    // $limit = (int) $r['info']['login_device_cnt'];
    // 周期内绑定过几次
    $left = $now - 86400*$limit_cycle;
    $r = $this->count(['uid'=>$uid,'create_time'=>['gt',$left]]);

    return max($limit - intval($r),0);
  }

  // 是否可以解绑
  public function canUnBind($uid){
    $now = time();
    $limit_cycle = (int) ConfigHelper::getValue('app_device_cycle');
    $limit = (int) ConfigHelper::getValue('app_device_cycle_limit');
    // $r = (new MemberConfigLogic)->getInfo(['uid'=>$uid]);
    // if(!$r['status']) return $r;
    // $limit = (int) $r['info']['login_device_cnt'];
    // 周期内绑定过几次
    $left = $now - 86400*$limit_cycle;
    $r = $this->count(['uid'=>$uid,'create_time'=>['gt',$left]]);

    if(intval($r) >= $limit){
      return returnErr('您'.$limit_cycle.'天最多只能解绑'.$limit.'次');
    }
    return returnSuc('success');
  }
}