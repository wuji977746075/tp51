<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-17
 * Time: 14:46
 */

namespace src\bbs\like;

use src\base\BaseModel as Model;

/**
 * 论坛板块模型
 */

class Like extends Model{
  protected $table = 'itboye_likes';
  /**
   * 自动完成
   * @var array
   */
  protected $autoWriteTimestamp = false; //强制自动时间
  protected $insert = ['create_time'];//默认关闭

  protected function setCreateTimeAttr(){
    return time();
  }
}
