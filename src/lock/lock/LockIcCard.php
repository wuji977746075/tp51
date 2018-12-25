<?php
namespace src\lock\lock;

use src\base\BaseModel as Model;
class LockIcCard extends Model{
  protected $table = 'itboye_locks_ic';
  protected $autoWriteTimestamp = true;
}