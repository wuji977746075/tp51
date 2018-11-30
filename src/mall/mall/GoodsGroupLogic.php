<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-20
 * Time: 9:29
 */

namespace app\src\goods\logic;


use app\admin\controller\Product;
use app\src\base\helper\ExceptionHelper;
use app\src\base\logic\BaseLogic;
use app\src\extend\Page;
use app\src\goods\model\ProductGroup;
use think\Db;
use think\exception\DbException;

class ProductGroupLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new ProductGroup());
    }

   public function addProduct($params){
        $product_group = $this->_param('product_group', '');
        $group_start_time = $this->_param('group_start_time', '');
        $group_start_time = strtotime($group_start_time);
        $group_end_time = $this->_param('group_end_time', '');
        $group_end_time = strtotime($group_end_time);
        $group = array(
            'product_group' => $product_group,
            'group_start_time' => $group_start_time,
            'group_end_time' => $group_end_time,
        );
        $groupModel = new ProductGroupLogic();
        if($group['product_group'] !== '' && $group['product_group'] !== 0){
            $map = array(
                'p_id' => $pid
            );
            $error = false;
            $result = $groupModel ->delete($map);

            if($result['status'] === false){
                $error = $result['info'];
            }

            if($error === false){
                $entity = array(
                    'start_time' => $group['group_start_time'],
                    'end_time' => $group['group_end_time'],
                    'price' => 0,
                    'p_id' => $pid,
                    'g_id' => $group['product_group'],
                );
                $result = $groupModel -> add($entity);
                if($result['status'] === false){
                    $error = $result['info'];
                }
            }
        }
    }
    public function queryWithProduct($map = null,$page = array('curpage'=>1,'size'=>10),$order = false,$params = false,$field = false){
        $main_img_type = 6015;//主图

        $query = Db::table("itboye_product_group")->alias('g')
            ->join(['itboye_product' => 'p'],'p.id = g.p_id','left')
            ->join(['(select img_id as img,pid from itboye_product_image where type='.$main_img_type.')' => 'image'],'g.p_id=image.pid','left')
            ->join(['(select min(price) as price,product_id from itboye_product_sku group by product_id)' => 'sku'],'g.p_id=sku.product_id','left')
            ->join(['itboye_product_attr'=>'attr'],'g.p_id=attr.pid','left');

        $query = $query -> page($page['curpage'] . ',' . $page['size']);

        if($map !== null){
            $query = $query -> where($map);
        }

        if($order !== false){
            $query = $query -> order($order);
        }

        if($field !== false){
            $query = $query -> field($field);
        }

        $list  = $query -> select();

        $query = Db::table("itboye_product_group")->alias('g')
            ->join(['itboye_product' => 'p'],'p.id = g.p_id','left')
            ->join(['(select img_id as img,pid from itboye_product_image where type='.$main_img_type.')' => 'image'],'g.p_id=image.pid','left')
            ->join(['(select min(price) as price,product_id from itboye_product_sku group by product_id)' => 'sku'],'g.p_id=sku.product_id','left')
            ->join(['itboye_product_attr'=>'attr'],'g.p_id=attr.pid','left');

        if($map){
            $query = $query -> where($map);
        }
        $count = $query -> count();

        $Page = new Page($count, $page['size']);

        //分页跳转的时候保证查询条件
        if ($params !== false ) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        $show = $Page -> show();

        return $this -> apiReturnSuc(array("count" => $count,"show" => $show,"list" => $list));
    }
}