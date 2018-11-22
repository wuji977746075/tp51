<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-01-16 13:48:48
 * Description : [Description]
 */

namespace app\src\auth\model;
use think\Model;

/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\
 * @example
 *
 */
class DataObject extends Model{
  protected $table = 'auth_data_object';
  protected $auto  = [];
  protected $insert = ['object_pk'=>'id'];
}