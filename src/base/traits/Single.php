<?php

namespace src\base\traits;
// 单例
trait Single {
  /**
   * @var ChromePhp
   */
  protected static $_instance;
  /**
   * gets instance of this class
   *
   * @return ChromePhp
   */
  public static function getInstance()
  {
      if (self::$_instance === null) {
          self::$_instance = new self();
      }
      return self::$_instance;
  }
}