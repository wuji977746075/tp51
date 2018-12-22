<?php

class ErrorCode {
  // 0  success
  // -1 undefined err
  const ERROR = 1; //错误 1-999
  const ERROR_CRYPT        = 10;  //加解密错误
  const LACK_PARA          = 11; //缺少参数
  const NOT_FOUND_RESOURCE = 12;  //404请求资源不存在
  const INVALID_PARA       = 13;  //无效\非法参数

  const ERROR_DB     = 2; //数据库错误

  const ERROR_MODEL  = 3; //模型错误

  const ERROR_MODULE = 4; //模块错误
  const ERROR_API       = 41; // 接口错误
  const ERROR_LOCK      = 4100; // 锁错误
  const API_NEED_UPDATE = 1005;  //接口需要同步、升级
  const API_EXCEPTION   = 1006;  //发生异常
  const API_NO_AUTH     = 1007;  //接口无权限
  const API_NEED_LOGIN  = 1111;  //需要登录

  const ERROR_LOGIC  = 5;

  const ERROR_DOMAIN = 6;

  const ERROR_ACTION = 7;



  // 历史遗留
  const ERROR_BUSINESS  = 14;  //业务错误
}