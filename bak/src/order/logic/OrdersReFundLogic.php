<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-09
 * Time: 20:54
 */

namespace app\src\order\logic;

use app\src\base\logic\BaseLogic;
use app\src\order\model\OrdersReFund;
use app\src\extend\Page;
class OrdersReFundLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new OrdersReFund());
    }
    public function queryWithOrder($map = null, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false, $fields = false){

        if(!is_null($map))    $query = $this->getModel()->where($map);
        if(false !== $order)  $query = $query->order($order);
        if(false !== $fields) $query = $query->field($fields);

        $list = $query -> page($page['curpage'] . ',' . $page['size']) -> select();
        // dump($model->getLastSql());exit;
        if (false === $list) return $this->apiReturnErr($this->getModel() -> getDbError());

        $count = $this->getModel() -> where($map) -> count();
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
        return $this -> apiReturnSuc(array("show" => $show, "list" => $list));

    }
    /**
     * 修改订单售后状态
     */
    public function changeServerStatus($order_code,$cs_status){

        $map = array(
            'order_code'=>$order_code
        );
        //修改订单的售后状态
        $entity = array(
            'cs_status' => $cs_status
        );
        $result = apiCall(OrdersApi::SAVE,array($map,$entity));
        return $result;
    }
    /**
     * 修改订单状态
     */
    public function changeOrderStatus($order_code,$order_status){

        $map = array(
            'order_code'=>$order_code
        );
        //修改订单的售后状态
        $entity = array(
            'order_status' => $order_status
        );
        $result = apiCall(OrdersApi::SAVE,array($map,$entity));
        return $result;
    }

    public function queryWithCount($map = null, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false, $fields = false) {
        $query = $this->getModel();

        $query->join('__ORDERS__ ON __ORDERS__.order_code =__ORDER_REFUND__.order_code','LEFT');

        if(!is_null($map)){
            $query = $query->where($map);
        }
        if(!($order === false)){
            $query = $query->order($order);
        }
        if(!($fields === false)){
            $query = $query->field($fields);
        }
        $list = $query -> page($page['curpage'] . ',' . $page['size']) -> select();


        if ($list === false) {
            $error = $this->getModel() -> getDbError();
            return $this -> apiReturnErr($error);
        }

        $count = $this->getModel() -> join('__ORDERS__ ON __ORDERS__.order_code =__ORDER_REFUND__.order_code','LEFT') -> where($map) -> count();


        return $this -> apiReturnSuc(array("count" => $count, "list" => $list));
    }
}