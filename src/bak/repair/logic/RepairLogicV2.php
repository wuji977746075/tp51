<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 17:10
 */

namespace app\src\repair\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\city\logic\CityLogicV2;
use app\src\message\enum\MessageType;
use app\src\message\facade\MessageFacade;
use app\src\repair\model\Repair;
use app\src\system\logic\DatatreeLogicV2;
use app\src\user\logic\DriverLogicV2;
use app\src\user\logic\MemberLogic;
use app\src\user\logic\UcenterMemberLogic;
use app\src\user\logic\WorkerLogicV2;
// use app\src\repair\logic\RepairOrderHisLogicV2;
use app\src\wallet\logic\WalletLogic;
use app\src\wallet\logic\ScoreHisLogicV2;
use think\Db;

class RepairLogicV2 extends BaseLogicV2{
    //U司机 W技工 A后台
    const TOBE_RECEIVE = 0;  //待W接单 : U已申请
    const SUCCESS      = 8;  //U维修完/A超时完成
    const CANCEL       = 9; //取消

    const TOBE_PRICE = 1; //待W报价 : W已接单/A已派单
    const TOBE_PAY   = 2; //待W重报价待U支付  : W已报价
    const PAYING     = 3;
    //待U支付完成或U取消订单或A超时取消 : U支付中
    const REPAIRING  = 4; //待W维修完成(W维修中) : U支付完成
    const PAYED      = 4; //待W维修完成(W维修中) : U支付完成
    const REPAIRED   = 5; //待U维修完成或超时A自动完成 : W已维修完成

    protected $driver_group;//司机用户组id
    protected $worker_group;//技工用户组id

    public function getWorkerGroup(){
      return $this->worker_group;
    }

    public function getDriverGroup(){
      return $this->driver_group;
    }

