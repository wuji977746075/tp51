<?php
namespace src\cms;
use src\base\BaseLogic;

class CmsPostExtraLogic extends BaseLogic{

  function getContentScws($content,$l=99){
    return scws($content,99);
  }
}