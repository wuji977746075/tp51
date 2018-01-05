<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 17:19
 */

namespace app\admin\controller;

use app\src\base\helper\ConfigHelper;
use app\src\file\logic\UserPictureLogic;
use app\src\file\logic\AudioFileLogic;
use think\Response;

class File extends Admin
{

    protected function initialize(){

        //TODO: 导致session，修改不启作用，沿用上次，导致一级菜单未能存入session，使得当前激活菜单不正确
        //FIXME:考虑，将图片上传放到另外一个类中
        //解决uploadify上传session问题

        session('[pause]');
        $session_id = $this->_param('session_id','');
        if (!empty($session_id)) {
            session_id($session_id);
            session('[start]');
        }

        parent::initialize();
    }

    public function uploadPicture(){

        if(IS_POST){

            /* 返回标准数据 */
            $return  = ['status' => 1, 'info' => '上传成功', 'data' => ''];
            if(config('UPLOAD_PICTURE_REMOTE_URL')==NULL){

                /* 调用文件上传组件上传文件 */
                $Picture = new UserPictureLogic();
                $pic_driver = config('PICTURE_UPLOAD_DRIVER');
                $info = $Picture->upload(
                    $_FILES,
                    config('PICTURE_UPLOAD'),
                    config('PICTURE_UPLOAD_DRIVER'),
                    config("UPLOAD_{$pic_driver}_CONFIG")
                ); //TODO:上传到远程服务器

                /* 记录图片信息 */
                if($info){
                    $return['status'] = 1;
                    $return = array_merge($info['download'], $return);
                } else {
                    $return['status'] = 0;
                    $return['info'] = $Picture->getError();
                }
            }else{

                /* 上传到远程服务器 */
                $result = $this->uploadPictureRemote($_FILES['image']);
                $result = json_decode($result,true);
                if(isset($result['code']) && $result['code'] == 0){
                    $return = $result['data']['image'];
                    $return['status'] = 1;
                }else{
                    $return['status'] = 0;
                    $return['info'] = $result['data'];
                }
            }

            /* 返回JSON数据 */
            $this->ajaxReturn($return);
        }

    }

    //上传到远程服务器
    private function uploadPictureRemote($file){

        $tmp_path = $file['tmp_name'];
        if(!is_uploaded_file($tmp_path)) exit('invalid tmp file');
        $data = file_get_contents($tmp_path);

        $url = config('file_curl_upload_url').'?client_id='.CLIENT_ID;
        //只支持单文件
        $file  = ['fdata'=>$data,'ftype'=>$file['type'],'fname'=>$file['name']];
        $param = ['type'=>'other','uid'=>UID];

        $op    = $this->upload_file($url,$file,$param);
        return $op;
    }
    private function upload_file($url,$file,$param){
        $post_data = array_merge($file,$param);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT,5);            //定义超时5秒钟
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

        $return_data = curl_exec($ch);

