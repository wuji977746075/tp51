<?php
namespace src\user\user;
use src\base\BaseModel as Model;

class User extends Model
{
  protected $table = "f_user";

  public function extra(){
    return $this->hasOne('UserExtra','uid','id');
  }
}