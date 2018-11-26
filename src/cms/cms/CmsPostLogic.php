<?php
namespace src\cms\cms;
use src\base\BaseLogic;

class CmsPostLogic extends BaseLogic{
  // 过滤 str
  function filter($s,$replace='*'){
    $ws = explode(' ',getConfig('filter_words'));
    $s = str_replace_all($ws,$replace,$s);
    return $s;
  }
  // check title
  function checkTitle($t,$id=0){
    $t = $this->filter($t);
    $map = ['title'=>$t,'id'=>['neq',$id]];
    $this->getField($map,'id') && throws('title exist');
    return  $t;
  }


  function queryCountWithUser($map = null, $page = false, $order = false, $params = false, $fields = 'p.*,u.name as author_name') {
    empty($page) && $page = ['page'=>1,'size'=>10];
    $query = $this->getModel()->alias('p')->join(['f_user'=>'u'],'p.author = u.id','left');
    if(!empty($map)) $query = $query->where($map);
    if(false !== $order) $query = $query->order($order);
    if(false !== $fields) $query = $query->field($fields);

    $start = isset($page['start']) ? $page['start'] : max(0,(intval($page['page'])-1)*intval($page['size']));
    $list = $query -> limit($start,$page['size']) -> select();
    $count = $this -> getModel() ->alias('p')->join(['f_user'=>'u'],'p.author = u.id','left')-> where($map) -> count();
    return ["count" => $count, "list" => $list->toArray()];
  }
}