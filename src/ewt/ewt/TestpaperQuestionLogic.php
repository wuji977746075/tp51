<?php
/**
 * @author rainbow 2017-03-25 17:03:01
 */
namespace src\ewt\ewt;

use src\base\BaseLogic;
// use app\src\ewt\model\TestpaperQuestion;
// use think\Db;
// use app\src\extend\Page;

class TestpaperQuestionLogic extends BaseLogic{

    //根据测试id 获取音频ids
    public function getAudioIdsByPaperId($id=0){
        $model = $this->getModel();
        $map = ['p.test_id'=>$id,'q.audio_id'=>['neq',0]];
        $r = $model->alias('p')->join(['itboye_ewe_question'=>'q'],'q.id=p.question_id or q.parent_id = p.question_id','left')->
        where($map)->field('distinct(q.audio_id)')->order('q.audio_id asc')->select();
        $ids = [];
        foreach ($r as $v) {
          $ids[] = $v['audio_id'];
        }
        return array_unique($ids);
    }

    private function getQuery(){

        $query = Db::table("itboye_ewe_testpaper_question")->alias("tq")
            ->field("tq.sort,que.question,que.title,tq.id as id,que.dt_type,que.note,tq.dt_part,tq.score")
            ->join(["itboye_ewe_question"=>"que"],"que.id = tq.question_id","LEFT");
        return $query;
    }

    public function queryWithPagingHtml($map = null, $page = ['curpage' => 1, 'size' => 10], $order = false, $params = false, $fields = false)
    {
        $query = $this->getQuery();
        if(!is_null($map)) $query = $query->where($map);
        if(false !== $order) $query = $query->order($order);
        $start = max(intval($page['curpage'])-1,0)*intval($page['size']);
        $list = $query -> limit($start,$page['size']) -> select();
        $query = $this->getQuery();
        $count = $query -> where($map) -> count();

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
        return (["count"=>$count, "list" => $list ,"show" => $show]);
    }
}