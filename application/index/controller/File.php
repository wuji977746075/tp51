<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-17
 * Time: 15:51
 */

namespace app\index\controller;


use app\src\base\helper\ConfigHelper;
use app\src\base\helper\ExceptionHelper;
use app\src\file\logic\UserPictureLogic;
use app\src\user\logic\MemberLogic;
use app\src\user\logic\UcenterMemberLogic;
use think\Controller;
use think\Exception;
// use app\src\file\logic\AudioFileLogic;
// use app\src\ewt\logic\BookunitLogicV2;
// use app\src\ewt\logic\TestpaperQuestionLogicV2;

class File extends Controller
{

    protected $notify_id = NOW_TIME;
    //对应照片搜索、头像、其它、身份证、行驶证
    protected $Accept_Type = ['photo_search','avatar','other','id_certs','driver_cert'];
    protected $client_id = "";


    //获取书籍或书籍 打包音频
    public function zip($t='',$id=0){
        $ts = ['book','paper'];
        if(!in_array($t, $ts)) exit('类型错误');
        if($t == 'book'){
            $ids = (new BookunitLogicV2)->getAudioIdsByBookId($id);
        }elseif($t== 'paper'){
            $ids = (new TestpaperQuestionLogicV2)->getAudioIdsByPaperId($id);
        }

        $r = (new AudioFileLogic)->zipFiles($t."_".$id,$ids);
        if(!$r['status']) exit($r['info']);
        if($r['info']){
          $file = $r['info'];
          header("Cache-Control: public");
          header("Content-Description: File Transfer");
          header('Content-disposition: attachment; filename='.basename($file)); //文件名
          header("Content-Type: application/zip"); //zip格式的
          header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
          header('Content-Length: '. filesize($file)); //告诉浏览器，文件大小
          @readfile($file);
        }
        exit;
    }

    //获取音频mp3
    public function audio(){
      // $uid = intval($this->request->get('uid',0));
      $id = intval($this->request->get('id',0));
      if($id){
          $r = (new AudioFileLogic)->getInfo(['id'=>$id]);
          if($r['status']){
            if($r['info'] && isset($r['info']['path']))
                $r['info']['path'] = config('site_url').$r['info']['path'];
            $this->apiReturnSuc($r['info']);
            // $this->redirect($r['info']['path'],302);
          }else{
            $this->apiReturnErr($r['ifno']);
            // echo $r['info'];
          }
      }else{
        $this->apiReturnErr('error:id');
        // echo 'error: id';
      }
      die();
    }

    /**
     * curl upload
     * @author hebidu <email:346551990@qq.com>
     */
    public function curl_upload()
    {
        try{
            $this->client_id = $this->_param('client_id','');
            addLog("File/curl_upload",$_GET,'',$this->client_id."调用文件上传接口!");

            $uid   = $this->_param('uid',0);
            $type  = $this->_param('type','');
            $fdata = $this->_param('fdata','');
            $fname = $this->_param('fname','');
            $ftype = $this->_param('ftype','');

            if(!in_array($type,$this->Accept_Type)) $this->apiReturnErr(lang('err_file_not_accept_type'));

            if($uid <= 0) $this->apiReturnErr(lang('invalid_parameter',['param'=>$uid]));

            $userLogic = new UcenterMemberLogic();

            $result = $userLogic->getInfo(['id'=>$uid]);

            if(!$result['status']) $this->apiReturnErr( lang('err_file_user_id_not_exist') );

            /* 调用文件上传组件上传文件 */
            $Picture = new UserPictureLogic();
            $extInfo = array(
                'uid' => $uid,
                'imgurl' => ConfigHelper::upload_path(),
                'type'=>$type);

            $info = $Picture->curl_upload(
                ['data'=>$fdata,'name'=>$fname,'type'=>$ftype],
                config('user_picture_upload'),
                $extInfo
            );

            /* 记录图片信息 */
            if(is_array($info)){
                $this -> apiReturnSuc($info);

            } else {
                $this->apiReturnErr($Picture->getError());
            }

        }catch (Exception $ex){
            $this->apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }

    public function _param($key, $default = '', $emptyErrMsg = '')
    {
        $value = $this->request->post($key, $default);

        if ($value == $default) {
            $value = $this->request->get($key, $default);
        }

        if ($default == $value && !empty($emptyErrMsg)) {
            $this->apiReturnErr($emptyErrMsg);
        }
        return $value;
    }

    /**
     * ajax返回，并自动写入token返回
     * @param $data
     * @param int $code
     * @internal param $i
     */
    protected function apiReturnErr($data, $code = -1)
    {
        header('Content-Type:application/json; charset=utf-8');
        json(['code' => $code, 'data' => $data, 'notify_id' => $this->notify_id])->send();
        exit(0);
    }

    /**
     * ajax返回
     * @param $data
     * @internal param $i
     */
    protected function apiReturnSuc($data)
    {
        header('Content-Type:application/json; charset=utf-8');
        json(['code' => 0, 'data' => $data, 'notify_id' => $this->notify_id])->send();
        exit(0);
    }

    //文件 form 上传

    public function upload()
    {
        try{
            $this->client_id = request()->get("client_id","");

            addLog("File/upload",$_GET,$_POST,$this->client_id."调用文件上传接口!");

            //检查type
            $type = $this->_param('type','');
            if(empty($type)) $type = request()->get('type','');
            if(!in_array($type,$this->Accept_Type)) $this->apiReturnErr(lang("err_not_support_file_ext"));

            //检查 UID
            $uid  = intval($this->_param('uid',0));
            if($uid <= 0) $this->apiReturnErr(lang("invalid_parameter",['param'=>'user id']));
            $logic = new UcenterMemberLogic();
            $result = $logic->getInfo(['id'=>$uid]);
            if(!$result['status']) $this->apiReturnErr(lang("invalid_parameter",['param'=>'user id']));

            if(!isset($_FILES['image'])) $this->apiReturnErr(Llack('image'));
             $files = request()->file('image');

            $result['info'] = "";
            //2.再上传到自己的服务器，
            //TODO:也可以上传到QINIU上

            /* 调用文件上传组件上传文件 */
            $Picture = new UserPictureLogic();
            $extInfo = ['uid' => $uid,'show_url' => ConfigHelper::upload_path(),'type'=>$type];

            $info = $Picture->upload(
                $files,
                ConfigHelper::user_picture_upload(),
                $extInfo
            );
            /* 记录图片信息 */
            if($info && is_array($info)){

                if(count($info) > 0){
                    $one = $info[0];
                    $this->process($type,$one);
                }

                $this -> apiReturnSuc($info);

            } else {
                $this->apiReturnErr(json_encode($info));
                if(is_string($info)){
                    $this->apiReturnErr($info);
                }else{
                    $this->apiReturnErr('上传失败');
                }
            }
        }catch (Exception $ex){
            $this->apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }


    private function process($type,$one){
        if($type == 'avatar'){
            $id = $one['id'];
            $uid = $one['uid'];
            $logic = new MemberLogic();
            $logic->save(['uid'=>$uid],['head'=>$id]);
        }

    }



}