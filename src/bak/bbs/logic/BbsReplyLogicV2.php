<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-06-30 15:10:22
 * Description : [bbs Logic V2]
 */

namespace app\src\bbs\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\bbs\model\BbsReply;

class BbsReplyLogicV2 extends BaseLogicV2{

  protected function _init(){
    $this->setModel(new BbsReply());
  }

  // [回复总数,直接回复数] or 回复下的次级回复数
  public function countReply($pid=0,$rid=0){
    return $rid ? $this->count(['pid'=>$pid,'rid'=>$rid]) : [$this->count(['pid'=>$pid]),$this->count(['pid'=>$pid,'rid'=>0])];
  }

  // 获取帖子或回复的下级回复列表
  public function getReply($pid=0,$rid=0,$p=1,$size=10,$order='create_time desc'){
    $r = $this->queryWithPagingHtml(['pid'=>$pid,'rid'=>$rid],['curpage'=>$p,'size'=>$size],$order,[],'id,pid,rid,to_uid,uid,create_time,content');

    $list  = $r['list'];
    $count = $r['count'];
    foreach ($list as &$v) {
      $v['uname'] = get_nickname($v['uid']);
      $v['to_uname'] = get_nickname($v['to_uid']);
      // img
      $r = (new BbsAttachLogicV2)->query(['pid'=>$pid,'rid'=>$v['id']],['curpage'=>1,'size'=>BbsLogicV2::MAX_REPLY_IMG],false,false,'img');
      $v['img'] = array_keys(changeArrayKey($r['list'],'img'));
      // 时间转换
      $v['create_time_desc'] = getDateDesc($v['create_time'],'Y-m-d H:i:s');
    } unset($v);
    return ['list'=>$list,'count'=>$count];
  }
}