<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 11:48
 */

namespace app\admin\controller;


class Cache extends Admin
{

    /**
     * 清除所有缓存
     */
    public function clearAll() {
//        $this->rmdirr(RUNTIME_PATH);
        $this->clearHTML();
        $this->clearTEMP();
        $this->clearData();
        return $this->boye_display();
    }

    /**
     * 应用模板缓存
     */
    public function clearHTML()
    {
        $this->rmdirr(CACHE_PATH);
    }

    public function rmdirr($dirname) {
        if (!file_exists($dirname)) {
            return false;
        }
        if (is_file($dirname) || is_link($dirname)) {
            return unlink($dirname);
        }
        $dir = dir($dirname);
        if ($dir) {
            while (false !== $entry = $dir -> read()) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                //递归
                $this -> rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
            }
        }
        $dir -> close();
        return rmdir($dirname);
    }

    /**
     * 临时，S缓存
     */
    public function clearTEMP()
    {
        $this->rmdirr(TEMP_PATH);
    }

    //处理方法

    /**
     * 缓存
     */
    public function clearData()
    {

        $this->rmdirr(LOG_PATH);
    }

}