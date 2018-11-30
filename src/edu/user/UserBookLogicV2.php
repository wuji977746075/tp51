<?php
/*rainbow 2017-03-20 16:17:31*/
namespace app\src\ewt\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\ewt\model\UserBook;
use app\src\extend\Page;

class UserBookLogicV2 extends BaseLogicV2{

  public function getExpireTime($uid=0,$id=0){
    if($uid && $id){
      $r = $this->getInfo(['uid'=>$uid,'book_id'=>$id,'expire_time'=>['>',time()]],'expire_time desc','expire_time');
      return $r ? (int)$r['expire_time'] : 0;
    }else{
      return 0;
    }
  }


  public function getUserBooks($map = null, $page = ['curpage'=>1,'size'=>10], $order = false, $params = false, $fields = false) {
    $type = 6015;
    $model = $this->getModel();
    $query = $model->alias('b')
    ->join(['itboye_product'=>'p'],'p.id = b.book_id','left')
    ->join(['itboye_product_image'=>'img'],'img.pid = b.book_id and img.type = '.$type,'left');
    if(!is_null($map))    $query = $query->where($map);
    if(false !== $order)  $query = $query->order($order);
    if(false !== $fields) $query = $query->field($fields);
    $start = max(intval($page['curpage'])-1,0)*intval($page['size']);
    $list = $query -> limit($start,$page['size']) -> select();

    $count = $this -> getModel()->alias('b')
    ->join(['itboye_product'=>'p'],'p.id = b.book_id','left')
    ->join(['itboye_product_image'=>'img'],'img.pid = b.book_id and img.type = '.$type,'left')-> where($map) -> count();

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
    return returnSuc(["count" => $count, "list" => $data, "show"=>$show]);
  }
}