<?php
//文件 curl_upload 测试
// var_dump($_FILES);exit;
// array(1) {
//   ["file"]=&gt;
//   array(5) {
//     ["name"]=&gt;
//     string(14) "10元1比1.png"
//     ["type"]=&gt;
//     string(9) "image/png"
//     ["tmp_name"]=&gt;
//     string(22) "C:\Windows\php724D.tmp"
//     ["error"]=&gt;
//     int(0)
//     ["size"]=&gt;
//     int(20072)
//   }
// }
function upload_file($url,$file,$param){
  $post_data = array_merge($file,$param);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT,5);            //定义超时5秒钟
  curl_setopt($ch, CURLOPT_POST, true );
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
  // curl_getinfo($ch);
  $return_data = curl_exec($ch);
  curl_close($ch);
  return $return_data;
}
if(isset($_FILES['file'])){
	$file 	  = $_FILES['file'];
	$tmp_path = $file['tmp_name'];
	if(!is_uploaded_file($tmp_path)) exit('invalid tmp file');
	$data = file_get_contents($tmp_path);
	// file_put_contents('2.png', substr($png,0,-11));
	// header('Content-Disposition: Attachment;filename=image.png');
	// header('Content-type: image/png');
	// $im = imagecreatefrompng($tmp_path);
	// imagepng($im);

  //只支持单文件
  // $url   = 'http://ihome.api/public/index.php/file/curl_upload?client_id=by571846d03009e1';
  $url   = 'http://ihome.api/public/index.php/file/curl_upload?client_id=by571846d03009e1';
  $file  = ['fdata'=>$data,'ftype'=>$file['type'],'fname'=>$file['name']];
  $param = ['type'=>'other','uid'=>11];
  $op    = upload_file($url,$file,$param);
  echo json_encode($op);
  exit;
}