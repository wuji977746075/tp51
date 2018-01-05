<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/4
 * Time: 12:27
 */

namespace app\src\user\logic;


use app\src\base\logic\BaseLogic;
use app\src\extend\Page;
use app\src\user\model\VUserInfo;
use think\Db;

class VUserInfoLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new VUserInfo());
    }

    public function queryWithPagingHtml($map = null, $page = ['curpage'=>1,'size'=>10], $order = false, $params = false, $fields = false) {
        $u_group = isset($map['u_group'])?$map['u_group']: '';

        if(empty($u_group)){
            return parent::queryWithPagingHtml($map,$page,$order,$params,$fields);
        }
        unset($map['u_group']);
        $start = max(intval($page['curpage'])-1,0) * intval($page['size']);
        $query = Db::table("v_user_info")->alias("u")
            ->field("u.*")
            ->join(["common_auth_group_access"=>"aga"],"aga.uid = u.id","left")
            ->where("aga.group_id",$u_group)
            ->order($order)
            ->where($map);

        $list = $query -> limit($start,$page['size']) -> select();
        $count = Db::table("v_user_info")->alias("u")
            ->field("u.*")
            ->join(["common_auth_group_access"=>"aga"],"aga.uid = u.id","left")
            ->where("aga.group_id",$u_group)
            ->where($map) -> count();
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
            }else{
                array_push($data,$vo);
            }
        }
        return $this -> apiReturnSuc(["show" => $show, "list" => $data]);
    }
}