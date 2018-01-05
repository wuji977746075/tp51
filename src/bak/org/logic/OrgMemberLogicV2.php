<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-01-18 16:28:30
 * Description : [Description]
 */

namespace app\src\org\logic;
use app\src\org\model\OrgMember;
use app\src\base\logic\BaseLogicV2;

/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\
 * @example
 */
class OrgMemberLogicV2 extends BaseLogicV2 {

  protected function _init(){
    $this->setModel(new OrgMember());
  }
}