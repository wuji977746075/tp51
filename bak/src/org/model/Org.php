<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-01-12 10:47:27
 * Description : [Description]
 */

namespace app\src\org\model;
use think\Model;

/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\src\org\model
 * @example
 */
class Org extends Model {
  protected $table = 'common_org';
  protected $auto = ['update_time'];
  protected $insert = ['create_time'];

  protected function setUpdateTimeAttr(){
    return time();
  }
  protected function setCreateTimeAttr(){
    return time();
  }
}