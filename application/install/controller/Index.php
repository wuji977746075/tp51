<?php
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

  private function install(){
    // copy file
    //   copy to config file
    // run  sql
    // sql insert
    //   sql insert : admin(name,pass)+db(pre)
    // remove {root:tp51}/install
    //   ok ...
  }

  // install
  function index() {
    $db_config = [
      'name' =>'fly',
      'user' =>'root',
      'pass' =>'1',
      'table_pre' =>'f_',
    ];
    $this->checkDb($db_config);
    $this->install();
    // copy  file + right
    // excute sql
    // add install.lock
    // clear
    echo 'install ok !';
  }
}