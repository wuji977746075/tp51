<?php
namespace src\session;
use think\Model;

class Session extends Model
{
  protected $table = "f_com_session";

  protected $autoWriteTimestamp = true;
}