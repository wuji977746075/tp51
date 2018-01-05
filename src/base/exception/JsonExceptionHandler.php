<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 15:05
 */

namespace app\src\base\exception;

use Exception;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;

class JsonExceptionHandler extends Handle
{
    public function render(\Exception $e)
    {

      // 参数验证错误
      if ($e instanceof ValidateException) {
          return json(['msg'=> $e->getError(),'code'=>422]);
      }


      // 请求异常
      if ($e instanceof HttpException && request()->isAjax()) {
          return response($e->getMessage(), $e->getStatusCode());
      }
      // 其他错误交给系统处理
      return parent::render($e);
    }

}