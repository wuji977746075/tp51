<?php
namespace src\user\wallet;
use src\base\BaseModel as Model;

class UserWallet extends Model{
  protected $table = "f_user_wallet";
  protected $autoWriteTimestamp = true;
}