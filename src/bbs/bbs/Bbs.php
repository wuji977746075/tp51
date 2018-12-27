<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-26 18:03:05
 * Description : [Description]
 */

namespace src\bbs\bbs;

use src\base\BaseModel as Model;

/**
 * 论坛板块模型
 */

class Bbs extends Model{
  /**
   * 自动完成
   * @var array
   */
  protected $autoWriteTimestamp = true; //强制自动时间
  protected $insert = ['status'=>-1];//默认关闭
}
