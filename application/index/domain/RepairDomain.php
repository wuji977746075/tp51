<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2016-12-14 15:07:50
 * Description : [Description]
 */

namespace app\domain;

use app\src\repair\logic\RepairLogicV2;
use app\src\repair\logic\RepairOrderLogicV2;

/**
 * [司机报修 - app接口 及 管理接口]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\domain
 * @example
 */
class RepairDomain extends BaseDomain {

  /**
   * 报修
   * 101 : req -city -area
   * @Author
   * @DateTime 2016-12-15T17:11:53+0800
   */
  public function add(){
    $this->checkVersion("101");

    $params = $this->parsePost('address,vehicle_type|0|int,mobile,detail,uid|0|int','lng|0|float,lat|0|float,images||mulint,repair_type|0|int');

    $r = (new RepairLogicV2) -> add($params);
    $this->exitWhenError($r,true);
  }
  /**
   * 获取维修依赖
   * @Author
   * @DateTime 2016-12-15T17:11:58+0800
   * @return   [type]                   [description]
   */
  public function rely(){
    $this->checkVersion("100");

    $r = (new RepairLogicV2) -> rely();
    $this->exitWhenError($r,true);
  }

  /**
   * 技工 - 获取可接单列表
   * 101: 添加经纬度排序
   * 102: req -city
   * 102: req +km
   */
  public function query(){
    $this->checkVersion("102");

    $params = $this->parsePost('uid|0|int,lng|0|float,lat|0|float','current_page|1|int,per_page|10|int,order|0|int,km|50|int');

    $r = (new RepairLogicV2) -> unTakeList($params);
    $this->exitWhenError($r,true);
  }
  /**
   * 查询维修列表
   * 101 返回worker_mobile worker_real_name=>worker_realname driver_real_name=>driver_realname
   * @Author
   * @DateTime 2016-12-19T12:14:37+0800
   * @return   [apiReturn] [description]
   */
  public function queryList(){
    $this->checkVersion("101");

    $params = $this->parsePost('uid|0|int,group_id|0|int','current_page|1|int,per_page|10|int,repair_status|-1|int');

    $r = (new RepairLogicV2) -> queryList($params);
    $r['info']['list'] = fixNull($r['info']['list'],'');
    $this->exitWhenError($r,true);
  }

  /**
   * 获取维修中的订单 - 技工或师傅
   * @Author
   * @DateTime 2016-12-19T12:14:37+0800
   * @return   [apiReturn] [description]
   */
  public function current(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int,group_id|0|int','');
    $this->exitWhenError((new RepairLogicV2)->current($params),true);
  }

  /**
   * 技工接单
   * @Author
   * @DateTime 2016-12-19T12:15:22+0800
   * @return   [apiReturn] [description]
   */
  public function take(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int,id|0|int','');
    extract($params);
    $this->exitWhenError((new RepairLogicV2)->take($uid,$id),true);
  }

  //维修 - 技工设置金额 - 可重复调用 - repair_price分 stuff_price分
  public function setPrice(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int,repair_price|0|int,id|0|int','stuff_price|0|int');
    $this->exitWhenError((new RepairLogicV2)->setPrice($params),true);
  }

  //真删除维修 - 未接单
  public function del(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int,id|0|int','');
    $this->exitWhenError((new RepairLogicV2)->del($params));
    $this->apiReturnSuc(L('success'));
  }

  /**
   * 获取支付信息,无订单生成订单 - 司机支付/再支付
   * @Author
   * @DateTime 2016-12-21T15:08:49+0800
   * @return   [apiReturn] [description]
   */
  public function pay(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int,id|0|int,score|0|int','bill_type|0|int,bill_title,bill_code,address_id|0|int');
    $r = (new RepairOrderLogicV2)->pay($params);
    $this->exitWhenError($r,true);
  }

  /**
   * 取消支付
   * @Author
   * @DateTime 2016-12-21T15:59:09+0800
   * @return   [apiReturn]  [description]
   */
  public function unPay(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int,id|0|int','');
    $r = (new RepairOrderLogicV2)->cancel($params);
    $this->exitWhenError($r,true);
  }

  /**
   * 维修完成 - 技工或司机
   * @Author
   * @DateTime 2016-12-26T15:10:52+0800
   * @return   function                 [description]
   */
  public function done(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int,id|0|int,group_id|0|int','evaluate');
    $r = (new RepairLogicV2)->done($params);
    $this->exitWhenError($r,true);
  }
  //取消维修中单子 - 技工或司机
  public function cancel(){
    $this->checkVersion("100");

    $params = $this->parsePost('uid|0|int,id|0|int','');
    extract($params);
  }
  //todo : 订单支付详情
  public function orderDetail(){

  }

}