    //给维修单$id附近$km的$size个技工 发新维修单
    public function pushNearBy($id=0,$size=10,$km=50){
      $size = max(intval($size),0);
      if(!$size) $size = 9999;
      //? 待接单的维修
      $info = $this->getInfo(['id'=>$id,'repair_status'=>self::TOBE_RECEIVE]);
      if(!$info) return returnErr(Linvalid('id or repair_status'));
      $uid = (int) $info['uid'];
      //? driver
      // if(!(new WorkerLogicV2())->isWorker($uid)) return returnErr(L('err_account_no_permissions'));
      //get location
      // $r = (new MemberLogic())->getInfo(['uid'=>$uid],false,'lat,lng');
      // if(!$r['status'])     return returnErr($r['info']);
      // if(empty($r['info'])) return returnErr(Linvalid('uid'));
      $lng = (float) $info['lng'];
      $lat = (float) $info['lat'];
      // addTestLog('lng:'.$lng.';lat:'.$lat,'新维修单:'.$id,'新维修单');
      //? 附近$km(km)内的$size个技工
      $order = $this->getPosOrder($lng,$lat);
      $map = [];
      $km = round(intval($km)/111,4); //经纬度差
      $map['m.lng'] = ['between',[$lng-$km,$lng+$km]];
      $map['m.lat'] = ['between',[$lat-$km,$lat+$km]];
      $r = (new WorkerLogicV2())->getWorkers($map,['curpage'=>1,'size'=>$size],$order,false,'m.uid');
      $to_uid = '';
      foreach ($r['list'] as $v) {
        $to_uid.= $to_uid ? ','.$v['uid'] : $v['uid'];
      }
      // addTestLog($to_uid,'新维修单:'.$id,'新维修单');
      $r = $this->pushRepairMsg($to_uid,'新维修单','收到新的维修订单,点击查看','worker',false,['repair'=>$id,'sound'=>'newrepair']);
      // addTestLog($r,'新维修单:'.$id,'新维修单');
      return returnSuc($r['status'] ? L('success') : $r['info']);
    }
    /**
     * 维修支付成功回调 - 订单处理 - 外部请加事务 - push
     * 已知支持 : 微信 余额 支付宝
     * @Author
     * @DateTime 2016-12-29T15:25:01+0800
     * @param    string     $pay_code  [支付编码]
     * @param    int        $pay_money [支付金额,分]
     * @param    int        $pay_type  [支付类型]
     * @param    string     $trade_no  [第三方支付的交易号,自己的不用]
     * @return   apiReturn  [处理结果 一般包含订单 - 写入到第三方支付日志]
     */
    public function paySuccess($pay_code='',$pay_money=0,$pay_type,$trade_no=''){
      $now = time();
      //维修订单状态修改
      $logic = new RepairOrderLogicV2();
      $r = $logic -> getInfo(['pay_code'=>$pay_code],false,'id,repair_id,order_code,uid,pay_status');
      if($r){
        $id         = $r['id'];
        $uid        = $r['uid'];
        $repair_id  = $r['repair_id'];
        $order_code = $r['order_code'];
        //? 处理过
        $pay_status = (int) $r['pay_status'];
        if($pay_status === RepairOrderLogicV2::PAYED){
          return returnErr('重复支付['.$order_code.']');
        }else{
          $logic->save(['id'=>$id],['pay_status'=>RepairOrderLogicV2::PAYED,'pay_money'=>$pay_money,'pay_type'=>$pay_type,'trade_no'=>$trade_no]);
            $repair = new RepairLogicV2();
            //维修状态修改 (支付中=>支付完成)
            // $r = $repair->getInfo(['id' => $repair_id, 'repair_status' => RepairLogicV2::PAYING]);
            // if (!$r) return returnErr(Linvalid('repair : id or repair_status'));
            // $worker = (int)$r['worker_uid'];
            // $r = $repair->save(['id' => $repair_id, 'repair_status' => RepairLogicV2::PAYING], ['repair_status' => RepairLogicV2::PAYED]);
            //维修状态修改 (所有状态=>支付完成)
            $r = $repair->getInfo(['id' => $repair_id]);
            $worker = (int)$r['worker_uid'];
            $r = $repair->save(['id' => $repair_id], ['repair_status' => RepairLogicV2::PAYED]);
          //写入订单历史
          $map   = [
            'reason'       =>'支付订单',
            'create_time'  =>$now,
            'isauto'       =>0,
            'order_code'   =>$order_code,
            'order_status' =>self::REPAIRING,
            'operator'     =>$uid,
            'repair_id'    =>$repair_id,
          ];
          (new RepairOrderHisLogicV2())->add($map);
          //推送
          $r = $this->pushRepairMsg($worker, '维修支付完成', '您接的维修单已支付完成,请着手维修', 'worker');
          return returnSuc(['uid'=>$uid,'msg'=>$order_code]);
        }
      }else{
        return returnErr('未知订单[repair_order null]');
      }
      return returnErr("you should't be here"); //不会到此
    }

    public function pushRepairMsg($to_uid, $title = '', $content = '', $client = '', $debug = false,$after_open_extra=[])
    {
        if ($debug) {
            $to_uid = 122;
            $client = "driver";
        }
        $params = [
            'uid' => 0,
            'to_uid' => $to_uid,
            'msg_type' => MessageType::REPAIR,
            'push' => 1,
            'record' => 1,
            'client' => $client,

            'content' => $content,
            'extra' => $content,
            'title' => $title,
            'summary' => $content,
        ];
        $facade = new MessageFacade();
        return $facade->pushMsg($params,$after_open_extra);
    }


    //task - 司机维修自动完成
    public function autoDone($time = 0)
    {
        $map = [
            'update_time' => ['<', $time],
            'repair_status' => self::REPAIRED,
        ];
        $r = $this->queryNoPaging($map);
        foreach ($r as $v) {
            $params = [
                'uid' => $v['uid'],
                'id' => $v['id'],
                'group_id' => $this->driver_group,
                'evaluate' => "",
            ];
            $r = $this->done($params,true);
            // echo $r['info'].'<br/>';
        }
        // shalt('test');
    }


