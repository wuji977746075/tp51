<?php
/**
 * 周舟 hzboye010@163.com
 * addby sublime snippets
 * File Test 附件测试类
 */

namespace app\test\controller;

use app\extend\BoyeService;

class File extends Ava {

	//单文件上传 -web
	public function upload(){
	//	if(IS_AJAX){
	// 		$data = array(
  //       'username' => input('post.username',''),
  //       "password" => input('post.password',''),
  //       'api_ver'  =>$this->api_ver,
  //       'type'		 =>'BY_User_login',
  //     );

  //     $service = new BoyeService();
  //     $result = $service->callRemote("",$data,false);
  //     return $this->parseResult($result);
  //   }else{
    	$this->assign('type','BY_File_');
	    // $this->assign('client_id',CLIENT_ID);
	    $this->assign('post_url',config('API_URL').'/file/upload?client_id='.CLIENT_ID);
			$this->assign('field',[
				['uid','11',LL('need-mark user ID')],
				['type2','avatar',LL('need-mark file-type')],
				['house_no','HN1110057',L('house_no')],
			]);
	    return $this -> fetch();
		// }
	}

	//获取图片 - web
	public function showPic(){
		// if(IS_AJAX){
		// 	$data = array(
		// 		'id'        => input('post.id',''),
		// 		"size"      => input('post.size',''),
		// 		'api_ver'   =>$this->api_ver,
		// 		'notify_id' =>$this->notify_id,
		// 		'alg'       =>$this->alg,
		// 		'type'      =>'BY_Picture_index',
	  //     );

	  //     $service = new BoyeService();
	  //     $result = $service->callRemote("",$data,false);
	  //     return $this->parseResult($result);
		// }else{
			$this->assign('type','BY_File_');
			$this->assign('post_url',config('api_url').'/picture/index');
			$this->assign('sizes',[60,120,150,160,180,200,240,360,480,640,720,960]);
			$this->assign('field',[
					['id','21',LL('need-mark picture ID')],
					['size','',L('file-size')],
			]);
		  return $this -> fetch('showPic');
	  // }
	}

	//获取app图片轮播
	public function banner(){
		if(IS_AJAX){
			$data = array(
        'position' => input('post.position',''),
        'api_ver'  => $this->api_ver,
        'type'		 =>'BY_File_banner',
      );

      $service = new BoyeService();
      $result = $service->callRemote("",$data,false);
      return $this->parseResult($result);
    }else{
    	$this->assign('type','BY_File_banner');
			$this->assign('field',[
				['api_ver',$this->api_ver,LL('need-mark api version')],
				['position',6030,LL('need-mark position ID')],
			]);
	    return $this -> fetch('ava/test');
		}
	}

	//图片删除
	// public function delPic(){
	// 	if(IS_AJAX){
	// 		$data = array(
	// 			'uid'  => input('post.uid',''),
	// 			'tid'  => input('post.tid',''),
	// 			'imgs' => input('post.imgs',''),
 //        'api_ver'  =>$this->api_ver,
 //        'type'		 =>'BY_File_delPic',
 //      );
 //      $service = new BoyeService();
 //      $result = $service->callRemote("",$data,false);
 //      return $this->parseResult($result);
 //    }else{
 //    	$this->assign('type','BY_File_delPic');
	// 		$this->assign('field',[
	// 			['api_ver',$this->api_ver,LL('need-mark api version')],
	// 			['uid',50,LL('need-mark user ID')],
	// 			['tid',6742,LL('need-mark thread ID')],
	// 			['imgs','4,5',LL('need-mark img IDS')],
	// 		]);
	//     return $this -> fetch('ava/test');
	// 	}
	// }
}