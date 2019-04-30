<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-12-24 15:44:13
 * Description : [Description]
 */

namespace src\base\traits;
use think\Db;

trait Trans {

  protected $trans = 0;
  protected function trans() {
    $this->trans = intval($this->trans) + 1;
    Db::startTrans();
  }
  // ajaxRet : $this->trans >0
  // suc err opSuc opErr 等ajax操作自动提交
  protected function commit() {
    $this->trans = intval($this->trans) - 1;
    // $this->trans = 0 ;
    Db::commit();
  }
  // ajaxRet : $this->trans >0
  // suc err opSuc opErr 等ajax操作自动提交
  protected function rollback() {
    $this->trans = intval($this->trans) - 1;
    // $this->trans = 0 ;
    Db::rollback();
  }
  // 待废弃 : => rollback
  // suc err opSuc opErr 等ajax操作自动提交
  protected function back() {
    $this->rollback();
  }
}