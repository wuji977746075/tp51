<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-26 18:03:05
 * Description : [Description]
 */

namespace src\bbs\ban;
use src\base\BaseLogic;

/**
 * 论坛板块业务逻辑
 */

class BbsBanLogic extends BaseLogic {
  const BAN_REPLY = 1; //倒数二进制位1:禁止发改评论
  const BAN_POST  = 2; //倒数二进制位2:禁止发改帖
  const RULE_NUM  = 2; //规则数

  // 根据int 获取规则描述
  // 现在规则数 : 2
  public function getRuleDesc($rule=0){
    $rule = str_pad(decbin($rule),self::RULE_NUM,'0',STR_PAD_LEFT);
    $len  = strlen($rule);
    $str  = '';

    $str .= intval($rule[$len-1]) ? '禁止回复' : '';
    $str .= intval($rule[$len-2]) ? ($str ? ';':'').'禁止帖子' : '';
    return $str;
  }

  //禁言的用户列表
  public function querySlient(array $map,array $page=['page'=>1,'size'=>10],$order=false,array $params=[],$fields=false){
    $count = $this->getModel()->group('uid')-> where($map) -> count();
    $list = [];
    if($count){
      $query = $this->getModel()->group('uid');
      if($map) $query = $query->where($map);
      if(false !== $order)  $query = $query->order($order);
      if(false !== $fields) $query = $query->field($fields);

      $start = max(intval($page['page'])-1,0)*intval($page['size']);
      $list = $query -> limit($start,$page['size']) -> select();//->buildSql();//
      $list = $list ? obj2Arr($list) : [];
    }
    // 查询满足要求的总记录数
    // $Page = new Page($count, $page['size']);
    // //分页跳转的时候保证查询条件
    // if (false !== $params) {
    //   foreach ($params as $key => $val) {
    //     $Page -> parameter[$key] = urlencode($val);
    //   }
    // }
    // // 实例化分页类 传入总记录数和每页显示的记录数
    // $show = $Page -> show();

    // return ["count"=>$count,"show" => $show, "list" => $list];
    return ["count"=>$count, "list" => $list];
  }

  //是否 禁止了某样或某些权限
  //$index 二进制倒数第几位 : 1:回复,2:帖子
  //return false未禁止 0永久禁止 int时段禁止
  public function isBan($uid,$right=0){
    //查询最近的一条
    $r = $this->getInfo(['uid'=>$uid],'create_time desc');
    if(!$r) return false;
    $rule  = str_pad(decbin($r['rule']),$right,'0',STR_PAD_LEFT);
    $len   = strlen($rule);
    $start = (int) $r['start_time'];
    $end   = (int) $r['end_time'];
    $now = time();
    if($start > $now) return false;
    if($end && $end < $now) return false;
    return intval($rule[$len - $right]) ? $end : false;
  }
}