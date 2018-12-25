<?php

// function data_encode($data,$secret,$time){
//   $data = base64_encode(json_encode($data).$secret);
//   return [
//     'data'=>base64_encode($data.$time),
//     'time'=>$time
//   ];
// }

// function data_decode($data,$secret,$time){
//   $data = preg_replace('/'.$time.'$/', '', base64_decode($data));
//   $data = preg_replace('/'.$secret.'$/', '', base64_decode($data));
//   return json_decode($data,true);
// }
