<?php
namespace src\cms;
use src\base\BaseLogic;

class CmsPostLogic extends BaseLogic{

  // 过滤 str
  function filter($s,$replace='*'){
    return $s;
  }
  // check title
  function checkTitle($t,$flag=false){
    return $t;
  }
}