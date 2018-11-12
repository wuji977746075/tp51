<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-10-30 11:23:18
 * Description : [Description]
 */

namespace app\index\controller;
// use

class Tool {

  // ajax info
  function info(){
    $ret = [];
    $r = \think\Db::query('select version() as mysql_v;');
    // $r = \think\Db::execute('select version();'); // 1
    //
    //
    $ret['mysql_v']    = ($r && $r[0]) ? $r[0]['mysql_v'] : '';
    $ret['curl']       = function_exists('curl_init') ? 'YES':'NO';
    $ret['time_limit'] = 't300S';
    $ret['cms_v']      = 'RainbowCMS V1.0.0';
    echo json_encode(['code'=>0,'msg'=>'success','info'=>$ret]);
  }
}