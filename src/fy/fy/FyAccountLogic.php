<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-08-20 16:52:27
 * Description : [分佣系统 - 淘宝客账号logic]
 */

namespace src\fy\fy;
use src\base\BaseLogic;

class FyAccountLogic extends BaseLogic
{
  // 推广位信息 + 分配人信息
  function queryCountWithUser($map = null, $page = false, $order = false, $params = false, $fields = false){
    $pre = PRE;
    empty($page) && $page = ['page'=>1,'size'=>10];
    $model = $this->getModel();
    $count = $model ->alias('fa')
    ->join([$pre.'fy_account_user'=>'fau'],'fau.pid=fa.id','left')
    ->join([$pre.'user'=>'u'],'fau.uid=u.id','left')
    ->where($map)->count();
    $list  = [];
    if($count){
        $query = $model->alias('fa')
        ->join([$pre.'fy_account_user'=>'fau'],'fau.pid=fa.id','left')
        ->join([$pre.'user'=>'u'],'fau.uid=u.id','left');
        if(!empty($map)) $query = $query->where($map);
        if(false !== $order) $query = $query->order($order);
        if(false !== $fields) $query = $query->field($fields);

        $start = isset($page['start']) ? $page['start'] : max(0,(intval($page['page'])-1)*intval($page['size']));
        $list = $query -> limit($start,$page['size']) -> select();
        $list = $list->toArray();
    }
    return ["count" => $count, "list" => $list];
  }

  // 根据pid获取最下级的uid
  // 注意 : max的查询要先判断再查
  function getInviteUidByPid($pid) {
    $ret = 0;
    $id = $this->getField(['pid'=>$pid],'id');
    if($id){
      $l  = new FyAccountUserLogic;
      // 查询最下级uid
      if($l->getField(['pid'=>$id],'id')){
        $level = $l->getModel()->where(['pid'=>$id])->max('level');
        $uid = $l->getField(['pid'=>$id,'level'=>$level],'uid');
        $ret = $uid ? $uid : 0;
      }
    }
    return $ret;
  }

  // 根据佣金规则(db_config)计算佣金
  // todo :
  function getInviteMoney($login_time,$real_time,$buy_time){
    // 佣金 : 登陆 实名 首购
    // todo : 放到数据库里
    $config = ['login'=>2000,'real'=>1000,'buy'=>3000];
    $m = 0;
    if($login_time>0 && $login_time<time()) $m = $m+$config['login'];
    if($real_time>0 && $real_time<time()) $m = $m+$config['real'];
    if($buy_time>0 && $buy_time<time()) $m = $m+$config['buy'];
    return $m;
  }
}