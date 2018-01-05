<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-21 11:06:00
 */

namespace app\src\repair\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\base\utils\CodeGenerateUtils;
use app\src\repair\model\RepairOrder;
use app\src\repair\model\Repair;
use app\src\repair\logic\RepairLogicV2;
use think\Db;
use app\src\extend\Page;
use app\src\wallet\logic\ScoreHisLogicV2;
use app\src\wallet\logic\WalletLogic;

class RepairOrderLogicV2 extends BaseLogicV2
{
    const TOBE_PAY = 0; //待支付 : 生成订单 , 超时真删除
    const PAYED    = 1; //支付完成
    const REFUNDED = 2; //已退款

    //自动取消
    public function autoCancel($time = 0)
    {
        $map = [
            'update_time' => ['<', $time],
            'pay_status' => self::TOBE_PAY,
        ];
        $r = $this->queryNoPaging($map);
        foreach ($r as $v) {
            $params = [
                'uid' => $v['uid'],
                'id' => $v['repair_id'],
            ];
            $r = $this->cancel($params);
            // echo $r['info'].'<br/>';
        }
        // shalt('test');
    }

    //自动关闭
    public function autoClose($time = 0,$echo=true)
    {
      $l = new RepairLogicV2;
      $map = [
        'update_time'   => ['<', $time],
        'repair_status' => RepairLogicV2::TOBE_PAY,
      ];
      $r = $l->queryNoPaging($map);
      foreach ($r as $v) {
        $r2 = $l->cancel($v['id'],1);
        $msg = '关闭维修超时未支付订单'.$v['id'].':'.$r2['info'].'<br/>';
        if($echo) echo $msg;
        addTestLog('autoClose',$msg,date('Y-m-d H:i'));
      }
    }

    /**
     * 业务 - 取消支付 - 司机
     * 退回已支付积分 2017-07-25 14:15:56
     * @Author
     * @DateTime 2016-12-21T16:05:46+0800
     * @param    [type]                   $params [id,uid]
     * @return   [apiReturn]                      [description]
     */
    public function cancel($params){
      extract($params);
      $logic = new RepairLogicV2();
      //订单查询条件
      $map = ['repair_id'=>$id,'pay_status'=>self::TOBE_PAY];

      //? 用户维修支付中
      $where = ['uid'=>$uid,'id'=>$id,'repair_status'=>RepairLogicV2::PAYING];
      $r = $logic->getInfo($where);
      if(!$r) return returnErr(L('fail').' repair err : id or uid or repair_status');
      //订单查询条件变更
      $map['uid'] = $uid;

      //? 用户待支付维修订单
      $r = $this->getInfo($map);
      Db::startTrans();
      if($r){
        $oid        = $r['id'];
        $order_code = $r['order_code'];
        $repair_id  = $r['repair_id'];
        // ? 使用了积分的话 退回积分 并修改订单信息
        $score = (int) $r['score'];
        if($score){
          $r = (new ScoreHisLogicV2)->addScore($uid,$score,ScoreHisLogicV2::PAY_CUT,'维修取消支付退回');
          if(!$r['status']) return returnErr($r['info'],true);
          //修改订单信息
          $this->save(['id'=>$oid],['score'=>0,'score_pay'=>0]);
        }
        //记录订单变更
        $map = [
          'reason'       =>'取消支付',
          'create_time'  =>time(),
          'isauto'       =>0,
          'order_code'   =>$order_code,
          'order_status' =>RepairLogicV2::CANCEL,
          'operator'     =>$uid,
          'repair_id'    =>$repair_id,
        ];
        (new RepairOrderHisLogicV2())->add($map);
      }else{
        return returnErr(L('fail').' order err : null',true);
      }
      //改变维修状态 - 待支付/再报价
      $logic->save(['id'=>$id],['repair_status'=>RepairLogicV2::TOBE_PAY]);
      Db::commit();
      return returnSuc(L('success'));
    }

