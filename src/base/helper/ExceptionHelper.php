<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-17
 * Time: 9:35
 */

namespace app\src\base\helper;


use think\Exception;

class ExceptionHelper
{
    public static function getErrorString(Exception $ex){

      dump($ex->getCode());
      dump($ex);
      die();
        // return 'file:'.$ex->getFile().';line:'.$ex->getLine().';msg:'.$ex->getMessage();
        // return 'trace:'.$ex->getTraceAsString().'file:'.$ex->getFile().';line:'.$ex->getLine().';msg:'.$ex->getMessage();
    }
}