    //业务 - 维修完成 - 技工或司机 - push
    public function done($params,$isAuto=false){
      extract($params);
      //? 维修
      $r = $this->getInfo(['id'=>$id]);
      if(!$r) return returnErr(Linvalid('id'));
      $price  = (int) $r['price'];
      $fee    = (int) $r['fee'];
      $driver = (int) $r['uid'];
      $worker = (int) $r['worker_uid'];
        $repair_status = (int)$r['repair_status'];
      if($group_id == $this->driver_group){
        //? 发单的司机
        if($uid !== $driver) return returnErr(Linvalid('driver'));
        //? 技工维修完
        if ($repair_status !== self::REPAIRED)
         return returnErr(L('err_repair_status') . ':' . $this->getStatusMsg($repair_status));
        //? 技工获利
        $r = (new RepairOrderLogicV2())->getInfo(['repair_id'=>$id,'pay_status'=>RepairOrderLogicV2::PAYED]);
        if(!$r) return returnErr(L('err_no_payinfo'),true);
        $total_pay = intval($r['pay_money'])+intval($r['pay_balance']);
        if($total_pay != $price) return returnErr(L('err_price_not_equal_pay'),true);

        // $tax = (float) config('worker_tax_rate');
        // if($tax<0)  return returnErr('技工费率过小');
        // if($tax>=1) return returnErr('技工费率过大');
        Db::startTrans();
        // 更改业务状态
        $map = ['repair_status'=>self::SUCCESS];
        // ? 评价
        if($evaluate){
          $map['evaluate']      = $evaluate;
          $map['evaluate_time'] = time();
        }
        $this->save(['id'=>$id],$map);
        // 技工 + 加钱
        $gain = $total_pay - $fee ;//intval($total_pay * (1-$tax)); //分
        $tax_fee = $fee;//$total_pay-$gain;//分 手续费
        if($gain){
          $r = (new WalletLogic())->plusMoney($worker,$gain,'维修完结技工结算');
          if(!$r['status']) return returnErr($r['info'],true);
        }
        // 技工 + 积分
        $r = (new ScoreHisLogicV2)->changeScoreByRule($worker,ScoreHisLogicV2::REPAIR_DONE_WORKER_ADD,true);
        if(!$r['status']) return returnErr($r['info'],true);
        // 司机 + 积分
        $r = (new ScoreHisLogicV2)->changeScoreByRule($driver,ScoreHisLogicV2::REPAIR_DONE_DRIVER_ADD,true);
        if(!$r['status']) return returnErr($r['info'],true);

        // 推送
        $r = $this->pushRepairMsg($worker,'维修完结通知','您的维修已'.($isAuto ? '自动':'').'完结,您获得'.($gain/100).'元,已扣除手续费'.($tax_fee/100).'元,请自行查看余额,如有疑问请拨打客服电话','worker');
        Db::commit();
        //订单历史 写入
        $entity = [
          'reason'       =>'司机确认,维修完结',
          'create_time'  =>time(),
          'isauto'       =>intval($isAuto),
          'order_code'   =>'',
          'order_status' =>self::SUCCESS,
          'operator'     =>$uid,
          'repair_id'    =>$id,
        ];
        (new RepairOrderHisLogicV2())->add($entity);
      }elseif($group_id == $this->worker_group){
        //? 接单的技工
        if($uid !== $worker)
          return returnErr(Linvalid('worker'));
        //? 维修中
          if ($repair_status !== self::REPAIRING)
              return returnErr(Linvalid('pay_status') . ':' . $this->getStatusMsg($repair_status));
        //更改业务状态
        $this->save(['id'=>$id],['repair_status'=>self::REPAIRED]);
        //推送
        $r = $this->pushRepairMsg($driver,'技工维修完成','您的报修已维修完成','driver');

        //订单历史 写入
        $entity = [
          'reason'       =>'技工维修完成',
          'create_time'  =>time(),
          'isauto'       =>intval($isAuto),
          'order_code'   =>'',
          'order_status' =>self::REPAIRED,
          'operator'     =>$uid,
          'repair_id'    =>$id,
        ];
        (new RepairOrderHisLogicV2())->add($entity);
      }else{
        return returnErr(Linvalid('group_id'));
      }
      return returnSuc($r['status'] ? L('success') : $r['info']);
    }

