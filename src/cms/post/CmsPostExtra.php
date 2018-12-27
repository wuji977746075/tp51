<?php
namespace src\cms\post;

use src\base\BaseModel as Model;
class CmsPostExtra extends Model {
  protected $table = "f_cms_post_extra";
  protected $autoWriteTimestamp = true;
  // protected $insert = ['status' => 1];
}