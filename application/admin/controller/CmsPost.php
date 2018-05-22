<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-05-12 11:12:30
 * Description : [Description]
 */

namespace app\admin\controller;
use src\cms\CmsCateLogic;

class CmsPost extends CheckLogin {
  protected $banEditFields = ['author','id'];

  // todo :获取字符串的分词信息
  function ajaxFenci() {
    $title = '北京大学生喝进口红酒，在北京大学生活区喝进口红酒';
    // $title = '聚知台是一个及时沟通工具';
    $ar = getFenci($title);
    print_r($ar);

  }
  // todo :文章采集
  function ajaxCaiji() {

  }
  function index() {
    return parent::index();
  }

  function set(){
    $this->jsf = array_merge($this->jsf,[
      'cate'    => '分类',
      'content' => '详情',
      'excerpt' => '摘要',
      'kwords'  => '关键词',
      'main_img'=> '列表图',
      'status'  => '发布',
      'publish_time'  => '发布时间',
    ]);
    if(IS_GET){ // view
      $cates = (new CmsCateLogic)->getAllMenu(false,3);
      $this->jsf_tpl = [
        ['*cate|selects','',$cates],
        ['*title','input-long'],
        ['kwords','input-long'], //默认标题分词
        ['*content|textarea','input-long'],
        ['excerpt|textarea','input-long'], //默认内容前50字
        ['main_img|btimg','',1],
        ['status|radio','','lay-text="发布|草稿"'],
        ['publish_time|time','','Y-m-d H:i:s'],
      ];
    }else{ // save
      // todo: editor 分页 图片保存到本地
      $this->jsf_field = ['title,cate,main_img,excerpt,content',',kwords,status|0,publish_time'];
      // check cate add(草稿时publish_time/0 author/UID)
      // save to post
      // content 分词信息
      // save to post_extra
    }
    return parent::set();
  }
}