<?php
/**
 * @author rainbow 2016-12-15 15:38:20
 */
namespace app\src\area\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\area\model\Area;

class AreaLogicV2 extends BaseLogicV2{

  protected function _init(){
    $this->setModel(new Area());
  }

  public function isExistCode($code='',$field='id'){

    $r = $this->getInfo(['areaID'=>$code],false,$field);
    return $r;
  }
}
