<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-22 11:36:57
 * Description : rainbowPHP 安装模块
 */

namespace app\install\controller;
use think\Controller;

// require :
//  a running null mysql database
//  right dir
class Index extends Controller {
  public function __construct(){
    $this->check();
  }

  private function check(){
    // check install.lock
    if(file_exists('../install.lock')){
      throws('install already');
    }
    // check right
  }

  private function checkDb($config=[]){
    if($config){

      // db config pass,name,pre
      // check db
    }
    throws('check db error');
  }

  // setting
  function index() {
    echo 'install... !';
  }

  // install
  function install(){
    $db_config = [
      'name' =>'fly',
      'user' =>'root',
      'pass' =>'1',
      'table_pre' =>'f_',
    ];
    $this->checkDb($db_config);
    // copy file
    //   copy to config file
    // run  sql
    // sql insert
    //   sql insert : admin(name,pass)+db(pre)
    // remove {root:tp51}/install
    // add install.lock
    // clear
  }
}