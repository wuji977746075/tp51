<?php
namespace src\cms;
use think\Model;

class CmsPost extends Model
{
  protected $table = "f_cms_post";
  protected $autoWriteTimestamp = true;
  // protected $insert = ['status' => 1];
  public function extra(){
    return $this->hasOne('CmsPostExtra','pid','id');
  }
}