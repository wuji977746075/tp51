<?php
/*rainbow 2017-03-20 16:17:31*/
namespace src\ewt\logic;

use src\base\BaseLogic;
use src\ewt\model\SchoolReport;
use think\Db;

class SchoolReportLogic extends BaseLogic{

  public function getInfoWithUnit(array $map,$order=false,$field=false){
    $model = $this->getModel();
    $info = $model->alias('s')
    ->join(['itboye_ewe_bookunit'=>'b'],'s.bookunit_id=b.id','left')
    ->where($map)
    ->order($order)->field($field)
    ->find();
    return $info;
  }

  // 查询答过题的书 + 1/2单元 + max10
  // p.status=1 : 2017-10-16 17:01:24
  public function queryBookDone(array $map,$page=['curpage'=>1,'size'=>10],$order=false,$params=false,$field=false){
    $model = $this->getModel();

    $map['p.status'] = 1;
    $page  = (int) $page['curpage'];
    $size  = (int) $page['size'];
    $start = max(($page-1),0)*$size;
    $list  = Db::table('itboye_product')->alias('p')
    ->join(['itboye_ewe_bookunit'=>'b'],'b.book_id = p.id','left')
    ->join(['itboye_ewe_school_report'=>'s'],'s.bookunit_id = b.id','left')
    ->where($map)->field($field)->limit($start,$size)->group('b.book_id')->select();
    $count = Db::table('itboye_product')->alias('p')
    ->join(['itboye_ewe_bookunit'=>'b'],'b.book_id = p.id','left')
    ->join(['itboye_ewe_school_report'=>'s'],'s.bookunit_id = b.id','left')
    ->where($map)->group('b.book_id')->count();

    //? 查询每本书的1,2级单元
    $l = new BookunitLogicV2;
    foreach ($list as &$v) {
      $v['units'] = $l->queryLevel12(['book_id'=>$v['book_id'],'level'=>['in',[1,2]]],'sort desc,id asc');
    }
    return returnSuc($list);
  }
  //答题列表
  // p.status=1 : 2017-10-16 17:01:24
  public function queryWithUnit(array $map,$pager=['curpage'=>1,'size'=>10],$order,array $params,$field=false){
    $model = $this->getModel();

    $map['p.status'] = 1;
    $page  = (int) $pager['curpage'];
    $size  = (int) $pager['size'];
    $start = max(($page-1),0)*$size;
    $list  = $model->alias('s')
    ->join(['itboye_ewe_bookunit'=>'b'],'s.bookunit_id = b.id','left')
    ->join(['itboye_product'=>'p'],'b.book_id = p.id','left')
    ->where($map)->order($order)->field($field)->limit($start,$size)->select();
    $list  = $list ? obj2Arr($list) : [];
    $count = $model->alias('s')
    ->join(['itboye_ewe_bookunit'=>'b'],'s.bookunit_id = b.id','left')
    ->join(['itboye_product'=>'p'],'b.book_id = p.id','left')
    ->where($map)->count();

    return returnSuc(['list'=>$list,'count'=>$count]);
  }
}