<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-08-20 16:52:48
 * Description : [分佣系统 - 淘宝客账号模型]
 */

namespace src\fy\account;
use think\Model;

class FyAccount extends Model
{
  protected $table = "f_fy_account";
  protected $autoWriteTimestamp = true;
}