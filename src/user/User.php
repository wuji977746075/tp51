<?php
namespace src\user;
use think\Model;

class User extends Model
{
  protected $table = "f_user";

  public function extra(){
    return $this->hasOne('UserExtra','uid','id');
  }
}