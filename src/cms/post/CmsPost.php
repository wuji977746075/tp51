<?php
namespace src\cms\post;

use src\base\BaseModel as Model;

class CmsPost extends Model {
  protected $table = "f_cms_post";
  protected $autoWriteTimestamp = true;
  // protected $insert = ['status' => 1];
  public function extra(){
    return $this->hasOne('CmsPostExtra','pid','id');
  }
}