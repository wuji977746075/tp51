<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-10-08 10:07:30
 * Description : [分佣系统 - 拉新数据模型]
 */

namespace src\fy\invite;
use think\Model;

class FyInvite extends Model
{
  protected $table = "f_fy_invite";
  protected $autoWriteTimestamp = true;
}