<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-17
 * Time: 14:49
 */

namespace app\src\file\logic;


use app\src\base\logic\BaseLogic;
use app\src\extend\upload\Upload;
use app\src\file\model\UserPicture;
use think\Exception;

class UserPictureLogic extends BaseLogic
{
    private $error;

    /**
     * @return mixed
     */
    protected function _init()
    {
        $this->setModel(new UserPicture());
    }

    public function getError(){
        return $this->error;
    }
    /**
     * 检测上传目录
     * @param  string $savepath 上传目录
     * @return boolean          检测结果，true-通过，false-失败
     */
    public function checkPath($path){
      /* 检测并创建目录 */
      if(!is_dir($path)){
        if(!mkdir($path)){
          $this->error = '上传目录 ' . $path . ' 创建失败！';
          return false;
        }else{
          chmod($path,0777);
          /* 检测目录是否可写 */
          if (!is_writable($path)) {
              $this->error = '上传目录 ' . $path . ' 不可写！';
              return false;
          } else {
              return true;
          }
        }
      }
      return true;
    }

    /**
     * 文件上传
     * todo: 文件真实目录是否存在
     * todo: 待删除重复的文件
     * @param  array  $files   要上传的文件列表（通常是$_FILES数组）
     * @param  array  $setting config.php中的文件上传配置
     * eg: ConfigHelper::user_picture_upload(),
     * @param  array  $extInfo 传递过来的额外信息
     * eg:['uid' => $uid,'show_url' => ConfigHelper::upload_path(),'type'=>$type];
     * @param  string $driver  上传驱动名称
     * @param  array  $config  上传驱动配置
     * @return array           文件上传成功后的信息
     */
    public function upload($files, $setting,$extInfo, $driver = 'local', $config = null){
        $now      = time();
        $model    = $this->getModel();
        $type     = $extInfo['type'];
        $uid      = $extInfo['uid'];
        $show_url = $extInfo['show_url'];

        $rule = 'date';//保存规则 : date(推荐,其他类型要改) md5 ...
        $path  = isset($setting['rootPath']) ? rtrim($setting['rootPath'],'/'):'./upload/userPicture';
        $path .= '/'.$type; //根据文件类型再分分文件夹
        if(!$this->checkPath($path)) return $this->getError();
        //设置日期子文件夹
        $relate_path = ltrim($path,'.').'/';
        $sub_path    = isset($setting['subName']) ? $setting['subName']:['date','Ymd'];
        if(is_string($sub_path[1]))
          $sub_path = call_user_func($sub_path[0],$sub_path[1]);
        else
          $sub_path = call_user_func_array($sub_path[0],$sub_path[1]);
        if(!$this->checkPath($path.'/'.$sub_path)) return $this->getError();

        //统一封装成多张上传
        if(is_object($files)) $files = [$files];

        //文件检查设置 - 后缀
        $check = ['ext' => $setting['exts']];
        //文件检查设置 - 大小
        if(isset($setting['maxSize'])){
          $size =  (int) $setting['maxSize'];
          if($size>0) $check['size'] = $size;
        }
        //文件检查设置 - 类型
        if(isset($setting['mimes']) && !empty($setting['mimes'])) $check['type'] = $setting['mimes'];
        //文件检查设置 - 长宽比
        if(isset($setting[$type.'_rate'])){
          $rate_arr = $setting[$type.'_rate'];
          $rate_check = true;
        }else{
          $rate_check = false;
        }

        $return  = [];//返回
        foreach($files as $file){
            //  上传根目录: /public/upload/userPicture
            //  详细地址  : 上传根目录+/{$type}/{date}/{md5(microtime)}.{ext}
            // $temp = $file->getPathName();
            // $md5  = md5_file($temp);
            // $sha1 = sha1_file($temp);
            $info = $file->getInfo();
            $name = $info['name'];
            //? 文件检查 长宽比
            if($rate_check){
                $f_info = getimagesize($temp);
                if($f_info[0]*$rate_arr[1] != $f_info[1]*$rate_arr[0]){
                  return '该类型图片需要比例'.$rate_arr[0].':'.$rate_arr[1];
                }
            }

            //文件检查
            if(!$file->check($check)) return $file->getError();
            //上传图片
            $upload = $file->rule($rule)->move($path);
            // $upload = $file->rule($rule)->move('./upload/userPicture/'.$type);
            if($upload->getError()){
                // 上传失败获取错误信息
                return $upload->getError();
            }
            // 成功上传后 获取上传信息
            $sha1 = $upload->hash('sha1');
            $md5  = $upload->hash('md5');

            // ? 图片上传过
            $field = 'path,uid,ori_name,savename,size,url,imgurl,md5,sha1,type,ext,id';
            $r = sdb('itboye_user_picture','')->where('md5',$md5)->field($field)->find();
            if(!empty($r)){ //无需记录
                //todo : 真实图片是否存在
                // $path = '.'.$field['path'];
                // if($is_file($path)){
                //     unlink($path);
                // }
                $img_info = $r;
                $r2 = sdb('itboye_user_picture','')->where(['md5'=>$md5,'uid'=>$uid,'type'=>$type])->field($field)->find();
                if(empty($r2)){
                    //该图片该类型该用户未上传过
                    unset($r['id']);
                    $r['uid']      = $uid;
                    $r['type']     = $type;
                    $r['ori_name'] = $name;
                    $r['create_time'] = $now;
                    // url(图片链接) ? ...
                    $id = (int) $model->insertGetId($r);
                    if($id){
                        $r['id'] = $id;
                        $img_info = $r;
                    }
                }else{
                    $img_info  = $r2;
                }
                $img_info['new'] = 0; //文件已存在
                $return[] = $img_info;
            }else{

                $ext      = $upload->getExtension();
                $savename = $upload->getFilename();
                //修复多文件上传 20170116 : $path
                $path2     = $relate_path.$sub_path.'/'.$savename;
                $imgurl   = rtrim($show_url,'/').$path2;
                $img_info = [
                  'path'        => $path2,
                  'uid'         => $uid,
                  'ori_name'    => $name,
                  'savename'    => $savename,
                  'size'        => $info['size'],
                  'url'         => '',//图片链接
                  'imgurl'      => $imgurl,//完整显示地址
                  'md5'         => $md5,
                  'sha1'        => $sha1,
                  'type'        => $type,
                  'ext'         => $ext,
                  'create_time' => $now,
                ];
                $id = (int) $model->insertGetId($img_info);
                if(!$id){
                    //TODO: 上传成功，插入失败,记录日志
                    return $model->getError();
                }
                $img_info['id']  = $id;
                $img_info['new'] = 1; //新文件
                $return[] = $img_info;
            }
        }
        return $return;
    }

