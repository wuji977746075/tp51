<?php
/**
 * @author rainbow 2016-12-15 15:38:20
 */
namespace app\src\city\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\city\model\City;

class CityLogicV2 extends BaseLogicV2{

  protected function _init(){
    $this->setModel(new City());
  }

  public function isExistCode($code='',$field='id'){

    $r = $this->getInfo(['cityID'=>$code],false,$field);
    return $r;
  }
}
