<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 17:10
 * app 调研
 */

namespace src\ewt\model;

use src\base\BaseModel as Model;

class UserBook extends Model{
  protected $table = "itboye_ewe_user_book";
  protected $autoWriteTimestamp = true;
}