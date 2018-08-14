<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-05-12 11:12:30
 * Description : [Description]
 */

namespace app\admin\controller;
use src\cms\CmsCateLogic;
use src\cms\CmsPostExtraLogic;

class CmsPost extends CheckLogin {
  protected $banEditFields = ['author','id'];

  // 获取字符串的分词信息
  function ajax_scws() {
    $kword = urldecode($this->_param('kword','','需要关键词'));
    $size  = max($this->_param('size/d',10),1);
    $arr   = boolval($this->_param('arr',false));
    // $title = '北京大学生喝进口红酒，在北京大学生活区喝进口红酒';
    // $title = '聚知台是一个及时沟通工具';
    $r = scws($kword,$size,$arr);
    $this->suc('','',$r);
  }

  // todo :文章采集 : xx网站 分页规则 抓取文章
  function ajax_grab() {
  }

  // logic ajax page-list
  public function ajax() {
    // $kword = $this->_get('kword',''); // 搜索关键词
    $r = $this->logic->queryCountWithUser($this->where,$this->page,$this->sort);
    $this->checkOp($r);
  }

  function index() {
    return parent::index();
  }

  function set() {
    $this->jsf = array_merge($this->jsf,[
      'cid'          => '分类',
      'content'      => '详情',
      'excerpt'      => '摘要',
      'kwords'       => '关键词',
      'main_img'     => '列表图',
      'status'       => '发布',
      'publish_time' => '发布时间',
    ]);
    if(IS_GET){ // view
      $cates = (new CmsCateLogic)->getAllMenu(false,3);
      $this->jsf_tpl = [
        ['*cid|selects','',$cates],
        ['*title','input-long'],
        ['kwords','input-long'], //默认标题分词
        ['*content|textarea','input-ueditor'],
        ['excerpt|textarea','input-long'], //默认内容前50字
        ['main_img|btimg','',1],
        ['status|radio','','lay-text="发布|草稿"'],
        ['publish_time|time','','Y-m-d H:i:s'],
      ];
// $info =
      return parent::set();
    }else{ // save
      $extra = new CmsPostExtraLogic;
      $paras = $this->_getPara('title,cid,content','excerpt,main_img,kwords,status|0,publish_time,dt_types');
      // check cid/title add时(草稿时publish_time/0 author/UID)
      // todo: editor 分页 图片保存到本地
      $id = $this->id;
      $paras['title'] = $this->logic->checkTitle($this->logic->filter($paras['title']));
      $content        = $this->logic->filter($paras['content']);
      $content_kwords = $extra->getContentScws($content);

      $this->trans();
      $add = $paras;
      unset($add['content']);unset($add['kwords']);
      $add2 = ['content'=>$content,'kwords'=>$paras['kwords'],'content_kwords'=>$content_kwords];
      if($id){  // edit
        $this->logic->save(['id'=>$id],$add);
        (new CmsPostExtraLogic)->save(['pid'=>$id],$add2);
      }else{    // add
        $add['author'] = UID;
        $id = $this->logic->add($add);
        $add2['pid'] = $id;
        (new CmsPostExtraLogic)->add($add2);
      }
      $this->opSuc();
    }
  }
}