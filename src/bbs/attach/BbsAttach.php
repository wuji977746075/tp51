<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-17
 * Time: 14:46
 */

namespace src\bbs\attach;

use src\base\BaseModel as Model;

/**
 * 论坛板块模型
 */

class BbsAttach extends Model{
  /**
   * 自动完成
   * @var array
   */
  protected $autoWriteTimestamp = false; //强制自动时间
}
