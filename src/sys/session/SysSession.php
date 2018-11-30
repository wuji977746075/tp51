<?php
namespace src\sys\session;
use think\Model;

class SysSession extends Model
{
  protected $table = "f_com_session";

  protected $autoWriteTimestamp = true;
}