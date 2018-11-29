<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-26 18:03:05
 * Description : [Description]
 */

namespace src\bbs\ban;
use think\Model;

/**
 * 论坛禁闭模型
 */

class BbsBan extends Model{
  /**
   * 自动完成
   * @var array
   */
  protected $autoWriteTimestamp = true; //强制自动时间
}