    public function getStatusMsg($status)
    {
        switch ($status) {
            case self::TOBE_RECEIVE:
                return '待接单';
                break;
            case self::SUCCESS:
                return '已完结';
                break;
            case self::CANCEL:
                return '维修关闭';
                break;
            case self::TOBE_PRICE:
                return '待报价';
                break;
            case self::TOBE_PAY:
                return '待支付';
                break;
            case self::PAYING:
                return '支付中';
                break;
            case self::REPAIRING:
                return '维修中';
                break;
            case self::REPAIRED:
                return '技工维修完';
                break;

            default:
                return '';
                break;
        }
    }

    /**
     * 业务 - 接单 - push
     * 注意控制并发
     * @Author
     * @DateTime 2016-12-17T11:49:14+0800
     * @param    int|$uid     [description]
     * @param    int|$id      [description]
     * @return   [apiReturn]  [description]
     */
    public function take($uid=0,$id=0,$admin=false){
      //? 技工
      if(!(new WorkerLogicV2())->isWorker($uid)) return returnErr(L('err_account_no_permissions'));
      //? 维修中
      if($this->isRepairing($uid,$this->worker_group)) return returnErr(L('err_repairing'));
      Db::startTrans();
      //? 待维修单子
      $info = $this->getInfo(['id'=>$id]);
      if(!$info) return returnErr('订单不存在',true);
      $repair_status = intval($info['repair_status']);
      if($repair_status !== self::TOBE_RECEIVE){ //非待接单 状态
        if($repair_status === self::TOBE_PRICE)  return returnErr(L('err_repair_has_take'),true);
        else return returnErr('订单:'.$this->getStatusMsg($repair_status));
      }
      //接单 - 并发状态保证
      $r = $this->save(['id'=>$id,'repair_status'=>self::TOBE_RECEIVE],['repair_status'=>self::TOBE_PRICE,'worker_uid'=>$uid]);
      if(!$r) return returnErr(L('fail'),true);
      Db::commit();
      //推送
      $this->pushRepairMsg($info['uid'],'报修接单通知','您的报修已被接单','driver');
      $this->pushRepairMsg($uid,'报修接单通知',($admin ? '您已被指派接单' : '接单成功'),'worker');
      //写入订单历史
      $map   = [
        'reason'       =>'技工接单',
        'create_time'  =>time(),
        'isauto'       =>0,
        'order_code'   =>'',
        'order_status' =>self::TOBE_PRICE,
        'operator'     =>$uid,
        'repair_id'    =>$id,
      ];
      (new RepairOrderHisLogicV2())->add($map);

      return returnSuc('接单成功');
    }

    /**
     * 是否维修中 - 司机或技工
     * @Author
     * @DateTime 2016-12-14T16:30:05+0800
     * @param    [type]                   $uid [description]
     * @return   mixed                         [description]
     */
    public function isRepairing($uid = 0, $group_id = 0, $field = 'id')
    {
        $map = ['repair_status' => ['notin', [self::SUCCESS, self::CANCEL, self::TOBE_RECEIVE,self::REPAIRED]]];
        if ($group_id == $this->driver_group) {
            //司机
            $map['uid'] = $uid;
        } elseif ($group_id == $this->worker_group) {
            //技工
            $map['worker_uid'] = $uid;
        } else {
            //角色错误 - 抛出
            Linvalid("group_id", true);
            return true;//不会走到
      }
        $r = $this->getInfo($map, false, $field);
        return $r;
    }

