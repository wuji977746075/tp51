<?php
namespace src\lottery\lottery;
use src\base\BaseModel as Model;

class Lottery extends Model {
  protected $table = "f_lottery";
  protected $autoWriteTimestamp = true;
}