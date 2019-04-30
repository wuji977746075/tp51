<?php
namespace src\lottery\prize;
use src\base\BaseModel as Model;

class LotteryPrize extends Model {
  protected $table = "f_lottery_prize";
  protected $autoWriteTimestamp = true;
}