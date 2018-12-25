<?php
namespace src\lock\lock;

use src\base\BaseModel as Model;

class LockHis extends Model{
  protected $table = 'itboye_locks_his';
  protected $autoWriteTimestamp = true;
}