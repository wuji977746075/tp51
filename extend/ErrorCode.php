<?php

class ErrorCode {
  const ERROR    = 9999; //错误
  const DB_ERROR = 9000; //数据库错误
  const MODEL_ERROR = 9001; //模型错误
  //--------------- 系统错误
  const LACK_PARA = 1000; //缺少参数
  const NOT_FOUND_RESOURCE = 1002;  //404请求资源不存在
  const INVALID_PARA = 1003;  //无效\非法参数
  const BUSINESS_ERROR = 1004;  //业务错误

  const API_NEED_UPDATE = 1005;  //接口需要同步、升级
  const API_EXCEPTION = 1006;  //发生异常
  const API_NO_AUTH = 1007;  //接口无权限
  const API_NEED_LOGIN = 1111;  //需要登录


  //---------------- Logic里的异常 2000+
  const LOGIC_ERROR  = 2000;


  //---------------- 未定义的模块error - lock 3000+
  const MODULE_ERROR = 3000;
  const LOCK_ERROR   = 3100;

}