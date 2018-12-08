<?php
namespace src\lottery\lottery;
use think\Model;

class Lottery extends Model {
  protected $table = "f_lottery";
  protected $autoWriteTimestamp = true;
}