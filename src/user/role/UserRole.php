<?php
namespace src\user\role;
use src\base\BaseModel as Model;

class UserRole extends Model{
  protected $table = "f_user_role";
  protected $autoWriteTimestamp = true;
}