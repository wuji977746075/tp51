<?php
namespace src\cms\cate;

use src\base\BaseModel as Model;

class CmsCate extends Model {
  protected $table = "f_cms_cate";
  protected $autoWriteTimestamp = true;
  protected $insert = ['status' => 1];
}