    /**
     * 业务 - 维修列表
     * @Author
     * @DateTime 2016-12-21T13:37:57+0800
     * @return   [type]                   [description]
     */
    public function queryList($params){
      extract($params);
      $map = [];
      if($group_id == $this->driver_group){
        //司机
        $map['r.uid'] = $uid;
      }elseif($group_id == $this->worker_group){
        //技工
        $map['r.worker_uid'] = $uid;
      }else{
        //角色错误 - 抛出
        $msg = Linvalid("group_id",true);
        return returnErr($msg);//不会走到
      }
      if($this->isLegalRepairStatus($repair_status)){
        $map['r.repair_status'] = $repair_status;
      }
      $logic = new DatatreeLogicV2();
      $page = ['curpage'=>$current_page,'size'=>$per_page];
      $r = $this->query($map,$page,'r.id desc',false,'r.*,o.order_code');
      $member  = new MemberLogic();
      $umember = new UcenterMemberLogic();
      foreach ($r['list'] as &$v) {
        $v['driver_realname'] = $member->getOneInfo($uid,'realname');
        $v['worker_realname'] = $member->getOneInfo($v['worker_uid'],'realname');
        $v['worker_mobile']   = $umember->getOneInfo($v['worker_uid'],'mobile');
        $v['repair_name']  = '';//$logic->getNameById($v['repair_type']);
        $v['vehicle_name'] = $logic->getNameById($v['vehicle_type']);
      }
      return returnSuc($r);
    }
    /**
     * 是否为合法的维修状态
     * @Author
     * @DateTime 2016-12-21T13:48:22+0800
     * @param    integer                  $repair_status [description]
     * @return   boolean                                 [description]
     */
    public function isLegalRepairStatus($repair_status=-1){
      $legal = [self::TOBE_RECEIVE,self::SUCCESS,self::CANCEL,self::TOBE_PRICE,self::TOBE_PAY,self::PAYING,self::REPAIRING,self::REPAIRED];
      return in_array($repair_status, $legal);
    }

    /**
     * query
     * @param 查询条件|null $map
     * @param array|分页参数 $page
     * @param bool|排序参数 $order
     * @param bool|点击分页时带参数 $params
     * @param bool $fields
     * @return array
     * @internal param 查询条件 $map
     * @internal param 分页参数 $page
     * @internal param 排序参数 $order
     * @internal param 点击分页时带参数 $params
     */
    public function query($map = null, $page = ['curpage' => 1, 'size' => 10], $order = false, $params = false, $fields = false)
    {
      $model = $this->getModel();
      if (!empty($map)) $query = $model->alias('r')->where($map);
      if (false !== $order) $query = $query->order($order);
      if (false !== $fields) $query = $query->field($fields);
      $query = $query->join(['itboye_repair_order o', ''], 'o.repair_id = r.id', 'left');
      $start = max(intval($page['curpage']) - 1, 0) * intval($page['size']);
      $list = $query->limit($start, $page['size'])->select();
      $count = $model->alias('r')->where($map)->count();
      return ["count" => $count, "list" => $list];
    }

    /**
     * 业务 - 可接单列表
     * @Author
     * @DateTime 2016-12-17T09:44:06+0800
     * @param    [type]                   $params [description]
     * @return   [apiReturn]                      [description]
     */
    public function unTakeList($params){
      extract($params);
      $order = intval($order);
      if($uid && $lng && $lat){
        //更新用户坐标
        $r = (new MemberLogic())->saveById($uid,['lng'=>$lng,'lat'=>$lat]);
        if(!$r['status']) return returnErr($r['info']);
      }
      if($order==1){
        //距离排序
        if(!$lng || !$lat) return returnErr(Llack('lng or lat'));
        $order = $this->getPosOrder($lng,$lat);
      }elseif($order ==2){
        //最新发布
        $order = 'r.create_time desc';
      }else{
        //默认
        $order = 'r.id desc';
      }
      $map = [];
      $km = round(intval($km)/111,4); //经纬度差
      $map['r.lng'] = ['between',[$lng-$km,$lng+$km]];
      $map['r.lat'] = ['between',[$lat-$km,$lat+$km]];
      //可接单的类型
      $map['r.repair_status'] = self::TOBE_RECEIVE;
      //uid对应的技能对应的维修类别
      // $map = [
      //   'r.repair_type'=>['in',[]]
      // ];
      // if($city){
      //   $map['r.city'] = $city;
      // }
      // if($area){
      //   $map['r.area'] = $area;
      //   unset($map['city']);
      // }
      $logic = new DatatreeLogicV2();
      $page = ['curpage'=>$current_page,'size'=>$per_page];
      $r = $this->query($map,$page,$order,false,'r.id,r.mobile,r.images,r.address,r.detail,r.lng,r.lat,r.create_time,r.update_time,r.repair_type,r.vehicle_type');
      if($current_page===1 && empty($r['list'])){
        unset($map['r.lng']);
        unset($map['r.lat']);
        $r = $this->query($map,$page,$order,false,'r.id,r.mobile,r.images,r.address,r.detail,r.lng,r.lat,r.create_time,r.update_time,r.repair_type,r.vehicle_type');
      }
      foreach ($r['list'] as &$v) {
        $v['repair_name']  = '';//$logic->getNameById($v['repair_type']);
        $v['vehicle_name'] = $logic->getNameById($v['vehicle_type']);
      }
      return returnSuc($r);
    }

