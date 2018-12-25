<?php
/**
 * @Author   : rainbow
 * @Email    : hzboye010@163.com
 * @DateTime : 2016-10-31 14:24:48
 * @Description : lock model
 */
namespace src\lock\lock;

use src\base\BaseModel as Model;

class Lock extends Model{
	protected $table = 'itboye_locks';
  protected $autoWriteTimestamp = true;
}