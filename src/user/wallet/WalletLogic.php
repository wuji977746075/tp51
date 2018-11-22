<?php
namespace src\user\wallet;

use src\base\BaseLogic;
use src\user\user\UserLogic;
use src\fy\fy\FyAccountUserLogic;

class WalletLogic extends BaseLogic{
  const PLUS_FY  = 17;
  const MINUS_FY = 18;

  const OK    = 0; // 账户正常
  const FROZE = 1; // 账户冻结

  /**
   * + money 外层请加事务
   * @param  $uid   [description]
   * @param  $money [description]
   * @param  $type  [description]
   * @param  string $extra [description]
   * @return Exception|id(his key)
   */
  function plus($uid,$money,$type,$reason='',$extra='') {
    if(is_numeric($money) && $money>0){
      // $this->trans();
      $m = $this->getBalance($uid);
      //+
      !$this->setInc(['uid'=>$uid],'account_balance',$money) && throws('添加余额失败');
      //+his
      $his = new WalletHisLogic;
      $add = [
        'uid'          => $uid,
        'before_money' => $m,
        'plus'         => $money,
        'minus'        => 0,
        'after_money'  => $m + $money,
        'dt_type'      => $type,
        'reason'       => $reason,
        'extra'        => $extra,
        'wallet_type'  => self::OK,
      ];
      $id = $his->add($add);
      // $this->commit(); 外层需要 suc或commit;
      return $id;
    }else{
      throws('操作金额非法:'.$money);
    }
  }
  // - money
  function minus($uid,$money,$type,$reason='',$extra='') {
    //-
    // + his
  }

  // 获取账户信息 无则添加
  function getInfoIfAdd($uid){
    $this->trans();
    $info = $this->getInfo(['uid'=>$uid],'id asc','uid,wallet_type,frozen_funds,account_balance');
    if(!$info){
      $add = [
        'uid'             => $uid,
        'wallet_type'     => self::OK,
        'frozen_funds'    => 0,
        'account_balance' => 0,
      ];
      if(!$this->add($add)) throws('添加余额账户失败:'.$uid);
      $info = $this->getInfo(['uid'=>$uid],'id asc','uid,wallet_type,frozen_funds,account_balance');
      !$info && throws('查询余额账户失败:'.$uid);
    }
    $this->commit();
    return $info;
  }

  function getBalance($uid){
    $info = $this->getInfoIfAdd($uid);
    return $info['account_balance'];
  }

  private function getParents($uid) {
    $ul = new UserLogic;
    // 查上级=>1级
    $info = $ul->getInfo(['id'=>$uid],false,'id as uid,level,invite_uid as parent,rate');
    $info = $info ? $info->toArray() : [];
    $ret  = $this->getParent($info ? [$info['level']=>$info] : [],$ul);
    asort($ret);
    $data = [];
    foreach ($ret as &$v) {
      $level_next     = intval($v['level']) + 1;
      $v['rate_next'] = isset($ret[$level_next]) ? $ret[$level_next]['rate'] : 0; //向下返佣的比例
    } unset($v);
    return $ret;
  }

  // 递归查询 上级=>1级
  private function getParent($ret=[],$ul){
    // $ul = new UserLogic;
    $para = $ret ? end($ret) : [];
    if($para){
      $parent = (int) $para['parent'];
      // 查上级分销商
      $info = $ul->getInfo([['id','=',$parent],['level','gt',0]],false,'id as uid,level,invite_uid as parent,rate');
      if($info){
        $info  = $info->toArray();
        $level = (int) $info['level'];
        if($level){
          if($level !== intval($para['level'])-1) throws('返佣:用户'.$info['id'].'非'.$parent_level.'分销商');
          isset($ret[$level]) && throws('返佣:发现多个'.$level.'级分销商');
          $ret[$level] = $info; // 加$level分销商
          // 非1级继续往上查
          return $this->getParent($ret,$ul);
        }
      }
    }
    return $ret;
  }
  /**
   * 递归返佣 默认最多递归99级
   * @param $pid  fy_account 主键
   * @param $uid
   * @param $money
   * @return  Exception|string
   */
  function fenyong($pindex,$uid,$money) {
    // echo 'uid:'.$uid;
    // echo '-money:'.$money;

    // 淘宝总佣金 $money
    if($money <=0)  throws('分佣:返佣金额错误!');
    $users = $this->getParents($uid);
    $level = 1;$level_end = (int) end($users)['level'];
    // 返佣
    reset($users);
    $temp  = current($users);
    // dump($users);

    $money = floor($money*$temp['rate']); // 系统总佣金
    // dump($users);
    foreach ($users as $v) {
      // $level级分销商 总佣金 $money
      if( $money <=0 ) break;
      // level级 返佣
      $level_temp = intval($v['level']);
      $level_next = $level_temp + 1;
      if($level_temp !== $level) throws('分佣:未发现'.$level.'级分销商');

      $uid_temp  = (int)$v['uid'];
      $balance   = $this->getBalance($uid_temp);
      $rate_temp = floatval($v['rate_next']);
      if($rate_temp<0 || $rate_temp>1) throws('分佣:返佣率错误!');
      if($level_end == $level_temp){ // 到底了
        $temp_money = 0;
        $plus_money = $money - $temp_money;
      }else{ // 往下返佣
        $temp_money = floor($money*$rate_temp); //向下返佣
        $plus_money = $money - $temp_money;
      }
      // echo $uid_temp.'+'.$money.'-'.$temp_money;
      // 自己加钱
      $plus_money>0 && $this->plus($uid_temp,$plus_money,self::PLUS_FY,($money/100).'-'.($temp_money/100),$pindex);

      // 准备向下级分佣
      $level = $level_next;
      $money = $temp_money; // 注意:我已改变
    }
    return $level_end.'级分佣成功!';
  }

}
