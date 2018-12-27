<?php

namespace src\admin\menu;
use src\base\BaseModel as Model;

class Menu extends Model {
  protected $table = "f_sys_menu";
  protected $autoWriteTimestamp = true;
  // protected $auto = ['name', 'ip'];
  protected $insert = ['status' => 1];
  // protected $update = [];
  // protected function setNameAttr($value)
  // {
  //     return strtolower($value);
  // }

  // protected function setIpAttr()
  // {
  //     return request()->ip();
  // }
  // protected $type = [
  //   'status'    =>  'integer',
  //   'score'     =>  'float',
  //   'birthday'  =>  'datetime',
  //   'info'      =>  'array',
  // ];
}