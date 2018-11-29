<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-17
 * Time: 14:46
 */

namespace app\src\bbs\bbs;

use think\Model;

/**
 * 论坛板块模型
 */

class BbsReply extends Model{
  /**
   * 自动完成
   * @var array
   */
  protected $autoWriteTimestamp = true; //强制自动时间
}
