<?php

class ErrorCode {
  /**
   * 缺少参数
   */
  const Lack_Para = 1000;
  /**
   * 404请求资源不存在
   */
  const Not_Found_Resource = 1002;

  /**
   * 无效\非法参数
   */
  const Invalid_Para = 1003;

  /**
   * 业务错误
   */
  const Business_Error = 1004;

  /**
   * 接口需要同步、升级
   */
  const Api_Need_Update = 1005;

  /**
   * 发生异常
   */
  const Api_Exception = 1006;


  /**
   * 接口无权限
   */
  const Api_No_Auth = 1007;


  /**
   * 需要登录
   */
  const Api_Need_Login = 1111;

}