    /**
     * curl 文件上传 - 20170118修复 rainbow
     * 老版curl_upload上传
     * @param  array  $files
     *  $data : 文件流数据
     *  eg: $files  = ['data'=>$fdata,'name'=>$fname,'type'=>$ftype]
     * @param  array  $setting 文件上传配置 application/config.php
     *  eg: config('user_picture_upload')
     * @param  string $extInfo
     *  eg: ['uid' => $uid,'imgurl' => config('upload_url'),'type'=>$type];
     * @param  string $driver  上传驱动名称
     * @param  array  $config  上传驱动配置
     * @return array           文件上传成功后的信息
     */
    public function curl_upload($files, $setting,$extInfo, $driver = 'local', $config = null){
        $now = time();
        /* 上传文件 */
        $setting['callback']    = [$this, 'isFile'];
        $setting['removeTrash'] = [$this, 'removeTrash'];
        $type = isset($extInfo['type']) ? $extInfo['type']: 'other';
        $setting['savePath'] = $type.'/';
        $Upload = new Upload($setting, $driver, $config);
        $info   = $Upload->curl_upload($files);
// [\"name\"] => string(9) \"index.png\"\n
// [\"type\"] => string(9) \"image\/png\"\n
// [\"size\"] => int(3887)\n
// [\"ext\"] => string(3) \"png\"\n
// [\"md5\"] => string(32) \"3b841d8721e2c09618565bf10e0cfd37\"\n
// [\"sha1\"] => string(40) \"aeb7dfd6b7d546b7307346a1f68c176351a445fd\"\n
// [\"savename\"] => string(17) \"587f1468d4dc1.png\"\n
// [\"savepath\"] => string(57) \".\/upload\/userPicture\/.\/upload\/userPicture\/other\/20170118\/\"
        if($info){ //文件上传成功，记录文件信息
            $infos = [$info];
            $record = [];

            foreach ($infos as $key => &$value) {
                /* 已经存在文件记录 */
                if(isset($value['id']) && is_numeric($value['id'])){
                    $value['new'] = 0;
                    continue;
                }
                // dump($setting);exit;
                $value = array_merge($value,$extInfo);
                $value['ori_name'] = $value['name'];
                /* 记录文件信息 */
                $value['path'] = substr($value['savepath'],1).$value['savename'];   //在模板里的url路径
                $value['imgurl'] = rtrim($extInfo['imgurl'],'/').$value['path'];
                $map = [
                  'path'        => $value['path'],
                  'uid'         => $value['uid'],
                  'ori_name'    => $value['ori_name'],
                  'savename'    => $value['savename'],
                  'size'        => $value['size'],
                  'url'         => '',
                  'imgurl'      => $value['imgurl'],
                  'md5'         => $value['md5'],
                  'sha1'        => $value['sha1'],
                  'status'      => 1,
                  'create_time' => $now,
                  'type'        => $type,
                  'ext'         => $value['ext'],
                ];
                $model = $this->getModel();
                $id = $model->insertGetId($map);
                $value['id'] = (int) $id;
                $value['new'] = 1;
            }
            return $infos; //文件上传成功
        } else {
            $this->error = $Upload->getError();
            return false;
        }
    }