    private function getPosOrder($lng=0,$lat=0){
      $lng = (float) $lng;
      $lat = (float) $lat;
      return " pow(abs($lng-lng),2)+pow(abs($lat-lat),2) asc";
    }

    /**
     * 业务 - 设置维修金额 - 可多次 $uid $price(分) - push
     * @Author
     * @DateTime 2016-12-19T14:17:18+0800
     * @param    [apiReturn]  $params [description]
     */
    public function setPrice($params){
      extract($params);
      //? uid的维修
      $r = $this->getInfo(['worker_uid'=>$uid,'id'=>$id]);
      if(!$r) return returnErr(Linvalid('uid or id'));
      //? 可设置价格
      $repair_status = (int) $r['repair_status'];
      if(!in_array($repair_status,[self::TOBE_PRICE,self::TOBE_PAY])){
          if($repair_status === self::PAYING){
            return returnErr('司机正在支付，不能修改报价');
          }else{
            return returnErr(L('err_repair_status') . ':' . $this->getStatusMsg($repair_status));
          }
      }
      $driver = (int) $r['uid'];

      $tax = (float) config('worker_tax_rate');
      if($tax<0)  return returnErr('技工费率过小');
      if($tax>=1) return returnErr('技工费率过大');
      $price = $repair_price+$stuff_price;
      $fee = ceil($price*$tax);//$price- intval($price * (1-$tax));
      $r = $this->save(['id'=>$id,'worker_uid'=>$uid],['price'=>($repair_price + $stuff_price),'repair_price'=>$repair_price,'stuff_price'=>$stuff_price,'repair_status'=>self::TOBE_PAY,'fee'=>$fee]);
      //推送
      $r = $this->pushRepairMsg($driver,'维修报价通知','您的维修已报价,请自行查看','driver');
      //订单历史 写入
      $entity = [
        'reason'       =>'技工报价:'.($repair_price/100).':'.($stuff_price/100),
        'create_time'  =>time(),
        'isauto'       =>0,
        'order_code'   =>'',
        'order_status' =>self::TOBE_PAY,
        'operator'     =>$uid,
        'repair_id'    =>$id,
      ];
      (new RepairOrderHisLogicV2())->add($entity);
      return returnSuc($r['status'] ? L('success') : $r['info']);
    }
    /**
     * 业务 - 维修中
     * @Author
     * @DateTime 2016-12-19T14:02:54+0800
     * @param    [type]                   $params [description]
     * @return   [apiReturn]                      [description]
     */
    public function current($params){
      extract($params);
      $r = $this->isHandling($uid,$group_id,'id,mobile,images,address,detail,lng,lat,create_time,update_time,repair_status,price,repair_price,stuff_price,worker_uid,repair_type,vehicle_type');
      if($r){
        //查询师傅信息
        $worker_uid = (int) $r['worker_uid'];
        if($worker_uid){
          $r['worker_info'] = (new WorkerLogicV2())->getWorkerInfo($worker_uid);
        }else{
          $r['worker_info'] = ['temp'=>''];
        }
        $logic = new DatatreeLogicV2();
        $r['repair_name']  = $logic->getNameById($r['repair_type']);
        $r['vehicle_name'] = $logic->getNameById($r['vehicle_type']);
      }else{
        $r = ['temp'=>''];
      }
      return returnSuc($r);
    }

