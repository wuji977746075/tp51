<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2017-07-07 17:45:32
 * Description : [Description]
 */

namespace app\src\bbs\logic;
use app\src\base\logic\BaseLogicV2;
use app\src\bbs\model\Report;

class ReportLogicV2 extends BaseLogicV2 {
  protected function _init(){
    $this->setModel(new Report());
  }

}