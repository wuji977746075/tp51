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
  private $db    = null;
  private $force = true; // 强制安装:不判断是否安装过
  // init
  function initialize(){
    $this->check();
  }

  // setting
  function index() {
    return $this->fetch();
  }

  // install
  function install(){
    $config = [
      'name'      => input('name','fly'),
      'user'      => input('user','root'),
      'pass'      => input('pass','1'),
      'table_pre' => input('table_pre','f_'),
    ];
    $this->checkDb($config);
    // copy file
    //   copy to config file
    // run  sql
    // sql insert
    //   sql insert : admin(name,pass)+db(pre)
    // remove {root:tp51}/install
    // add install.lock
    // clear
    mysqli_close($this->db);
    return 'install ok ... !';

  }


  function _empty(){
    throws('非法访问 !');
  }
  private function check(){
    $root =  APP_PATH.'..';
    // check install.lock
    $lock = $root.'/install.lock';
    $install = $root.'install';
    if(!$this->force && file_exists($lock)){
      throws('install already');
    }
    // check right
    $root_w = is_writable($root) ? 1:0;
    $this->assign('root_w',$root_w);
    $php56  = version_compare(PHP_VERSION,'5.6.0','>=') >=0 ? 1:0;
    $php_mysqli = function_exists('mysqli_connect') ? 1:0;
    $this->assign('php_v',$php56);
    $this->assign('php_mysqli',$php_mysqli);
    // $this->assign('tp_v',1);
    // if(!$root_w){
    //    throws('根目录 is not writeable');
    // }
  }

  private function sql($sql=''){
    if($sql){
      return $this->db->excute($sql);
    }
    throws('invalid sql');
  }
  private function checkDb($config=[]){
    if($config){
      extract($config);
      // db config pass,name,pre
      // check db
      $con = mysqli_connect($host,$user,$pass,$name);
      if (!$con) {
        throws('mysqli error('
          .mysqli_connect_errno().'):'.mysqli_connect_error()
        );
      }
      mysqli_set_charset($con,'utf8');
      // mysqli_execute('use '.$name.';');
      $this->db = $con;
    }
    throws('check db error');
  }

}