    /**
     * rewrite
     * 业务 - 无订单则生成订单 - 每次重新生成支付码
     * 使用最新报价
     * 未使用积分时可使用积分    2017-09-01 11:11:02
     * @Author
     * @DateTime 2016-12-17T11:49:14+0800
     * @param    array |$params  [uid,id.score,bill_type,bill_title,bill_code]
     * @param    string|$pk      [description]
     * @return   [apiReturn]     [description]
     */
    public function pay($params,$pk='id'){
      extract($params);
      $score = intval($score);
      if($score<0) return returnErr(Linvalid('score'));

      $bill_type = intval($bill_type);
      if(in_array($bill_type, [1,2]) && empty($bill_title)){
        $this->apiReturnErr(Llack('bill_title'));
      }
      if($bill_type==2 && empty($bill_code)){
        $this->apiReturnErr(Llack('bill_code'));
      }

      $now   = time();
      $logic = new RepairLogicV2();
      $utils = new CodeGenerateUtils();
      //? 用户维修单
      $where = ['uid'=>$uid,'id'=>$id];
      $r = $logic->getInfo($where);
      if(!$r) return returnErr(Linvalid('id or uid'));
      $repair_status = (int) $r['repair_status'];
      //? 维修支付中/待支付
      if(!in_array($repair_status,[RepairLogicV2::TOBE_PAY,RepairLogicV2::PAYING])){
        return returnErr(L('fail').' repair_err : order_status');
      }
      //重新计算订单价格 - 防止取消后重报价了
      $price = intval($r['price']);
      //? 订单
      $order_info = $this->getInfo(['uid'=>$uid,'repair_id'=>$id],false,'id,order_code,pay_status,score,score_pay');
      // 已使用积分的话 不得再次使用
      if($order_info){
        $old_scorePay = intval($order_info['score_pay']);
        $old_score    = intval($order_info['score']);
        if($old_score && $score){
          return returnErr('不得重复使用积分');
        }
      }else{
        $old_score    = 0;
        $old_scorePay = 0;
      }

      // 计算新使用的可抵扣金额分
      if($score){
        $r = (new ScoreHisLogicV2)->getOpScore(ScoreHisLogicV2::PAY_CUT);
        if(!$r['status']) return returnErr($r['info']);
        $rate     = floatval($r['info']);
        $scorePay = intval($rate*$score*100);
      }else{
        $scorePay = 0;
      }
      if($scorePay && $old_scorePay) return returnErr('不得重复抵扣订单');

      $needPay = $price - $scorePay - $old_scorePay;
      if($needPay<0) return returnErr('使用积分过多');
      Db::startTrans();
      if(!$order_info){ // 新订单
        // 生成订单
        $order_code = $utils->getRepairOrderCode($uid);
        $entity = [
          'uid'         => $uid,
          'repair_id'   => $id,
          'order_code'  => $order_code,
          'pay_status'  => self::TOBE_PAY,
          'bill_type'   => $bill_type,
          'bill_title'  => $bill_title,
          'bill_code'   => $bill_code,
          'address_id'  => $address_id,
        ];
        $oid = parent::add($entity);
      }else{ // 已有订单
        //? 订单非未支付
        if(intval($order_info['pay_status']) !== self::TOBE_PAY){
          Db::rollback();
          return returnErr(L('fail').' order err : pay_status');
        }
        //? 订单未支付就使用了积分
        // if(intval($order_info['score'])>0){
        //   Db::rollback();
        //   return returnErr('不得重复使用积分,请先取消支付');
        // }
        $order_code = $order_info['order_code'];
        $oid        = $order_info['id'];
      }
      //订单 写入pay_code + money + 积分支付信息(score + score_pay) + 发票信息
      $pay_code = $utils->getRepairPayCode($uid);
      $this->save(['id'=>$oid],['pay_code'=>$pay_code,'money'=>$price,'score'=>$score+$old_score,
          'score_pay'=>$scorePay+$old_scorePay,'bill_type'=>$bill_type,'bill_title'=>$bill_title,'bill_code'=>$bill_code]);
      //订单历史 写入
      $entity = [
        'reason'       =>'尝试支付',
        'create_time'  =>$now,
        'isauto'       =>0,
        'order_code'   =>$order_code,
        'order_status' =>self::TOBE_PAY,
        'operator'     =>$uid,
        'repair_id'    =>$id,
      ];
      $id = (new RepairOrderHisLogicV2())->add($entity);
      //改变维修状态 - 支付中
      $logic->save($where,['repair_status'=>RepairLogicV2::PAYING]);
      //组装支付信息
      if($needPay>0){
        // 用户使用了积分
        if($score){
          $r = (new ScoreHisLogicV2)->cutScore($uid,$score,ScoreHisLogicV2::PAY_CUT,'维修订单抵扣');
          if(!$r['status']) return returnErr($r['info']);
        }
      }elseif($needPay == 0){ // 跳转积分支付 允许0积分
        // 用户使用了积分
        if($score){
          $r = (new WalletLogic)->scorePay(['pay_code'=>$pay_code,'uid'=>$uid,'score'=>$score]);
          if(!$r['status']) return returnErr($r['info'],true);
        }else{
          return returnErr('支付信息错误',true);
        }
      }else{
        return returnErr('支付信息错误',true);
      }
      Db::commit();
      return returnSuc($utils->getPayInfo($uid,$pay_code,$needPay,$now));

    }

