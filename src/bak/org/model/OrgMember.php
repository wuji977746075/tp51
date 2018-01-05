<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-01-18 16:27:15
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
 * @package app\
 * @example
 */
class OrgMember extends Model {
  protected $table = 'common_org_member';
  protected $auto = ['update_time'];
  protected $insert = ['create_time','is_admin'=>0];

  protected function setUpdateTimeAttr(){
    return time();
  }
  protected function setCreateTimeAttr(){
    return time();
  }
}