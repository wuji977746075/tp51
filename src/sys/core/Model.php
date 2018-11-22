<?php

namespace src\sys\core;
use think\Model as tpm;

class Model extends tpm
{
  protected $table = "f_sys_model";
  protected $autoWriteTimestamp = true;
}