    public function queryPages($map = null, $page = ['curpage'=>1,'size'=>10], $order = false, $params = false, $fields = 'r.*,m.nickname,o.order_code,o.pay_code,o.pay_type,o.pay_status,o.create_time as order_create_time,o.pay_money,o.pay_balance,o.score,o.score_pay,o.bill_type,o.bill_title,o.bill_code') {
      $model = new Repair();
      $query = $model->alias('r');
      if(!is_null($map))    $query = $query->where($map);
      if(false !== $order)  $query = $query->order($order);
      if(false !== $fields) $query = $query->field($fields);
      $query = $query->join(['common_member m',''],'m.uid=r.uid','left')->join(['itboye_repair_order o',''],'r.id=o.repair_id','left');
      $start = max(intval($page['curpage'])-1,0)*intval($page['size']);
      $list = $query -> limit($start,$page['size']) -> select();
      $count = $model ->alias('r')->join(['common_member m',''],'m.uid=r.uid','left')->join(['itboye_repair_order o',''],'r.id=o.repair_id','left')-> where($map) -> count();

      // $query = $this->getModel()->alias('o');
      // if(!is_null($map)) $query = $query->where($map);
      // if(false !== $order) $query = $query->order($order);
      // if(false !== $fields) $query = $query->field($fields);
      // $query = $query->join(['common_member m',''],'m.uid=o.uid','left')->join(['itboye_repair r',''],'r.id=o.repair_id','left');
      // $start = max(intval($page['curpage'])-1,0)*intval($page['size']);
      // $list = $query -> limit($start,$page['size']) -> select();

      // $count = $this ->getModel() ->alias('o')->join(['common_member m',''],'m.uid=o.uid','left')->join(['itboye_repair r',''],'r.id=o.repair_id','left')-> where($map) -> count();

      // 查询满足要求的总记录数
      $Page = new Page($count, $page['size']);

      //分页跳转的时候保证查询条件
      if ($params !== false) {
          foreach ($params as $key => $val) {
              $Page -> parameter[$key] = urlencode($val);
          }
      }

      // 实例化分页类 传入总记录数和每页显示的记录数
      $show = $Page -> show();
      $data = [];
      foreach ($list as $vo){
          if(method_exists($vo,"toArray")){
              array_push($data,$vo->toArray());
          }
      }

      return (["count"=>$count, "list" => $data ,"show" => $show]);
    }
    /**
     * @return mixed
     */
    protected function _init()
    {
        $this->setModel(new RepairOrder());
    }
}