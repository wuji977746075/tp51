<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2016-12-28 17:33:30
 * Description : [Description]
 */

namespace app\src\wallet\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\extend\Page;
use app\src\wallet\model\WalletOrder;
// use app\src\system\logic\DatatreeLogicV2;
// use think\Db;
// use think\Exception;

/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\
 * @example
 */
class WalletOrderLogicV2 extends BaseLogicV2 {
    const TOBE_PAY = 0;
    const PAYED    = 1;
    //初始化
    protected function _init(){
        $this->setModel(new WalletOrder());
    }

    /**
     * 业务 - 提现账号添加
     * @Author
     * @DateTime 2016-12-29T09:17:51+0800
     * @param    array                    $params [description]
     * @return   [apiReturn]                      [description]
     */

    public function queryWithMobileWithPagingHtml($map = null, $page = ['curpage'=>1,'size'=>10], $order = false, $params = false, $fields = false) {
        $query = $this->getModel();
        $query->alias('wo')->join('__UCENTER_MEMBER__ um', 'um.id = wo.uid');
        if(!is_null($map)) $query = $query->where($map);
        if(false !== $order) $query = $query->order($order);
        if(false !== $fields) $query = $query->field($fields);
        $start = max(intval($page['curpage'])-1,0)*intval($page['size']);
        $list = $query -> limit($start,$page['size']) -> select();

        $count = $this->getModel() ->alias('wo')->join('__UCENTER_MEMBER__ um', 'um.id = wo.uid') -> where($map) -> count();

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
}