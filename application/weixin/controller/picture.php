<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 青 <99701759@qq.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// 微信用户表中的信息在$this->userinfo
// 用户newmember表的信息在$this->newmember中
// 用户角色表的信息在$this->role中
namespace app\weixin\controller;

use app\src\file\logic\UserPictureLogic;
use think\Controller;
use app\weixin\controller\Home;
use app\src\wxpay\logic\WxaccountLogic;
class Picture{

    protected  $accept_size = array(60,120,150,160,180,200,240,360,480,640,720,960);
    public function returnDefaultImage(){
        exit();
    }

    public function index(){

        $id = input('id',0);
        $size = input('size',0,'intval');
        //TODO: 带图片类型，对不同类型分批处理
        if($id <= 0){
            $this->returnDefaultImage();
        }

        if(in_array($size,$this->accept_size) === false){
            $size = 0;
        }

        $Picture = (new UserPictureLogic());

        $result = $Picture->getInfo(array('id'=>$id));
        if(!$result) $this->returnDefaultImage();
        $result=$result['info'];

        if(empty($result)){
            $this->returnDefaultImage();
        }
        $url = '.'.$result['path'];

        $size = 0;
        if($size > 30 && $size < 1024){
            //TODO:不能太大、太小，可配置
            $url = $this->generate($result,$size);

        }

        if($url === false){
            $this->returnDefaultImage();
        }
//      图片缓存设置
        $url=$result['imgurl'];
        $site_url = config('real_site_url');
        $filePath=str_replace($site_url,'',$url);
        $filePath=str_replace('http://dehong.itboye.com//','',$filePath);
        $filePath=str_replace('http://apidev.itboye.com//','',$filePath);
        //var_dump($url);exit;;
        $time = filemtime($filePath);
        $dt =date("D, d M Y H:m:s GMT", $time );
        header("Last-Modified: $dt");
        header("Cache-Control: max-age=844000");
        header('Content-type: image/'.$result['ext']);
        if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE']==$dt) {
            header("HTTP/1.0 304 Not Modified");
            exit;
        }

        $image = @readfile($url);



        if ($image == false) {
            $this->returnDefaultImage();
        }

        echo $image;

        exit();

    }


}