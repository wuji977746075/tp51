<?php
namespace src\user\wallet;
use think\Model;

class Wallet extends Model
{
  protected $table = "f_user_wallet";
  protected $autoWriteTimestamp = true;
}