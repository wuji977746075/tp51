<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-28 18:07:21
 * Description : 模型拦截器
 */

namespace src\base;
use think\Model;
use src\base\traits\Jump;
abstract class BaseModel extends Model {
  use Jump;
  // protected $autoWriteTimestamp = false;
}