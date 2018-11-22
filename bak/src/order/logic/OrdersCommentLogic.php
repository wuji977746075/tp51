<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-09
 * Time: 20:54
 */

namespace app\src\order\logic;

use app\src\base\logic\BaseLogic;
use app\src\order\model\OrdersComment;
use app\src\extend\Page;
class OrdersCommentLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new OrdersComment());
    }
    public function queryWithUser($map = null, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false, $fields = false) {
        $query = $this->getModel()->alias('c')->join('left join common_member as m on m.uid=c.user_id');
        if(!is_null($map)) $query = $query->where($map);
        if(false !== $order) $query = $query->order($order);
        if(false !== $fields) $query = $query->field($fields);
        $list = $query -> page($page['curpage'] . ',' . $page['size']) -> select();
        if (false === $list) return $this -> apiReturnErr($this ->getModel() -> getDbError());

        $count = $this -> getModel()->alias('c') -> where($map) -> count();
        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);

        //分页跳转的时候保证查询条件
        if (false !== $params) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }
        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        return $this -> apiReturnSuc(array("show" => $show, "list" => $list));
    }
}