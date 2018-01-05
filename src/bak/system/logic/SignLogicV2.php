<?php
/**
 * @author rainbow 2017-07-12 14:59:41
 */
namespace app\src\system\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\system\model\Sign;

class SignLogicV2 extends BaseLogicV2{

  protected function _init(){
    $this->setModel(new Sign());
  }
}