<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-08-28 10:40:00
 * Description : [分佣系统 - 系统账户与淘宝客账号关联模型]
 */

namespace src\fy\account;
use think\Model;

class FyAccountUser extends Model
{
  protected $table = "f_fy_account_user";
  protected $autoWriteTimestamp = true;
}