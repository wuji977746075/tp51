<?php

namespace src\file;
use think\Model;

class UserAudio extends Model
{
  protected $table = "f_file_audio";
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