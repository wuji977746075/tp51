<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-06-30 15:10:22
 * Description : [bbs Logic V2]
 */

namespace app\src\bbs\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\bbs\model\Bbs;
use app\src\bbs\logic\BbsPostLogicV2;
use app\src\bbs\logic\BbsReplyLogicV2;
use app\src\system\logic\ConfigLogic;

class BbsLogicV2 extends BaseLogicV2{
  const DEFAULT_BLOCK   = 1;
  const BANNER_POSITION = 6220;
  const MAX_POST_IMG    = 9;
  const MAX_REPLY_IMG   = 3;

  protected $filter_words;
  protected function _init(){
    $this->setModel(new Bbs());
    $this->filter_words = (new ConfigLogic)->getOneField(['name'=>'BBS_BAN_WORDS'],'value');
  }

  public function checkBlock($tid=0){
    $r = $this->getInfo(['id'=>$tid]);
    if(!$r) return returnErr('论坛配置有误');
    if($r['status'] == 1) return returnErr('论坛锁定中,请稍后再试');
    if($r['status'] ==-1) return returnErr('论坛关闭中,请稍后再试');
    return returnSuc($r);
  }

  // 关键词过滤
  public function filter($str='',$rep='***'){
    return str_replace($this->filter_words, $rep, $str);
  }

  public static function apps($app=''){
    $apps = ['admin','android_driver','android_worker','pc','ios_driver','ios_worker','test','other'];
    if($app) return in_array($app,$apps);
    return $apps;
  }

  //板块的正常下级ids - tid=>直属的/0=>全部
  public function childIds($tid=0,$field='',$time=120){
    $key = 'bbs_child_'.$field.'_'.$tid;
    if($time>0){
      $val = cache($key);
      if($val) return $val;
    }
    $val = $this->queryNoPaging($tid ? ['parent'=>$tid] : []);
    $field && $val = changeArrayKey($val,$field,true);
    if($time>0) cache($key,$val,$time);
    return $val;
  }
  //某板块直属正常帖子数
  public function countPost($tid=0,$time=120){
    $key = 'bbs_post_num_'.$tid;
    if($time>0){
      $val = cache($key);
      if($val) return $val;
    }
    $val = (new BbsPostLogicV2)->count(['status'=>1,'tid'=>$tid]);
    if($time>0) cache($key,$val,$time);
    return $val;
  }

  //某板块直属正常回复数
  public function countReply($tid=0,$time=120){
    $key = 'bbs_reply_num_'.$tid;
    if($time>0){
      $num = cache($key);
      if($num) return $num;
    }
    $num = (new BbsReplyLogicV2)->count(['tid'=>$tid]);
    if($time>0) cache($key,$num,$time);
    return $num;
  }
}