        curl_close($ch);
        return $return_data;
    }

    //ajax - 视频列表
    public function audiolist(){
        // if(IS_AJAX){
            $q = $this->_param('q','');
            $time = $this->_param('time','');
            $cur  = $this->_param('p',0);
            $size = $this->_param('size',10);
            $map  = ['uid'=>$this->_uid,'status'=>1];
            $page = ['curpage'=>$cur,'size'=>$size];
            $order  = 'create_time desc';
            $params = [
                'p'    =>$cur,
                'size' =>$size,
            ];
            if(!empty($q)){
                $params['q'] = $q;
                $map['ori_name'] = ["like",'%'.$q.'%'];
            }
            if(!empty($time)){
                $time = strtotime($time);
                $map['create_time'] = [['lt',$time+24*3600],['gt',$time-1],'and'];
            }
            $fields = 'id,create_time,status,path,md5,ori_name,save_name,size,duration';
            $r = (new AudioFileLogic)->queryWithPagingHtml($map,$page,$order,$params,$fields);

            $r['status'] = (int) $r['status'];
            $this->ajaxReturn($r);

        // }
    }
    //ajax  - 图片列表
    public function picturelist(){
        // if(IS_AJAX){
            $q = $this->_param('q','');
            $time = $this->_param('time','');
            $cur = $this->_param('p',0);
            if( $cur==0){
                $cur=$this->_param('p',0);
            }
            $size = $this->_param('size',10);
            $map = array('uid'=>$this->_uid,'status'=>1);
            $page = array('curpage'=>$cur,'size'=>$size);
            $order = 'create_time desc';
            $params = array(
                'p'=>$cur,
                'size'=>$size,
            );
            if(!empty($q)){
                $params['q'] = $q;
                $map['ori_name'] = array("like",'%'.$q.'%');
            }

            if(!empty($time)){
                $time = strtotime($time);
                $map['create_time'] = array(array('lt',$time+24*3600),array('gt',$time-1),'and');
            }

            $fields = 'id,create_time,status,path,url,md5,imgurl,ori_name,savename,size';
            $r = (new UserPictureLogic)->queryWithPagingHtml($map,$page,$order,$params,$fields);

            $r['status'] = (int) $r['status'];
            $this->ajaxReturn($r);

        // }
    }


    /**
     * 上传音频接口
     * UPLOAD_PICTURE_REMOTE_URL
     */
    public function uploadUserAudio(){
        if(IS_POST){

            if(!isset($_FILES['audio'])){
                $this->apiReturnErr("文件对象必须为audio");
            }

            $result['info'] = "";
            //2.再上传到自己的服务器，
            //TODO:也可以上传到QINIU上
            /* 返回标准数据 */
            $return = ['status' => 1, 'info' => '上传成功', 'data' => ''];
            // if(config('UPLOAD_PICTURE_REMOTE_URL')==NULL) {
                $type = 'other';//todo: 上传时设置
                /* 调用文件上传组件上传文件 */
                $Picture = new AudioFileLogic();
                $extInfo = ['uid' => UID, 'show_url' => ConfigHelper::upload_path(),'type'=>$type];
                $file = request()->file('audio');
                $info = $Picture->upload(
                    $file,
                    config('user_audio_upload')
                    , $extInfo
                );
                /* 记录图片信息 */
                if ($info) {
                    $return['status'] = 1;
                    $return['info'] = $info[0];
                    // $return = array_merge($info, $return);
                } else {
                    $return['status'] = 0;
                    $return['info'] = $Picture->getError();
                }
            // }else{
                /* 上传到远程服务器 */
                // $result = $this->uploadPictureRemote($_FILES['audio']);

                // $result = json_decode($result,true);

                // if(isset($result['code']) &&  $result['code']==0){
                //     $return = $result['data']['image'];
                //     $return['status'] = 1;
                // }else{
                //     $return['status'] = 0;
                //     $return['info'] = $result['data'];
                // }
            // }

        }else{
            $return = ['status'=>false,'info'=>"非法请求"];
        }
        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }
    /**
     * 上传图片接口
     */
    public function uploadUserPicture(){

        if(IS_POST){

            if(!isset($_FILES['wxshop'])){
                $this->error("文件对象必须为wxshop");
            }

            $result['info'] = "";
            //2.再上传到自己的服务器，
            //TODO:也可以上传到QINIU上
            /* 返回标准数据 */
            $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

            if(config('UPLOAD_PICTURE_REMOTE_URL')==NULL) {
                $type = 'other';//todo: 上传时设置

                /* 调用文件上传组件上传文件 */
                $Picture = new UserPictureLogic();
                $extInfo = array('uid' => $this->_uid, 'show_url' => ConfigHelper::upload_path(),'type'=>$type);
                $file = request()->file('wxshop');
                $info = $Picture->upload(
                    $file,
                    config('user_picture_upload')
                    , $extInfo
                );

                /* 记录图片信息 */
                if ($info) {
                    $return['status'] = 1;
                    $return['info'] = $info[0];
                    // $return = array_merge($info, $return);
                } else {
                    $return['status'] = 0;
                    $return['info'] = $Picture->getError();
                }
            }else{
                /* 上传到远程服务器 */
                $result = $this->uploadPictureRemote($_FILES['wxshop']);

                $result = json_decode($result,true);

                if(isset($result['code']) &&  $result['code']==0){
                    $return = $result['data']['image'];
                    $return['status'] = 1;
                }else{
                    $return['status'] = 0;
                    $return['info'] = $result['data'];
                }
            }

            /* 返回JSON数据 */
            $this->ajaxReturn($return);
        }

    }

    private function ajaxReturn($data){
        $response =  Response::create($data, "json")->code(200);
        $response->header("X-Powered-By","WWW.ITBOYE.COM")->send();
        exit;
    }

    private function ajaxReturnErr($msg='default err msg'){
        $data = ['status'=>false,'info'=>$msg];
        $this->ajaxReturn($data);
    }

    /**
     * 图片删除
     */
    public function del(){
        $imgIds = $this->_param("imgIds/a",-1);
        if($imgIds!=-1){
            $map=array(
                'id'=>array(
                    'in',$imgIds
                )
            );

            $result = (new UserPictureLogic())->save($map,array('status'=>-1));

            if ($result['status']) {
                $this->success("删除成功!");
            }else{
                $this->error("删除失败!");
            }

        }else{
            $this->error("请先选中要删除的图片!");
        }
    }
}