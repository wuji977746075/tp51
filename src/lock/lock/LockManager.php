<?php
namespace src\lock\lock;
use src\base\BaseModel as Model;

class LockManager extends Model{

	protected $table = 'itboye_locks_manager';
  protected $autoWriteTimestamp = true;
}