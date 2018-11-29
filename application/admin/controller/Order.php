<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\admin\controller;

use app\src\i18n\logic\OrgMemberLogic;
use app\src\log\logic\ExchangeLogLogic;
use app\src\message\logic\MessageLogic;
use app\src\order\logic\OrdersCommentLogic;
use app\src\order\logic\OrdersReFundLogic;
use app\src\system\logic\CityLogic;
use app\src\system\logic\DatatreeLogicV2;
use app\src\system\logic\ProvinceLogic;
use think\Log;
use app\src\order\logic\OrdersLogic;
use app\src\order\logic\OrdersInfoViewLogic;
use app\src\order\logic\OrderStatusHistoryLogic;
use app\src\goods\logic\ProductFaqLogic;
use app\src\order\logic\OrdersItemLogic;
use app\src\order\logic\OrdersExpressLogic;
use app\src\user\logic\MemberLogic;
class Orders extends Admin{
  /**
   * 初始化
   */
  protected function _initialize() {
    parent::_initialize();
  }

  /**
   * 订单上报
   */
  public function upload(){
    $map['pay_status'] = \app\src\order\model\Orders::ORDER_PAID;
    $map['order_status'] = \app\src\order\model\Orders::ORDER_SHIPPED;
    $params = array();
    if (!empty($order_code)) {
      $map['order_code'] =$order_code;
      $params['order_code'] = $order_code;
      $this->assign("order_code",$order_code);
    }

    $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
    $order = " createtime asc ";


//    $result = apiCall(OrdersInfoViewApi::QUERY, array($map, $page, $order, $params));
        $result = (new OrdersInfoViewLogic())->queryWithPagingHtml($map, $page, $order,$params);
    if(!$result['status']){
      $this->error($result['info']);
    }

    $this -> assign('list', $result['info']['list']);
    $this -> assign('show', $result['info']['show']);
    return $this->boye_display();

  }

  /**
   * 商家主动退回订单
   */
  public function backOrder(){
    $id = $this->_param('post.orderid',0);
    $reason = $this->_param('post.reason','商家主动取消订单');

//    $result = apiCall(OrdersApi::GET_INFO, array(array()));
    $result = (new OrdersLogic())->getInfo([['id'=>$id]]);
    if(!$result['status']){
      $this->error($result['status']);
    }

    if(is_null($result)){
      $this->error("订单信息获取失败，请重试！");
    }

    $cur_status = $result['info']['order_status'];
    $uid = $result['info']['uid'];

    //检测当前订单状态是否合法
    if($cur_status != 2){
      $this->error("当前订单状态无法变更！");
    }
    $order_code = $result['info']['order_code'];
    //
//    $result = apiCall(OrderStatusApi::BACK_ORDER, array($order_code,$reason,false,UID));
    $result = (new OrderStatusHistoryLogic())->backOrder([$order_code,$reason,false,UID]);

    if(!$result['status']){
      $this->error($result['info']);
    }

    //===========推送给用户消息↓
    $text = "您的订单:".$order_code." [被退回],原因:".$reason.". [查看详情]";
    $msg = array(
      'title'=>'订单退回通知',
      'content'=>$text,
      'summary'=>'订单被退回'
    );
    $this->pushOrderMessage($order_code,$msg);
    //===========推送给用户消息↑

    //======================================
    $this->success("退回成功!");

  }

