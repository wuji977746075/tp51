<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-05-12 11:12:30
 * Description : [Description]
 */

namespace app\admin\controller;
// use

class CmsPost extends CheckLogin {
  protected $banEditFields = ['uid','id'];

  public function set(){
    $this->jsf = array_merge($this->jsf,[
      'uid'     => '作者',
      'excerpt' => '摘要',
    ]);
    if(IS_GET){ // view
    }else{ // save
      $this->jsf_field = ['title,uid,category,main_img,excerpt,content','desc'];
      // 分词信息
      // keywords
      // save to post
      // save to post_extra
    }
    return parent::set();
  }
}