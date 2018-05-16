<?php
namespace src\cms;
use think\Model;

class CmsCate extends Model
{
  protected $table = "f_cms_cate";
  protected $autoWriteTimestamp = true;
  protected $insert = ['status' => 1];
}