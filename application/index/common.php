<?php

function data_encode($data,$secret,$time){
  $data = base64_encode(json_encode($data).$secret);
  return [
    'data'=>base64_encode($data.$time),
    'time'=>$time
  ];
}

function data_decode($data,$secret,$time){
  $data = preg_replace('/'.$time.'$/', '', base64_decode($data));
  $data = preg_replace('/'.$secret.'$/', '', base64_decode($data));
  return json_decode($data,true);
}


function ret($data){
  \think\Response::create($data, 'json')->header("X-Powered-By",POWER)->send();
  exit();
};

// 直接返回错误信息
function err($msg='error',$code = -1,$data=[]){
  ret(['code' => $code,'msg'=>$msg,'data' => $data]);
}

// 直接返回成功信息
function suc($data=[],$msg='success'){
  ret(['code' => 0,'msg'=>$msg,'data' => $data]);
}