  /**
   * 数据统计
   */
  public function statics(){
    //TODO:可能数据库写一个视图效率更高吧，之后考虑
    //TODO:目前先是全表统计，之后可能考虑按时间，当天，本月，本年计算


    //地区管理员所属地区限制
    $AreaMap = $this->OrgAreaMap();

    /**
     * 订单退回
     */
    $map=array(
      'order_status'=>\app\src\order\model\Orders::ORDER_BACK,
    );
    if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//    $orderback=apiCall(OrdersInfoViewApi::COUNT,array($map));
        $orderback = (new OrdersInfoViewLogic())->count($map);
    /**
     * 待确认
     */
    $map=array(
      'order_status'=>\app\src\order\model\Orders::ORDER_TOBE_CONFIRMED,
    );
    if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//    $ordertobeconfirmed=apiCall(OrdersInfoViewApi::COUNT,array($map));
    $ordertobeconfirmed= (new OrdersInfoViewLogic())->count($map);
    /**
     * 待发货
     */
    $map=array(
      'order_status'=>\app\src\order\model\Orders::ORDER_TOBE_SHIPPED,
    );
    if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//    $ordertobeshipped=apiCall(OrdersInfoViewApi::COUNT,array($map));
    $ordertobeshipped=(new OrdersInfoViewLogic())->count($map);
    /**
     * 已发货
     */
    $map=array(
      'order_status'=>\app\src\order\model\Orders::ORDER_SHIPPED,
    );
    if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//    $ordershipped=apiCall(OrdersInfoViewApi::COUNT,array($map));
    $ordershipped = (new OrdersInfoViewLogic())->count($map);
    /**
     * 已收货
     */
    $map=array(
      'order_status'=>\app\src\order\model\Orders::ORDER_RECEIPT_OF_GOODS,
    );
    if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//    $orderreceiptofgoods=apiCall(OrdersInfoViewApi::COUNT,array($map));
    $orderreceiptofgoods=(new OrdersInfoViewLogic())->count($map);
    /**
     * 已退货
     */
    $map=array(
      'order_status'=>\app\src\order\model\Orders::ORDER_RETURNED,
    );
    if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//    $orderreturned=apiCall(OrdersInfoViewApi::COUNT,array($map));
    $orderreturned=(new OrdersInfoViewLogic())->count($map);
    /**
     * 已完成
     */
    $map=array(
      'order_status'=>\app\src\order\model\Orders::ORDER_COMPLETED,
    );
    if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//    $ordercompleted=apiCall(OrdersInfoViewApi::COUNT,array($map));
    $ordercompleted=(new OrdersInfoViewLogic())->count($map);
    /**
     * 取消或交易关闭
     */
    $map=array(
      'order_status'=>\app\src\order\model\Orders::ORDER_CANCEL,
    );
    if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//    $ordercancel=apiCall(OrdersInfoViewApi::COUNT,array($map));
    $ordercancel=(new OrdersInfoViewLogic())->count($map);
    //dump($orderback);
    $orderstatus=array(
      'order_back'=>$orderback['info'],
      'order_tobe_confirmed'=>$ordertobeconfirmed['info'],
      'order_tobe_shipped'=>$ordertobeshipped['info'],
      'order_shipped'=>$ordershipped['info'],
      'order_receipt_of_goods'=>$orderreceiptofgoods['info'],
      'order_returned'=>$orderreturned['info'],
      'order_completed'=>$ordercompleted['info'],
      'order_cancel'=>$ordercancel['info']
    );
    $map=array();
    if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//    $price=apiCall(OrdersInfoViewApi::SUM,array($map,"price"));
    $price=(new OrdersInfoViewLogic())->sum($map,"price");

    /**
     * 商品咨询待回复
     */
    $map=array(
      'reply_time'=>0,
    );
//    $faqtobereply = apiCall(ProductFaqApi::COUNT,array($map));
    $faqtobereply = (new ProductFaqLogic())->count($map);
    /**
     * 积分商品待发货
     */
    $map=array(
      'exchange_status'=>0,
    );
    $score_goods_tobe_deliver = ['info'=>''];// (new ExchangeLogLogic())->count($map);

    $this->assign('order_status',$orderstatus);
    $this->assign('price',$price['info']);
    $this->assign('faqtobereply',$faqtobereply['info']);
        $this->assign('score_goods_tobe_deliver',$score_goods_tobe_deliver['info']);
    return $this->boye_display();
  }

