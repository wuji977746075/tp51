<?php
namespace src\lottery\lottery;
use think\Model;

class LotteryPrize extends Model {
  protected $table = "f_lottery_prize";
  protected $autoWriteTimestamp = true;
}