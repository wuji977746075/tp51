<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-17
 * Time: 14:45
 */
namespace app\index\controller;

use image\Image;
use src\file\UserPictureLogic;
use src\user\UserLogic;
use think\Controller;

/**
 * 图片查看控制器
 * 图片和id绑定 不要修改绑定 缩略图没做改变
 * Picture ControllerClass
 * @author hebidu <email:346551990@qq.com>
 * @package app\index\controller
 */
class Picture extends Controller{

    protected $accept_size    = [];
    protected $denyPropValue  = 50;
    protected $thumbnail_path = '';
    protected $req_etag       = ''; // http cache : etag
    protected $req_edit       = '';// http cache : last_modifed
    protected $default_expire = 600;// http cache : last_modifed
    protected $default_etag   = 0.2018082402;// http cache : etag
    protected $default_avatar = 7;// default avatar img id

    protected function initialize() {
      $this->thumbnail_path = config('app.file_cfg.thumbnail_path');
      $sizes = config('app.file_cfg.picture_crop_size');
      if (!$sizes || !is_array($sizes)) $sizes = [60, 120, 150, 180, 200];
      $this->accept_size = $sizes;
      header("X-Copyright:".POWER);
      $this->req_etag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : '';
      $this->req_edit = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : '';
    }

    // 加入缓存防止对应的图片id变了
    function avatar(){
      $uid  = input('param.uid/d',0);
      $size = input('param.size/d',0); // 默认尺寸
      $df   = $this->default_avatar; //默认头像图片id
      if($uid){
        $logic = new UserLogic;
        $r = $logic->getInfo(['id'=>$uid]);
        $head = $r ? $r['avatar'] : '';
        if (strpos($head,'http') === 0){
          $this->redirect($head);
        }else{
          $this->getPicture($r ? intval($r['avatar']) : $df,$size);
        }
      }else{
        $this->getPicture($df,$size);
      }

    }

    // 验证码
    // 关闭vender 的 route
    // $id 多个时的flag
    function captcha ($w=120,$h=50,$id='login') {
      // Config::pull('captcha') or config/captcha.php
      $ops = [
        'fontSize' => 20,
        'imageW'   => $w,
        'imageH'   => $h,
        'useImgBg '=> true,
        'useNoise' => false,
        'expire'   => 300, // 300s 失效
        // 'seKey'    => 'rainbowP',// 改的话验证那也该自定义
        // 'length'   => 4,
        // 'reset'    => false, // 成功后重置
      ];
      $captcha = new \think\captcha\Captcha($ops);
      return $captcha->entry($id);
    }
    // 请求头缓存
    function index() {
      //TODO: 带图片类型，对不同类型分批处理
      $id   = input('id/d', 0);
      $size = input('size/d', 0);
      $this->getPicture($id, $size);
    }


    function test(){
      $image = new Image;
      $info['path'] = '/';
      if(!file_exists('.'.$info['path'])) return false;

      $image->open( realpath('.'.$info['path']));
      $thumbnail_path = $this->thumbnail_path.'/'.date('Y-m-d',time()).'/';

      if(!is_dir(($thumbnail_path))){
          if(!mkdir(($thumbnail_path))) return false;
      }
    }

