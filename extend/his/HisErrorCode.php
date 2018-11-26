<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/12/3
 * Time: 10:30
 */

namespace his;
use ErrorCode;

class HisErrorCode extends ErrorCode
{
  //4200-4399占位符 用户类错误
  const USER_NEED_IDENTITY = 4201;//'需要实名认证'

  const HIS_ERROR = 4401;// 'his错误'
  const PAX_ERROR = 4601;// 'paymax错误'
}