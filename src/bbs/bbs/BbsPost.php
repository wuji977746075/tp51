<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-26 18:03:05
 * Description : [Description]
 */

namespace src\bbs\bbs;
use src\base\BaseModel as Model;
/**
 * 论坛帖子模型
 */

class BbsPost extends Model{
  /**
   * 自动完成
   * @var array
   */
  protected $autoWriteTimestamp = true; //强制自动时间
  protected $insert = ['views'=>0]; //浏览量
}