    /**
     * 是否有未处理的单子  - 司机或技工
     * >未支付 是查询 score score_pay
     * @Author
     * @DateTime 2016-12-14T16:30:05+0800
     * @param    [type]                   $uid [description]
     * @return   mixed                         [description]
     */
    public function isHandling($uid = 0, $group_id, $field = 'id')
    {
        if ($group_id == $this->driver_group) {
            //司机
            $map = ['repair_status' => ['notin', [self::SUCCESS, self::CANCEL]]];
            $map['uid'] = $uid;
        } elseif ($group_id == $this->worker_group) {
            //技工
            $map = ['repair_status' => ['notin', [self::SUCCESS, self::CANCEL,self::REPAIRED]]];
            $map['worker_uid'] = $uid;
        } else {
            //角色错误 - 抛出
            $msg = Linvalid("group_id", true);
            return returnErr($msg);//不会走到
        }
        $r = $this->getInfo($map, false, $field);
        return $r;
    }

    /**
     * 业务 - 维修真删除(未接单)
     * @Author
     * @DateTime 2016-12-19T15:20:33+0800
     * @param    [type]                   $params [description]
     * @return   [apiReturn]                      [description]
     */
    public function del($params){
      extract($params);
      $map = ['id'=>$id,'uid'=>$uid];
      $info = $this->getInfo($map);
      if(!$info){
        return returnErr(Linvalid('uid or id'));
      }
      $status = intval($info['repair_status']);
      if($status !== self::TOBE_RECEIVE){
        if($status === self::TOBE_PRICE) return returnErr('已有技工接单了,请刷新查看');
        return returnErr('非待接订单,请刷新查看');
      }
      return returnSuc(parent::delete($map));
    }

    /**
     * 业务 - 取消接单(=>未接单) - admin
     * @Author
     * @DateTime 2016-12-19T15:20:33+0800
     * @param    [type]      $params [description]
     * @return   [apiReturn] [description]
     */
    public function cancelWorker($id=0,$is_auto=0){
      $map = ['id'=>$id];
      $r = $this->getInfo($map);
      if(!$r) return returnErr(Linvalid('id'));
      $status = (int) $r['repair_status'];
      if(!in_array($status,[self::TOBE_PRICE,self::TOBE_PAY])) return returnErr($this->getStatusMsg($status).'状态下不允许取消派单');
      $this->save(['id'=>$id],['repair_status'=>self::TOBE_RECEIVE,'worker_uid'=>0]);
      //司机推送
      if(intval($r['uid'])) $this->pushRepairMsg($r['uid'], '维修接单取消通知', '请注意,您的维修接单已取消', 'driver');
      //技工推送
      if(intval($r['worker_uid'])) $this->pushRepairMsg($r['worker_uid'], '维修接单取消通知', '请注意,您的维修接单已取消', 'worker');

      //订单历史 写入
      $entity = [
        'reason'       =>'取消接单',
        'create_time'  =>time(),
        'isauto'       =>intval($is_auto),
        'order_code'   =>'',
        'order_status' =>self::TOBE_RECEIVE,
        'operator'     =>-1,
        'repair_id'    =>$id,
      ];
      (new RepairOrderHisLogicV2())->add($entity);
      return returnSuc(L('success'));
    }

