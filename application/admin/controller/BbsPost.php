<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-05-12 11:12:30
 * Description : [Description]
 */

namespace app\admin\controller;
use src\bbs\bbs\BbsLogic;

class BbsPost extends CheckLogin {
  protected $model_id = 23;
  protected $banEditFields = ['uid','id'];
  // 主论坛时拆分content到extra
  // protected $extraFields = ['content','content_kwords','kwords','pid'];

  public function init(){
    $this->cfg['theme'] = 'layer';
  }
  // logic ajax page-list
  public function ajax() {
    $kword = $this->_get('kword',''); // 搜索关键词
    $r = $this->logic->queryCountWithUser($this->where,$this->page,$this->sort);
    $this->checkOp($r);
    return parent::ajax();
  }

  function set() {
    $this->jsf = array_merge($this->jsf,[
      'tid'          => '板块',
      'content'      => '详情',
      'status'       => '发布',
      'special'      => '精华',
      'top'          => '置顶',
      'publish_time' => '发布时间',
    ]);
    if(IS_GET){ // view
      $cates = (new BbsLogic)->queryTree(true);
      $this->jsf_tpl = [
        ['*tid|selects','',$cates],
        ['*title','input-long'],
        ['*content|textarea','input-ueditor'],
        ['special|radio'],
        ['top|radio'],
        ['status|radio','','lay-text="发布|草稿"'],
        ['publish_time|time','','Y-m-d H:i:s'],
      ];
      return parent::set();
    }else{ // save
      $paras = $this->_getPara('title,tid,content','status|0|int,special|0|int,top|0|int,publish_time');
      $id = $this->id;
      $paras['uid']   = UID;
      $paras['title'] = $this->logic->checkTitle($paras['title'],$id);
      $paras['content'] = $this->logic->filter($paras['content']);
      $paras['publish_time'] = strtotime($paras['publish_time']);
      $this->trans();
      $add = $paras;
      if($id){  // edit
        $this->logic->save(['id'=>$id],$add);
      }else{    // add
        $add['uid'] = UID;
        $id = $this->logic->add($add);
      }
      $this->opSuc();
    }
  }
}