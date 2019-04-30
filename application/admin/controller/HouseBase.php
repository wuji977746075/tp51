<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2019-01-02 13:38:57
 * Description : [Description]
 */

namespace app\admin\controller;
// use

abstract class HouseBase extends CheckLogin {
  protected $model_id = 32;
  function init(){
    $this->checkCity();// CITY CODE
    $this->cfg['theme'] = 'layer';
  }


  protected function checkCity() {
    $code = session('accode');
    if(!$code){
      header('Location: '. url('sysArea/house'));
      session('req_uri',__SELF__);
    }else{
      define('CODE',$code);
      define('CITY',session('acname'));
    }
  }
}