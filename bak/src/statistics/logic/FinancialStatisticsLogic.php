<?php

/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/3/2
 * Time: 下午4:05
 */

namespace app\src\statistics\logic;

use app\src\base\helper\ExceptionHelper;
use app\src\base\logic\BaseLogic;
use app\src\extend\Page;
use think\Db;
use think\Exception;

class FinancialStatisticsLogic extends BaseLogic
{
    protected function _init()
    {
        // TODO: Implement _init() method.
    }

    /**
     * 结合所有订单分页
     */
    public function queryOrder($_map,  $page = ['curpage'=>1,'size'=>10], $order = false, $params = false, $fields = false ){

        try{
            if($page['curpage']<1) $page['curpage'] = 1;
            $map = '';
            if(!empty($_map)){
                $map = "where $_map and u.status = 1";
            }else{
                $map = "where u.status = 1";
            }
            if($fields===false) $fields = '*';
            $limit = "limit ".($page['curpage']-1) * $page['size'] . ',' . $page['size'];
            if($order!==false) $order = "order by $order";
            $order_sql = "SELECT '商城订单' as `order_type`,`order_code`,`createtime` as `create_time`,`uid`,`pay_type`,`price` as `money`,`pay_code` FROM `itboye_orders` where pay_status=1 and status=1";
            $repair_sql = "SELECT '维修订单' as `order_type`,`order_code`,`create_time`,`uid`,`pay_type`,`money`,`pay_code` FROM `itboye_repair_order` where pay_status=1 and status=1";
            $cz_sql = "SELECT '充值订单' as `order_type`,`order_code`,`create_time`,`uid`,`pay_type`,`money`,`pay_code` FROM `itboye_wallet_order` where pay_status=1 and status=1";
            $join_user = "LEFT JOIN v_user_info as u ON uid = u.id";
            $sql = "SELECT $fields FROM ($order_sql UNION $repair_sql UNION $cz_sql) AS orders $join_user $map $order $limit";
            $count_sql = "SELECT count(*) as `count` FROM ($order_sql UNION $repair_sql UNION $cz_sql) AS orders $join_user $map";
            if(!empty($_map)){
                $map = "where $_map";
            }
            $sum_sql = "SELECT sum(`money`) as `sum` FROM ($order_sql UNION $repair_sql UNION $cz_sql) AS orders $join_user $map";


            $result = Db::query($sql);
            $data = is_null($result) ? [] : $result;

            $result = Db::query($count_sql);
            $count = $result[0]['count'];

            $result = Db::query($sum_sql);
            $sum_money = $result[0]['sum'];

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
            $sum = [
                'money' => $sum_money
            ];

            return $this -> apiReturnSuc(["show" => $show, "list" => $data, "sum" => $sum]);
        }catch (Exception $e){
            return $this->apiReturnErr(ExceptionHelper::getErrorString($e));
        }


    }
}