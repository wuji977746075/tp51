<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-04
 * Time: 9:40
 */

namespace app\src\banners\logic;

use app\src\banners\model\Banners;
use app\src\base\logic\BaseLogic;
use app\src\extend\Page;
use think\Db;

class BannersLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new Banners());
    }

    public function queryWithPosition($map = null, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false){

        $field = 'pic.uid as puid,banner.uid as buid,pic.path,pic.imgurl,dt.name as position_name,banner.sort,banner.url,banner.notice_time,banner.end_time,banner.start_time,banner.title,banner.create_time,banner.storeid,banner.id,banner.img,banner.position,banner.notes';
        $query = Db::table("itboye_banners")->alias("banner")
            ->field($field)
            ->join(["common_datatree"=>"dt"],'dt.id = banner.position',"LEFT")
            ->join(["itboye_user_picture"=>"pic"],'pic.id = banner.img',"LEFT");

        if(!is_null($map)){
            $query = $query->where($map);
        }

        $list = $query ->order('sort desc')-> page($page['curpage'] . ',' . $page['size']) -> select();
        $query = Db::table("itboye_banners")->alias("banner")
            ->field($field)
            ->join(["common_datatree"=>"dt"],'dt.id = banner.position',"LEFT")
            ->join(["itboye_user_picture"=>"pic"],'pic.id = banner.img',"LEFT");

        $count = $query -> where($map) -> count();
        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);

        //分页跳转的时候保证查询条件
        if ($params !== false && is_array($params)) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        return $this -> apiReturnSuc(array("show" => $show, "list" => $list));
    }

}