    // 请求头缓存 : id图片一一对应 : max-age + etag
    // 支持裁减大小宽度
    private function getPicture($id,$size=0){
        if($id <= 0) $this->returnDefaultImage();
        if(in_array($size,$this->accept_size) === false) $size = 0;

        $logic = new UserPictureLogic;
        $info  = $logic->getInfo(['id'=>$id]);
        if(empty($info)) $this->returnDefaultImage();
        $url = '.'.$info['path'];

        //
        $porn_prop = $info['porn_prop'];
        if ($porn_prop >= $this->denyPropValue) $this->returnDenyImage();

        if(file_exists($url) === false){ // 文件不存在
          $src = $info['imgurl'];
          if(strpos($src,config('site_url')) !== false){ // 本机的
            $this->returnDefaultImage();
          }else{ // 非本机
            $this->redirect($src);exit;
            // $match = preg_match('/^http:\/\/(.*?)\//', $src);
            // $this->redirect($match[0].'/index.php/picture/index?id='.$id.'&size='.$size);
          }
        }
        if($size > 30 && $size < 1024){
          $url = $this->generate($info,$size);
        }
        if($url === false) $this->returnDefaultImage();
        $this->req_etag == $id && retCode(304);
        // http 缓存设置
        header("Cache-Control: must-revalidate, max-age=10");
        header('Content-type: image/'.$info['ext']);
        // Last_Modified(now)+Etag / Last_Modified
        // HTTP_IF_NONE_MATCH
        // 使用固定值,应确保id对应图片不改变,可以改进为MD5/CRC等
        header("Etag: $id"); //最多缓存1h
        // HTTP_IF_MODIFIED_SINCE
        // $time = filemtime($url);
        // $real_edit = date("D, d M Y H:m:s GMT", $time );
        // header("Last-Modified: $real_edit");
        // header("Cache-Control: max-age=".$this->default_expire);
        // if($this->req_edit) {
        //   $req_edit = $this->req_edit;
        //   if(strtotime($req_edit) > NOW_TIME){
        //     retCode(401);
        //   }else if($req_edit == $real_edit){
        //     retCode(304);
        //   }
        // }

        $image = @readfile($url);
        if ($image == false) $this->returnDefaultImage();
        echo $image;
        exit;
    }

    /**
     * 生成缩略图
     * @param $info
     * @param $size
     * @return string
     */
    protected function generate($info,$size){
        $thumbnail_path = $this->thumbnail_path .'/w'.$size.'/';
        $save_name = $info['save_name'];
        $relative_path = $thumbnail_path.$save_name;
        if(file_exists($relative_path)) return $relative_path;

        $image = new Image;
        if(!file_exists(realpath('.'.$info['path']))) return false;
        $image->open( realpath('.'.$info['path']));

        if(!is_dir(($thumbnail_path))){
            if(!mkdir(($thumbnail_path))) return false;
        }

        $size_info  = getimagesize(realpath('.'.$info['path']));
        $scale_size = $this->calcScale($size_info[0],$size_info[1],$size);
        $result     = $image->thumb($scale_size['width'], $scale_size['height'],Image::IMAGE_THUMB_FIXED)->save($relative_path, null, 100);

        if(!file_exists(realpath($relative_path))) return false;
        return $relative_path;

    }

    protected function calcScale($w, $h, $size){
      $scale = $w / $h;
      if($w > $h){
        $dw = $size;
        $dh = intval($dw / $scale);
      }else{
        $dh = $size;
        $dw = intval($dh * $scale);
      }
      return ['width'=>$dw, 'height'=> $dh];
    }
    /**
     * 返回一个404图片
     */
    public function returnDefaultImage(){
      $this->returnBase64Image();
    }
    /**
     * 返回一个禁止访问的图片
     */
    public function returnDenyImage() {
      $this->returnBase64Image('deny');
    }
    private function returnBase64Image($type ='df'){
      $etag = $type.$this->default_etag;
      $this->req_etag == $etag && retCode(304);
      $code = $type == 'df' ? $this->default : $this->deny;
      $im  = imagecreatefromstring(base64_decode($code));
      if ($im !== false) {
        header('Content-Type: image/png');
        header("Cache-Control: must-revalidate, max-age=60");
        header("Etag: ".$etag);
        imagepng($im);
        imagedestroy($im);
        exit;
      }
      retCode(401);
    }
    // data:image/png;base64,
    protected $default = "iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAIAAAAiOjnJAAAACXBIWXMAAA7EAAAOxAGVKw4bAAACFUlEQVR4nO3SQQ0AIRDAwOP8S1tRmKAhITMK+uiamQ9O+28H8CZjkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFgljkTAWCWORMBYJY5EwFokNc30D9OCKRowAAAAASUVORK5CYII=";
    protected $deny = "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";
}