    /**
     * @param int $id
     * @discription 评价管理
     */
  public function commentEdit($id=0){
    if (IS_GET) {
//      $r  = apiCall(OrderCommentApi::GET_INFO,array(array('id'=>$id)));
      $r  = (new OrdersCommentLogic())->getInfo(['id'=>$id]);
      if($r['status']){
        $this->assign('entry',$r['info']);
        return $this->boye_display();
      }else{
        Log::record('INFO:' . $r['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
        $this->error($r['info']);
      }
    }else{
      $comment = $this->_param('comment','','内容错误');
//      $r = apiCall(OrderCommentApi::SAVE,array(array('id'=>$id),array('comment'=>$comment)));
      $r = (new OrdersCommentLogic())->save(array('id'=>$id),array('comment'=>$comment));
      if($r['status']){
        $this->success('修改成功！');
      }else{
        Log::record('INFO:' . $r['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
        $this->error($r['info']);
      }
    }
  }
  /**
   * 订单评价 - 分页
   */
  public function comment() {
    $p     = $this->_param('p',0);
    $uid   = $this->_param('uid',0);
    $map   = array();
    $param = array();
    if($uid){
      $map['c.user_id'] = $uid;
      $this->assign('uid',$uid);
    }
    $start = $this->_param('startdatetime',0);
    $this->assign('startdatetime',$start);
    $end   = $this->_param('enddatetime',0);
    $this->assign('enddatetime',$end);
    if($start>0 && $end>0){
      $param['startdatetime'] = $start;
      $param['enddatetime']   = $end;
      $start = strtotime($start);
      $end = strtotime($end);
      $map['createtime'] = array('BETWEEN',$start);
    }elseif($start>0){
      $param['startdatetime'] = $start;
      $start = strtotime($start);
      $map['createtime'] = array('GT',$start);
    }elseif($end>0){
      $param['enddatetime']   = $end;
      $end = strtotime($end);
      $map['createtime'] = array('LT',$start);
    }
    $order = $this->_param('order_code','');
    $this->assign('order',$order);
    if($order){
      $param['order_code'] = $order;
      $map['order_code']   = array('LIKE',$order_code);
    }
//    $r = apiCall(OrderCommentApi::QUERY_WITH_USER,array($map,array('curpage'=>$p,'size'=>10),false,$param,'c.*,m.nickname'));
    $r = (new OrdersCommentLogic())->queryWithUser($map,array('curpage'=>$p,'size'=>10),false,$param,'c.*,m.nickname');
    // dump($r);exit;
    if($r['status']){
      $this -> assign('list', $r['info']['list']);
      $this -> assign('show', $r['info']['show']);
      return $this -> boye_display();
    }else{
      Log::record('INFO:' . $r['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
      $this -> error($r['info']);
    }
  }
  /**
   * 订单管理
   */
  public function index() {
    $map = [];
    $params = [];
    $payStatus     = $this->_param('paystatus', '');
    $orderStatus   = $this->_param('orderstatus', '');
    $commentStatus = $this->_param('commentstatus', '');
    $order_code    = $this->_param('order_code', '');
    $userid        = $this->_param('uid', 0);
    $nickname      = $userid ? getNickname($userid) : '';
    $this->assign('uid',$userid);
    $this->assign('nickname',$nickname);

    $startdatetime = $this->_param('startdatetime');
    $startdatetime = strtotime($startdatetime);
    $enddatetime   = $this->_param('enddatetime');
    $enddatetime   = strtotime($enddatetime);
    if(!empty($startdatetime) && !empty($enddatetime)){
        if($startdatetime === FALSE || $enddatetime === FALSE){
            $params = array('startdatetime' =>$startdatetime, 'enddatetime' =>$enddatetime,'wxaccountid'=>getWxAccountID());
            $map['createtime'] = array( array('EGT', $startdatetime), array('ELT', $enddatetime), 'and');
            $startdatetime = date("Y-m-d h:i:s", $startdatetime);
            $enddatetime   = date("Y-m-d h:i:s", $enddatetime);
        }else{
            $params = array('startdatetime' => $startdatetime, 'enddatetime' =>$enddatetime,'wxaccountid'=>getWxAccountID());
            $map['createtime'] = array( array('EGT', $startdatetime), array('ELT', $enddatetime), 'and');
            $startdatetime = date("Y-m-d h:i:s", $startdatetime);
            $enddatetime   = date("Y-m-d h:i:s", $enddatetime);
        }
    }
    if (!empty($order_code)) {
      $map['order_code'] = array('like', $order_code . '%');
    }
    if ($nickname != '') {
      $map['username']   = $nickname;
      $params['username'] = $nickname;
    }
    if ($payStatus != '') {
      $map['pay_status']   = $payStatus;
      $params['paystatus'] = $payStatus;
    }
    if ($orderStatus != '') {
      $map['order_status']   = $orderStatus;
      $params['orderstatus'] = $orderStatus;
    }
    if ($commentStatus != '') {
      $map['comment_status']   = $commentStatus;
      $params['commentstatus'] = $commentStatus;
    }

    $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
    $order = " createtime desc ";

    if ($userid > 0){
       $map['uid'] = $userid;
    }

//    $result = (new OrdersInfoViewLogic())->queryWithPagingHtml($map, $page, $order);
    $result = (new OrdersInfoViewLogic())->queryWithPagingHtml($map, $page, $order,$params);

    //
    if ($result['status']) {
      $this -> assign('order_code', $order_code);
      $this -> assign('nickname', $nickname);
      $this -> assign('orderStatus', $orderStatus);
      $this -> assign('commentStatus', $commentStatus);
      $this -> assign('payStatus', $payStatus);
      $this -> assign('startdatetime', $startdatetime);
      $this -> assign('enddatetime', $enddatetime);
      $this -> assign('list', $result['info']['list']);
      $this -> assign('show', $result['info']['show']);
      return $this -> boye_display();
    } else {
      Log::ecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
      $this -> error($result['info']);
    }
  }

  /**
   * 订单确认
   */
  public function sure() {
    $order_code = $this->_param('order_code', '');
        $nickname = $this->_param('nickname','');
    $payStatus = $this->_param('payStatus', "");
    $userid = $this->_param('uid', 0);
//    $params = array();
        $params =false;
    $map = array();
    $map['order_status'] = \app\src\order\model\Orders::ORDER_TOBE_CONFIRMED;
    if($payStatus !== ""){
      $map['pay_status'] = $payStatus;
    }

    //$map['wxaccountid']=getWxAccountID();
    $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
    $order = " createtime desc ";

    if (!empty($order_code)) {
      $map['order_code'] = array('like', $order_code . '%');
      $params['order_code'] = $order_code;
    }
        if (!empty($nickname)) {
            $map['nickname'] = array('like', $nickname . '%');
            $params['nickname'] = $nickname;
        }
    if ($userid > 0) {
      $map['uid'] = $userid;
      $params['uid'] = $userid;
    }

    //地区管理员所属地区限制
    $AreaMap = $this->OrgAreaMap();
    if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//    $result = apiCall(OrdersInfoViewApi::QUERY, array($map, $page, $order, $params));
        $result = (new OrdersInfoViewLogic())->queryWithPagingHtml($map, $page, $order,$params);

    //
    if ($result['status']) {

      $this -> assign('order_code', $order_code);
      $this -> assign('payStatus', $payStatus);
      $this -> assign('nickname', $nickname);
      //$this -> assign('orderStatus', $orderStatus);
      $this -> assign('show', $result['info']['show']);
      $this -> assign('list', $result['info']['list']);
      return $this -> boye_display();
    } else {
      Log::record('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
      $this -> error($result['info']);
    }
  }

  /**
   * 发货
   */
  public function deliverGoods() {
    $orderStatus = $this->_param('order_status','3');
    $ordercode = $this->_param('order_code', '');
    $nickname = $this->_param('nickname', '');
    $userid = $this->_param('uid', 0);
    $params = array();

    $map = array();
    //$map['wxaccountid']=getWxAccountID();
    //$params['uid'] = $map['uid'];
    if (!empty($ordercode)) {
      $map['order_code'] = array('like','%'. $ordercode . '%');
      $params['order_code'] = $ordercode;
    }
        if (!empty($nickname)) {
            $map['nickname'] = array('like','%'.$nickname . '%');
            $params['nickname'] = $nickname;
        }
    if($orderStatus != ''){
      $map['order_status'] = $orderStatus;
      $params['order_status'] = $orderStatus;
    }

    $map['pay_status']=array(
      'in',array(\app\src\order\model\Orders::ORDER_CASH_ON_DELIVERY,\app\src\order\model\Orders::ORDER_PAID)
    );
    $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
    $order = " createtime desc ";

    if ($userid > 0) {
      $map['u'] = $userid;
    }

    //地区管理员所属地区限制
    $AreaMap = $this->OrgAreaMap();
    if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//    $result = apiCall(OrdersInfoViewApi::QUERY, array($map, $page, $order, $params));
    $result = (new OrdersInfoViewLogic())->queryWithPagingHtml($map, $page, $order, $params);
    //
    if ($result['status']) {
      $this -> assign('order_code', $ordercode);
      $this -> assign('order_status', $orderStatus);
      $this -> assign('nickname', $nickname);
      $this -> assign('show', $result['info']['show']);
      $this -> assign('list', $result['info']['list']);
      return $this -> boye_display();
    } else {
      Log::record('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
      $this -> error($result['info']);
    }
  }

  /**
   * 查看
   */
  public function view() {
    if (IS_GET) {
      $id = $this->_param('id',0);
      $map = array('id'=>$id);
//      $result = apiCall(OrdersInfoViewApi::GET_INFO, array($map));
      $result = (new OrdersInfoViewLogic())->getInfo($map);
      if ($result['status']) {
        // dump($result['info']);
        $order_code     = $result['info']['order_code'];
        $comment_status = $result['info']['comment_status'];
        $order_create   = $result['info']['createtime'];
        $result['info']['pay_three'] = $result['info']['price'] - $result['info']['discount_money'] - $result['info']['pay_balance'];

        $this -> assign("order", $result['info']);
        //同时查询套餐信息
//        $result = apiCall(OrdersItemApi::QUERY_NO_PAGING, array(array('order_code'=>$order_code)));
        $result = (new OrdersItemLogic())->queryNoPaging(['order_code'=>$order_code]);
        if(!$result['status']){
          ifFailedLogRecord($result, __FILE__.__LINE__);
          $this->error($result['info']);
        }
        // dump($result['info']);exit;
        $pacakge_info = [];
        $items = $result['info'];
        if($comment_status != \app\src\order\model\Orders::ORDER_TOBE_EVALUATE){
          foreach ($items as &$v) {
            //已评价订单
            //商品评价详情
//            $result = apiCall(OrderCommentApi::GET_INFO,array(array('order_code'=>$v['order_code'],'product_id'=>$v['p_id'],'psku_id'=>$v['psku_id'],'group_id'=>$v['group_id'],'package_id'=>$v['package_id'])));
            $result = (new OrdersCommentLogic())->getInfo(['order_code'=>$v['order_code'],'product_id'=>$v['p_id'],'psku_id'=>$v['psku_id'],'group_id'=>$v['group_id'],'package_id'=>$v['package_id']]);
            if(!$result['status']){
              ifFailedLogRecord($result, __FILE__.__LINE__);
              $this->error($result['info']);
            }
            $this->getAttachs($result['info']['id'],10,$result['info']);
            $v['comment_info'] = $result['info'];
          }
        }
        // dump($items);exit;
        $this -> assign("items", $items);

        //查询订单状态变更纪录
//        $result = apiCall(OrderStatusHistoryApi::QUERY_NO_PAGING, array(array('order_code'=>$order_code),"create_time asc"));
        $result = (new OrderStatusHistoryLogic())->queryNoPaging(['order_code'=>$order_code],"create_time asc");
//        dump($result);
        if(!$result['status']){
          ifFailedLogRecord($result, __FILE__.__LINE__);
          $this->error($result['info']);
        }
        array_unshift($result['info'],array('id'=>'0','reason'=>'订单生成','create_time'=>$order_create,'status_type'=>'ADD'));
        $this -> assign("statushistory", $result['info']);
        return $this -> boye_display();
      } else {
        $this -> error($result['info']);
      }
    }
  }

  /**
   * 单个发货操作
   */
  public function deliver() {
    $result = (new DatatreeLogicV2())->queryNoPaging(['parentid'=>6003]);
    $expresslist = array();
    if(!empty($result)){
      foreach($result as $val){
        $expresslist[$val['code']] = $val['name'];
      }
    }else{
      $this->error("物流公司列表获取失败！");
    }
    if (IS_GET) {
      $id = $this->_param('id',0);
      $map = array('id'=>$id);
      $result = (new OrdersInfoViewLogic())->getInfo($map);
      if($result['status']){
        $this->assign("order",$result['info']);
      }else{
        $this->error("订单信息获取失败！");
      }
      $this->assign("expresslist",$expresslist);
      return $this->boye_display();
    } elseif (IS_POST) {

      $expresscode = $this->_param('expresscode','');
      $expressno = $this->_param('expressno','');
      $uid = $this->_param('uid',0);
      $ordercode = $this->_param('ordercode','');
      $orderOfid = $this->_param('orderOfid','');
      if(empty($expresscode) || !isset($expresslist[$expresscode])){
        $this->error("快递信息错误！");
      }
      if(empty($expressno)){
        $this->error("快递单号不能为空");
      }
      $id = $this->_param('id',0);
      $entity = array(
        'expresscode'=>$expresscode,
        'expressname'=>$expresslist[$expresscode],
        'expressno'=>$expressno,
        'note'=>$this->_param('note',''),
        'order_code'=>$ordercode,
        'uid'=>$uid,
      );

      if(empty($entity['order_code'])){
        $this->error("订单编号不能为空");
      }
            $result = (new OrdersExpressLogic())->add($entity);

            if($result['status']){
                $deliver = true;
            }else{
                $deliver = false;
            }

      if($result['status']){

        // 1. 修改订单状态为已发货
//        $result = apiCall(OrderStatusApi::SHIPPED, array($ordercode,false,UID));
        $result = (new OrdersLogic())->stateChange($ordercode,$uid);

        if(!$result['status']){
          ifFailedLogRecord($result['info'], __FILE__.__LINE__);
        }

        //========================================
        if($deliver) {
          $text = "亲，您的订单($ordercode)已发货，快递单号：$expressno,快递公司：".$expresslist[$expresscode].",请注意查收";
          $summary = '订单已发货';
        }else{
          $text = "亲，您的订单($ordercode)物流信息已改变，快递单号：$expressno,快递公司：".$expresslist[$expresscode].",请注意查收";
          $summary = '物流信息已改变';
        }

        // 2.发送提醒信息给指定用户
        //===========推送给用户消息↓
        $msg = array(
          'title'=>'物流通知',
          'content'=>$text,
          'summary'=>$summary
        );
        $this->pushOrderMessage($ordercode,$msg);
        //=======================================
        $this->success(L('RESULT_SUCCESS'),url('Admin/Orders/deliverGoods'));
      }else{
        $this->error($result['info']);
      }
    }
  }

    /**
     * 单个发货操作
     */
    public function deliverEdit() {

        $result = (new DatatreeLogicV2())->queryNoPaging(['parentid'=>6003]);
        $expresslist = array();
        if(!empty($result)){
            foreach($result as $val){
                $expresslist[$val['code']] = $val['name'];
            }
        }else{
            $this->error("物流公司列表获取失败！");
        }

        if (IS_GET) {
            $id = $this->_param('id',0);
            $map = array('id'=>$id);
            $result = (new OrdersInfoViewLogic())->getInfo($map);
            if($result['status']){
                $this->assign("order",$result['info']);
            }else{
                $this->error("订单信息获取失败！");
            }
      $map = array('order_code'=>$result['info']['order_code']);
      $result = (new OrdersExpressLogic())->getInfo($map);

      if($result['status'] && is_array($result['info'])){
        $this->assign("express",$result['info']);
      }
            $this->assign("expresslist",$expresslist);
            return $this->boye_display();
        } elseif (IS_POST) {

            $expresscode = $this->_param('expresscode','');
            $expressno = $this->_param('expressno','');
            $uid = $this->_param('uid',0);
            $ordercode = $this->_param('ordercode','');
            $orderOfid = $this->_param('orderOfid','');
            if(empty($expresscode) || !isset($expresslist[$expresscode])){
                $this->error("快递信息错误！");
            }
            if(empty($expressno)){
                $this->error("快递单号不能为空");
            }
            $entity = array(
                'expresscode'=>$expresscode,
                'expressname'=>$expresslist[$expresscode],
                'expressno'=>$expressno,
                'note'=>$this->_param('note',''),
                'order_code'=>$ordercode,
                'uid'=>$uid,
            );

            if(empty($entity['order_code'])){
                $this->error("订单编号不能为空");
            }

            $result = (new OrdersExpressLogic())->saveByID($orderOfid,$entity);

            if($result['status']){
                $this->success(L('RESULT_SUCCESS'),url('Admin/Orders/deliverGoods'));
            }else{
                $this->error($result['info']);
            }
        }
    }
  /**
   * 售后管理 - 只看到本店铺的
   */
  public function afterSale(){
    $order_code   = $this->_param('order_code','');
        $nickname     = $this->_param('nickname','');
    $userid       = $this->_param('uid', 0);
    $cs_status    = $this->_param('cs_status',0);


    $params = array();
    $map    = array();
    if ($order_code){
      $map['order_code']    = array('like','%'.$order_code .'%');
      $params['order_code'] = $order_code;
    }
        if ($nickname){
            $map['nickname']    = array('like','%'.$nickname . '%');
            $params['nickname'] = $nickname;
        }
    //本店铺的
    $map['store_uid'] = UID;
    //订单为 售后非初始
    $map['cs_status'] = ['gt',0];
    //订单为 非未支付
    $map['pay_status'] = ['gt',0];
    $params['cs_status'] = $cs_status;


    //地区管理员所属地区限制
    // $AreaMap = $this->OrgAreaMap();
    // if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
    // //$map['wxaccountid'] = getWxAccountID();
    // if(isset($orderStatus)){
    //  $map['order_status']  = $orderStatus;
    //  // $params['order_status'] = $orderStatus;
    // }

    $page  = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
    $order = " create_time desc ";

    if($userid > 0){
//      $r = apiCall(MemberApi::GET_INFO,array(array('uid'=>$userid)));
      $r = (new MemberLogic())->getInfo(['uid'=>$userid]);
      if(!$r['status']) $this->error($r['info']);
      if(empty($r['info']))  $this->error('用户id错误');
      $nickname = $r['info']['nickname'];
      $this->assign('nickname',$nickname);
      $map['uid']    = $userid;
      $params['uid'] = $userid;
    }
    if($cs_status >=0 ) $map['cs_status'] = $cs_status;
    else $map['cs_status'] = array('neq',2);

    //查询
//     dump($map);
//    $result = apiCall(OrderRefundApi::QUERY_WITH_ORDER,array($map, $page, $order, $params));
    $result = (new OrdersReFundLogic())->queryWithOrder($map, $page, $order, $params);
    // dump($result);exit;
    if ($result['status']) {
      $this -> assign('order_code', $order_code);
      $this -> assign('cs_status', $cs_status);
      $this -> assign('uid', $userid);
      $this -> assign('nickname', $nickname );
      $this -> assign('show', $result['info']['show']);
      $this -> assign('list', $result['info']['list']);
      return $this -> boye_display();
    } else {
      Log::record('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
      $this -> error($result['info']);
    }

  }

  // 售后关闭交易 - 终结操作
  public function close(){
    $thid=$this->_param('thid',0);
    $order_code = '';
    $result = apiCall(OrderRefundApi::GET_INFO,array(array('id'=>$thid)));

    if($result['status']){
      if(is_null($result['info'])){
        $this->error('指定售后记录不存在');
      }else{
        $order_code = $result['info']['order_code'];

      }
    }else{
      $this->error('操作失败');
    }
    //修改售后状态
    $r = apiCall(OrderRefundApi::CHANGE_SERVICE_STATUS,array($order_code,\app\src\order\model\Orders::CS_PROCESSED));
    if(!$r['status']) $this->error($r['info']);
    //修改订单 + 推送
    $r = apiCall(OrderStatusApi::CLOSE,array($order_code,false,UID));
    if($r['status']){
      $this -> success($r['info']);
    }else{
      $this -> error($r['info']);
    }

  }

  /**
   * 单个申请退货 - 管道操作
   * 订单 =》ORDER_RETURNED
   */
  public function returnGoods()
  {
    if (IS_GET) {
      $id = $this->_param('id', 0);
      $thid = $this->_param('thid', 0);
      $this->assign("thid", $thid);
      $this->assign("id", $id);
//      $result = apiCall(OrderRefundApi::GET_INFO, array(array('id' => $thid)));
      $result = (new OrdersReFundLogic())->getInfo(['id' => $thid]);
      if ($result['status']) {
        if (is_null($result['info'])) {
//          $this->error('指定售后记录不存在');
        } else {
          $message = $result['info']['reply_msg'];
          $this->assign('message', $message);
        }
      } else {
        $this->error('操作失败');
      }
      return $this->boye_display();
    } else {
      $id = $this->_param('id', 0);
      $thid = $this->_param('thid', 0);
      $order_code = '';

//      $result = apiCall(OrderRefundApi::GET_INFO, array(array('id' => $thid)));
      $result = (new OrdersReFundLogic())->getInfo(['id' => $thid]);
      if ($result['status']) {
        if (is_null($result['info'])) {
          $this->error('指定售后记录不存在');
        } else {
          $valid_status = intval($result['info']['valid_status']);
          if (2 === $valid_status) $this->error('此售后已被驳回');
          $order_code = $result['info']['order_code'];

        }
      } else {
        $this->error('操作失败');
      }

      $entity = array(
        'valid_status' => 1,
        'reply_msg'    => $this->_param('note', '无'),
        'order_code'   => $order_code,
      );

//      $result = apiCall(OrderRefundApi::SAVE_BY_ID, array($thid, $entity));
      $result = (new OrdersReFundLogic())->saveByID($thid, $entity);
      if ($result['status']) {
        //修改订单状态为已退货
//        apiCall(OrderRefundApi::CHANGE_ORDER_STATUS, array($order_code, \app\src\order\model\Orders::ORDER_RETURNED));
                (new OrdersReFundLogic())->changeOrderStatus($order_code, \app\src\order\model\Orders::ORDER_RETURNED);
        // 推送订单消息
        $text = "亲，您订单($order_code)的退货申请已通过";
        $msg = array(
          'title'=>'退货申请通知',
          'content'=>$text,
          'summary'=>'退货申请已通过'
        );
        $this->pushOrderMessage($order_code,$msg);

        $this->success("操作成功！",U('Admin/Orders/afterSale'));
      } else {
        $this->error('操作失败');
      }
    }
  }

  /**
   * 单个申请退款 - 管道操作
   */
  public function refund(){
    if(IS_GET){
      $id   = $this->_param('id',0,'int');
      $thid = $this->_param('thid',0,'int');
      $this->assign("thid",$thid);
      $this->assign("id",$id);
      $result = apiCall(OrderRefundApi::GET_INFO,array(array('id'=>$thid)));
      if($result['status']){
        if(is_null($result['info'])){
          $this->error('指定售后记录不存在');
        }else{
          $message = $result['info']['reply_msg'];
          $this->assign('message',$message);
        }
      }else{
        $this->error('操作失败');
      }
      return $this->boye_display();
    }else{
      $id    = $this->_param('id',0,'int');
      $thid  = $this->_param('thid',0,'int'); //售后记录ID
      $money = $this->_param('money',0,'float');
      $reply_msg  = $this->_param('note','');
      //数据检查
      if($money<0) $this->error('退款金额须为正数');
      $r = apiCall(OrderRefundApi::GET_INFO,array(array('id'=>$thid)));
      if(!$r['status'])       $this->error($r['info']);
      if(is_null($r['info'])) $this->error('指定售后记录不存在','',1);
      $valid_status = intval($r['info']['valid_status']);
      if(2 === $valid_status) $this->error('此售后已被驳回');
      //操作
      $r = apiCall(OrderStatusApi::REFUNDED,array($r['info']['order_code'],0,$thid,$reply_msg,$money));
      if(!$r['status']) $this->error($r['info']);
      else $this->success($r['info'],U('Admin/Orders/afterSale'));
    }

  }

  /*
   * 修改订单信息
   * */
  public function editorder(){
    if(IS_GET){
      $id = $this->_param('id',0);
      $price = $this->_param('price',0);
      $post_price = $this->_param('postprice','0.00');
      $this->assign("id",$id);
      $this->assign("price",$price);
      $this->assign('post_price',$post_price);

      //查询地址
      $result = apiCall(OrdersInfoViewApi::GET_INFO,array(array('id'=>$id)));

      if($result['status']){
        $this->assign('contactinfo',$result['info']);
      }

      return $this->boye_display();
    }else{
      $id = $this->_param('id',0);
      $price = $this->_param('price',0);
      $post_price = $this->_param('postprice','0.00');
      $entity=array('price'=>$price,'post_price'=>$post_price);
      $resu=apiCall(OrdersApi::SAVE_BY_ID,array($id,$entity));

      $result = apiCall(OrdersApi::GET_INFO,array(array('id'=>$id)));
      $order_code = $result['info']['order_code'];

      //保存邮寄地址
      $entity = array(
        'contactname'=>I('post.contactname',''),
        'province'=>I('post.province',''),
        'city'=>I('post.city',''),
        'area'=>I('post.area',''),
        'mobile'=>I('mobile',''),
        'postal_code'=>I('post.code',''),
        'detailinfo'=>I('post.detailinfo',''),
      );

      $result = apiCall(OrdersContactInfoApi::SAVE,array(array('order_code'=>$order_code),$entity));
      if(!$result['status']){
        $this->error($result['info']);
      }

      if($resu['status']){

        //===========推送给用户消息↓
        $result = apiCall(OrdersApi::GET_INFO,array(array('id'=>$id)));
        if($result['status']){
          $order_code = $result['info']['order_code'];
          $contactinfo = "邮寄地址：".$entity['province'].$entity['city'].$entity['area']."，联系人：".$entity['contactname']."，联系电话：".$entity['mobile'];
          $text = "亲，您订单($order_code)的订单信息已被修改，订单金额:$price 元，运费:$post_price 元，$contactinfo";
          $msg = array(
            'title'   =>'订单信息修改通知',
            'content' =>$text,
            'summary' =>'订单信息被修改'
          );
          $this->pushOrderMessage($order_code,$msg);
          //===========推送给用户消息↑
        }

        //$this->success("操作成功！",U('Admin/Orders/sure'));
      }else{
        $this->error($result['info']);
      }
    }
  }

  /**
   * 驳回售后申请 - 售后终结操作
   */
  public function afterSalebh()
  {
    if (IS_GET) {
      $id = $this->_param('id', 0);
      $thid = $this->_param('thid', 0);
      $this->assign("thid", $thid);
      $this->assign("id", $id);
      return $this->boye_display();
    } else {
      $id = $this->_param('id', 0);
      $thid = $this->_param('thid', 0);
      $reply_msg = $this->_param('note', '无');
      //检查订单状态
      $result = apiCall(OrderRefundApi::GET_INFO, array(array('id' => $thid)));
      if (!$result['status']) $this->error($result['info']);
      $refund_info = $result['info'];
      if (empty($refund_info)) $this->error('售后id错误');
      $order_code = $refund_info['order_code'];

      $valid_status = intval($refund_info['valid_status']);
      if (1 === $valid_status) $this->error('不能驳回,已在售后中');
      $entity = array('valid_status' => 2, 'reply_msg' => $reply_msg);
      $resu = apiCall(OrderRefundApi::SAVE_BY_ID, array($thid, $entity));
      if ($resu['status']) {
        //===========推送给用户消息↓
        $text = "亲，您订单($order_code)的售后申请已经被驳回，理由：$reply_msg";
        // 推送订单消息
        $msg = array(
          'title'=>'售后申请通知',
          'content'=>$text,
          'summary'=>'售后申请被驳回'
        );
        $this->pushOrderMessage($order_code,$msg);

        $this->success("操作成功！",U('Admin/Orders/afterSale'));
      } else {
        $this->error($result['info']);
      }
    }
  }

  public function verify(){

    if(IS_POST){

      $ver_pass = $this->_param('ver_pass','');

      if(APP_DEBUG === true){
        //测试模式下不输入密码
        $this -> success(true);
      }
      $result = apiCall(UserApi::GET_INFO,array(UID));
      if($result['status']){
        if($result['info']['password'] == itboye_ucenter_md5($ver_pass,UC_AUTH_KEY)){
          $this -> success(true);
        }else{
          $this -> error('用户名或密码错误');
        }
      }else{
        $this -> error($result['info']);
      }
    }

  }


  /**
   * 批量发货
   * TODO:批量发货
   */
  public function bulkDeliver(){
    $this->error("功能开发中...");
  }

  /**
   * 批量确认订单
   */
  public function bulkSure() {
    if (IS_POST) {

      $order_codes = $this->_param('order_codes', -1);
      if ($order_codes === -1) {
        $this -> error(L('ERR_PARAMETERS'));
      }


      foreach($order_codes as $code){
        $result = apiCall(OrderStatusApi::CONFIRM_ORDER, array($code , false , UID));
        if (!$result['status']) {
          $this -> error($result['info']);
        }else{

          //===========推送给用户消息↓
          $text = "亲，您的订单已被确认,请等待发货，订单号($code)";
          $msg = array(
            'title'=>'订单通知',
            'content'=>$text,
            'summary'=>'订单已确认'
          );
          $this->pushOrderMessage($code,$msg);
          //===========推送给用户消息↑

        }
      }


      $this -> success(L('RESULT_SUCCESS'), U('Admin/Orders/sure'));

    }
  }


  private function sendTextTo($uid,$content){

    Hook::listen("");

  }

  /**
   * 获取评论附件
   * @param  int   $cid  评论ID
   * @return [type]      [description]
   */
  private function getAttachs($cid,$img=3,&$arr){
    $map = ['comment_id'=>$cid];
    $result = apiCall(OrderCommentAttachsApi::QUERY_NO_PAGING,[$map,false,['pic_id']]);
    if($result['status']){
      $arr['img'] = [];
      foreach($result['info'] as $key=>$v){
        if($key >$img) break;
        array_push($arr['img'],$v['pic_id']);
      }
    }
  }
  /**
   * 消息推送
   */
  private function pushMessage($msg_type,$message=array('title'=>' ','content'=>' ','summary'=>' ','extra'=>''),$uid=0,$after_open=false,$pushAll=false){
//    $result = apiCall(MessageApi::PUSH_MESSAGE_WITH_TYPE,array($msg_type,$message,$uid,$pushAll,$after_open));
    $result = (new MessageLogic())->pushMessageAndRecordWithType($msg_type,$message,$uid,$pushAll,$after_open);
    return $result['status'];
  }

  /**
   * 推送订单消息
   */
  private function pushOrderMessage($order_code,$msg=array('title'=>'订单通知','content'=>'订单通知内容','summary'=>'订单通知摘要')){
    //推送订单确认消息
//    $result = apiCall(OrdersApi::GET_INFO,array(array('order_code'=>$order_code)));
    $result = (new OrdersLogic())->getInfo(['order_code'=>$order_code]);
    if($result['status'] && !is_null($result['info'])){
      $uid= $result['info']['uid'];

//      $result = apiCall(OrdersItemApi::GET_INFO,array(array('order_code'=>$order_code)));
      $result = (new OrdersItemLogic())->getInfo(['order_code'=>$order_code]);
      if($result['status']){
        $img = $result['info']['img'];
      }
      $extra = json_encode(array('order_code'=>$order_code,'img'=>$img));
      $after_open = array('type'=>'go_activity','param'=>\app\src\message\model\Message::MESSAGE_ORDER_ACTIVITY,'extra'=>array('order_code'=>$order_code));
      $result = $this->pushMessage(\app\src\message\model\Message::MESSAGE_ORDER,$message=array('title'=>$msg['title'],'content'=>$msg['content'],'summary'=>$msg['summary'],'extra'=>$extra),$uid,$after_open);
    }
  }

  /**
   * 地区管理员地区集map
   */
  private function OrgAreaMap(){

    $permisson = action('OrgArea/check_manager_permisson',array(),'Widget');
    $this->assign('permisson',$permisson);
    $AreaMap = array();
    if($permisson != 7) return $AreaMap;//不是地区管理员
    $cityid = array();
    $map['type'] = 1;
    $map['member_uid'] = UID;
//    $result = apiCall(OrgMemberApi::QUERY_NO_PAGING,array($map));
    $result = (new OrgMemberLogic())->queryNoPaging($map);
    if($result['status']){
      foreach($result['info'] as $val){
        array_push($cityid,$val['organization_id']);
      }
    }

    $provinceid = array();
    $map['type'] = 0;
//    $result = apiCall(OrgMemberApi::QUERY_NO_PAGING,array($map));
    $result = (new OrgMemberLogic())->queryNoPaging($map);
    if($result['status']){
      foreach($result['info'] as $val){
        array_push($provinceid,$val['organization_id']);
      }

    }
    $cityid = implode(',',$cityid);
    $provinceid = implode(',',$provinceid);

    $city = array(-1);
    $province = array(-1);
    //查询地区名称
    if(!empty($cityid)){
      $map = "cityid in($cityid)";
      $filed = 'city';
//      $result = apiCall(CityApi::QUERY_NO_PAGING,array($map,false,$filed));
      $result = (new CityLogic())->queryNoPaging($map,false,$filed);
      if($result['status']) $city = array_column($result['info'],'city');
    }

    if(!empty($provinceid)){
      $map = "provinceid in($provinceid)";
      $filed = 'province';
//      $result = apiCall(ProvinceApi::QUERY_NO_PAGING,array($map,false,$filed));
      $result = (new ProvinceLogic())->queryNoPaging($map,false,$filed);
      if($result['status']) $province = array_column($result['info'],'province');
    }
    $AreaMap = array(
      'city' => array('in',$city),
      'province' => array('in',$province),
      '_logic' => 'or'
    );
    return $AreaMap;

  }


}
