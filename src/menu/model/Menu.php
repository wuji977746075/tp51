<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-15
 * Time: 10:39
 */

namespace app\src\menu\model;


use think\Model;

class Menu extends Model
{
    protected $table = "common_menu";

    protected $insert = ['status'=>1];
}