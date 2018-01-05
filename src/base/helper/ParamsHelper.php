<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-19
 * Time: 17:44
 */

namespace app\src\base\helper;


use think\Model;

class ParamsHelper
{


    /**
     * |后为类型，暂支持int，用于返回默认值0
     * @param array $fields ["username","uid|int"]
     * @param $params
     * @param Model $model
     * @return
     */
    public static function setModelAttr($fields,$params,Model $model){
        foreach ($fields as $vo){
            if(is_array($vo)){
                foreach ($vo as $k=>$v){
                    $model->setAttr($v,self::_param($k,$params));
                }
            }else{
                if(is_string($vo) && strpos($vo,"|") !== false){
                    list($key,$type) = explode("|",$vo);
                    $model->setAttr($key,self::_param($vo,$params));
                }else{
                    $model->setAttr($vo,self::_param($vo,$params));
                }
            }
        }
        return $model;
    }

    private static function _param($p,$params){

        if(strpos($p,"|") !== false){
            list($key,$type) = explode("|",$p);
        }else{
            $key = $p;
            $type = "string";
        }

        if(isset($params[$key])){
            return $params[$key];
        }

        if($type == "int"){
            return 0;
        }

        return "";
    }
}