    /**
     * 下载指定文件
     * @param  number  $root 文件存储根目录
     * @param  integer $id   文件ID
     * @param  string   $args     回调函数参数
     * @return boolean       false-下载失败，否则输出下载文件
     */
    public function download($root, $id, $callback = null, $args = null){
        /* 获取下载文件信息 */
        $file = $this->getInfo(['id'=>$id])->toArray();
        if(!$file){
            $this->error = '不存在该文件！';
            return false;
        }

        /* 下载文件 */
        switch ($file['location']) {
            case 0: //下载本地文件
                $file['rootpath'] = $root;
                return $this->downLocalFile($file, $callback, $args);
            case 1: //TODO: 下载远程FTP文件
                break;
            default:
                $this->error = '不支持的文件存储类型！';
                return false;

        }

    }

    /**
     * 检测当前上传的文件是否已经存在
     * @param  array $file 文件上传数组
     * @return bool 文件信息， false - 不存在该文件
     * @throws \Exception
     */
    public function isFile($file){
        if(empty($file['md5'])){
            throw new \Exception('缺少参数:md5');
        }
        /* 查找文件 */
        $map = array('md5' => $file['md5'],'sha1'=>$file['sha1'],);
        return $this->getModel()->where($map)->find();
    }

    /**
     * 下载本地文件
     * @param  array    $file     文件信息数组
     * @param  callable $callback 下载回调函数，一般用于增加下载次数
     * @param  string   $args     回调函数参数
     * @return boolean            下载失败返回false
     */
    private function downLocalFile($file, $callback = null, $args = null){
        if(is_file($file['rootpath'].$file['savepath'].$file['savename'])){
            /* 调用回调函数新增下载数 */
            is_callable($callback) && call_user_func($callback, $args);

            /* 执行下载 */ //TODO: 大文件断点续传
            header("Content-Description: File Transfer");
            header('Content-type: ' . $file['type']);
            header('Content-Length:' . $file['size']);
            if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
                header('Content-Disposition: attachment; filename="' . rawurlencode($file['name']) . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
            }
            readfile($file['rootpath'].$file['savepath'].$file['savename']);
            exit;
        } else {
            $this->error = '文件已被删除！';
            return false;
        }
    }

    /**
     * 清除数据库存在但本地不存在的数据
     * @param $data
     */
    public function removeTrash($data){
        $this->getModel()->where(array('id'=>$data['id'],))->delete();
    }


}