    //自动关闭 - 超时未接单
    public function autoClose($time = 0,$echo = true){
      $map = [
          'update_time'   => ['<', $time],
          'repair_status' => self::TOBE_RECEIVE,
      ];
      $r = $this->queryNoPaging($map);
      foreach ($r as $v) {
          $r2 = $this->cancel($v['id'],1);
          $msg = '关闭维修超时未接单订单'.$v['id'].':'.$r2['info'].'<br/>';
          if($echo) echo $msg;
          addTestLog('autoClose',$msg,date('Y-m-d H:i'));
      }
    }
    /**
     * 业务 - 取消维修(=>取消) - admin
     * @Author
     * @DateTime 2016-12-19T15:20:33+0800
     * @param    [type]      $params [description]
     * @return   [apiReturn] [description]
     */
    public function cancel($id=0,$is_auto=0){
      $map = ['id'=>$id];
      $is_auto = $is_auto ? '超时自动' : '';
      $r = $this->getInfo($map);
      if(!$r) return returnErr(Linvalid('id'));
      $status = (int) $r['repair_status'];
      if(in_array($status,[self::SUCCESS,self::CANCEL,self::REPAIRED])) return returnErr($this->getStatusMsg($status).'状态下不允许关闭维修');
      $this->save(['id'=>$id],['repair_status'=>self::CANCEL]);
      //司机推送
      if(intval($r['uid'])) $this->pushRepairMsg($r['uid'], '维修关闭通知', '请注意,您的维修已'.$is_auto.'关闭', 'driver');
      //技工推送
      if(intval($r['worker_uid'])) $this->pushRepairMsg($r['worker_uid'], '维修关闭通知', '请注意,您的维修已'.$is_auto.'关闭', 'worker');
      //订单历史 写入
      $entity = [
        'reason'       =>'维修关闭',
        'create_time'  =>time(),
        'isauto'       =>intval($is_auto),
        'order_code'   =>'',
        'order_status' =>self::CANCEL,
        'operator'     =>$is_auto ? 0 : -1,
        'repair_id'    =>$id,
      ];
      (new RepairOrderHisLogicV2())->add($entity);
      return returnSuc(L('success'));
    }

    /**
     * 业务 - 获取维修的依赖
     * @return [apiReturn]
     */
    public function rely(){
      $api = new DatatreeLogicV2();

      $rsp = [];
      //车辆类型
      $r = $api->getCacheList(['parentid'=>getDtreeId('vehicle_type')]);
      $rsp['vehicle_type'] = array_values($r);
      //维修类型
      $r = $api->getCacheList(['parentid'=>getDtreeId('repair_type')]);
      $rsp['repair_type'] = array_values($r);
      return returnSuc($rsp);
    }

    /**
     * 业务 - 报修
     * @return [apiReturn]
     */
    public function add($params,$pk='id'){
      extract($params);
      //? 司机+uid
      if(!(new DriverLogicV2())->isDriver($uid)) return returnErr(L('err_account_no_permissions'));

      //? 维修中
      if($this->isHandling($uid,$this->driver_group)) return returnErr(L('err_repairing'));

      $api = new DatatreeLogicV2();
      //? vehicle_type
      if(!$api->isParent($vehicle_type,getDtreeId('vehicle_type'))) return returnErr(Linvalid('vehicle_type'));
      //? repair_type
      // if(!$api->isParent($repair_type,getDtreeId('repair_type'))) return returnErr(Linvalid('repair_type'));
      //? 经+纬度
      if(!$lat && !$lng){
        //获取经纬度
        $r = getAddressPos($address);
        if(!$r['status']) return $r;
        $params['lng']  = $r['info']['lng'];
        $params['lat']  = $r['info']['lat'];
      }
      //? city
      // if(!((new CityLogicV2())->isExistCode($city))) return returnErr(Linvalid('city'));
      //? area
      // if(!(new AreaLogicV2())->isExistCode($area)) return returnErr(Linvalid('area'));
      $id = parent::add($params);
      if($id){
        $this->pushNearBy($id,1000);
        //订单历史 写入
        $entity = [
          'reason'       =>'司机报修',
          'create_time'  =>time(),
          'isauto'       =>0,
          'order_code'   =>'',
          'order_status' =>self::TOBE_RECEIVE,
          'operator'     =>$uid,
          'repair_id'    =>$id,
        ];
        (new RepairOrderHisLogicV2())->add($entity);

        return returnSuc(L('success'));
      }
      return  returnSuc(L('fail'));;
    }

    /**
     * @return mixed
     */
    protected function _init()
    {
        $this->setModel(new Repair());
        $this->driver_group = 6;
        $this->worker_group = 7;
    }
}