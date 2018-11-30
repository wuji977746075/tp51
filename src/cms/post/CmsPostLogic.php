<?php
namespace src\cms\post;
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
}