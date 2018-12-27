<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-17
 * Time: 14:49
 */

namespace src\file\user;

use src\base\BaseLogic;
use upload\Upload;
use think\Db;
use \ZipArchive as Zip;

class UserAudioLogic extends BaseLogic{

    // 确保指定的打包音频文件存在
    public function zipFiles($t='',$ids=[]){
        if($ids){
            $ids_md5 = md5(implode(',', $ids));
            $filename  = "./upload/zip/".$t."_".$ids_md5.".zip";
            if(!file_exists($filename)){ //不存在文件则创建
                $files = [];
                $r = $this->queryNoPaging(['id'=>['in',$ids]],false,'id,path');
                if(!$r['status']) return $r;
                foreach ($r['info'] as $v) {
                    $files[$v['id']] = $v['path'];
                }

                //生成文件
                $zip = new Zip();//linux需开启zlib，windows需开启php_zip.dll
                if ($zip->open($filename, Zip::CREATE)!==TRUE) {
                  return returnErr('文件创建失败');
                }
                foreach( $files as $k=>$v){
                  $file = '.'.$v;
                  if(file_exists($file)){
                    $zip->addFile( $file, $k.'.mp3');//第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
                  }else{
                    return returnErr("音频文件缺失:$k");
                  }
                }
                $zip && $zip->close();//关闭
                if(!file_exists($filename)){ // ? 仍然不存在
                    return returnErr("未找到文件");
                }
            }
            return returnSuc($filename);
        }
        return returnErr('需要音频');
    }

    protected function mp3_len($file) {
      $m = new \Mp3file($file);
      $a = $m->get_metadata();
      return $a['Length'] ? $a['Length'] : 0;
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
     * fix多图上传错误(path变量污染) 2018-03-30 16:44:04
     * @param  array  $files   要上传的文件列表（通常是$_FILES数组）
     * @param  array  $setting config.php中的文件上传配置
     * eg: ConfigHelper::user_picture_upload(),
     * @param  array  $extInfo 传递过来的额外信息
     * eg:['uid' => $uid,'show_url' => ConfigHelper::upload_path(),'type'=>$type];
     * @param  string $driver  上传驱动名称
     * @param  array  $config  上传驱动配置
     * @return array(成功信息)/string(失败字符串)
     */
    public function upload($files, $setting,$extInfo, $driver = 'local', $config = null){
        $now      = time();
        $model    = $this->getModel();
        $type     = $extInfo['type'];
        $uid      = $extInfo['uid'];
        $show_url = $extInfo['show_url'];

        $rule = 'date';//保存规则 : date(推荐,其他类型要改) md5 ...
        $path  = isset($setting['rootPath']) ? rtrim($setting['rootPath'],'/'):'./upload/userAudio';
        $path .= '/'.$type; //根据文件类型再分分文件夹
        // if(!$this->checkPath($path)) return $this->getError();
        //设置日期子文件夹
        $relate_path = ltrim($path,'.').'/';
        $sub_path    = isset($setting['subName']) ? $setting['subName']:['date','Ymd'];
        if(is_string($sub_path[1]))
          $sub_path = call_user_func($sub_path[0],$sub_path[1]);
        else
          $sub_path = call_user_func_array($sub_path[0],$sub_path[1]);
        // if(!$this->checkPath($path.'/'.$sub_path)) return $this->getError();

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
        $type = $extInfo['type'];
        //文件检查设置 - 长宽比
        // if(isset($setting[$type.'_rate'])){
        //   $rate_arr = $setting[$type.'_rate'];
        //   $rate_check = true;
        // }else{
        //   $rate_check = false;
        // }

        $return  = [];//返回
        foreach($files as $file){
            //  上传根目录: /public/upload/userAudio
            //  详细地址  : 上传根目录+/{$type}/{date}/{md5}.{ext}
            // $temp = $file->getPathName();
            // $md5  = md5_file($temp);
            // $sha1 = sha1_file($temp);
            $info = $file->getInfo();
            $name = $info['name'];
            //MP3文件时长
            $length = $this->mp3_len($info['tmp_name']);
            //文件检查
            if(!$file->check($check)) return $file->getError();
            //上传图片
            $upload = $file->rule($rule)->move($path);
            if($upload->getError()){
                // 上传失败获取错误信息
                return $upload->getError();
            }
            // 成功上传后 获取上传信息
            $sha1 = $upload->hash('sha1');
            $md5  = $upload->hash('md5');

            // ? 图片上传过
            $field = 'path,uid,ori_name,save_name,size,md5,sha1,type,ext,id';
            $r = $model->where(['md5'=>$md5,'status'=>1,'ori_name'=>$name])->field($field)->find();
            if(!empty($r)){ //无需记录
                //todo : 真实图片是否存在
                // $path = '.'.$field['path'];
                // if($is_file($path)){
                //     unlink($path);
                // }
                $img_info = $r;
                $r2 = $model->where(['md5'=>$md5,'uid'=>$uid,'type'=>$type,'status'=>1,'ori_name'=>$name])->field($field)->find();
                if(empty($r2)){
                  //该图片该类型该用户未上传过
                  unset($r['id']);
                  $r['uid']         = $uid;
                  $r['type']        = $type;
                  $r['ori_name']    = $name;
                  $r['create_time'] = $now;
                  $r['status']      = 1;
                  $r['update_time'] = $now;
                  $r['duration']    = $length;
                  // url(图片链接) ? ...
                  $id = (int) $this->add($r);
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
              $path1    = $relate_path.$sub_path.'/'.$savename;
              // $imgurl   = rtrim($show_url,'/').$path1;
              $img_info = [
                'ori_name'    => $name,
                'path'        => $path1,
                'uid'         => $uid,
                'save_name'   => $savename,
                'size'        => $info['size'],
                'duration'    => $length,//时长
                'md5'         => $md5,
                'sha1'        => $sha1,
                'type'        => $type,
                'ext'         => $ext,
                'status'      => 1,
                'create_time' => $now,
                'update_time' => $now,
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
     * curl 文件上传 - todo:修改
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
// shalt('here');die();
        /* 上传文件 */
        $setting['callback']    = [$this, 'isFile'];
        $setting['removeTrash'] = [$this, 'removeTrash'];
        $setting['savePath']    = (isset($extInfo['type']) ? $extInfo['type']: 'other').'/';
        $Upload = new Upload($setting, $driver, $config);
        $info   = $Upload->curl_upload($files);

        if($info){ //文件上传成功，记录文件信息
            $infos = ['image'=>$info];
            $record = [];
            foreach ($infos as $key => &$value) {
                /* 已经存在文件记录 */
                if(isset($value['id']) && is_numeric($value['id'])){
                    continue;
                }
                // dump($setting);exit;
                $value = array_merge($value,$extInfo);
                $value['ori_name'] = $value['name'];
                /* 记录文件信息 */
                $value['path'] = substr($value['savepath'],1).$value['savename'];   //在模板里的url路径
                $value['imgurl'] = $value['imgurl'].$value['path'];
                $map = [
                  'path'        => $value['path'],
                  'ori_name'    => $value['ori_name'],
                  'savename'    => $value['savename'],
                  'size'        => $value['size'],
                  'imgurl'      => $value['imgurl'],
                  'md5'         => $value['md5'],
                  'sha1'        => $value['sha1'],
                  'create_time' => $now,
                  'ext'         => $value['ext'],
                  'uid'         => $uid,
                  'type'        => $type,
                ];
                if($id = $this->insertGetId($map)){
                  $value['id'] = $id;
                } else {
                    //TODO: 文件上传成功，但是记录文件信息失败，需记录日志
                    //LogRecord($info[$key], $location)
                    //unset($infos);
                }
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



    public function queryWithType($map = null, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false){

        $field = 'pic.id as id,pic.path as path,pic.imgurl as imgurl,dt.name as type_name,pic.url,pic.ori_name as ori_name,pic.create_time as create_time,pic.uid as uid';
        $query = Db::table("itboye_user_picture")->alias("pic")
            ->field($field)
            ->join(["common_datatree"=>"dt"],'dt.code = pic.type',"LEFT");
        if(!is_null($map)){
            $query = $query->where($map);
        }

        $list = $query ->order('sort desc')-> page($page['curpage'] . ',' . $page['size']) -> select();
        $query = Db::table("itboye_user_picture")->alias("pic")
            ->field($field)
            ->join(["common_datatree"=>"dt"],'dt.code = pic.type',"LEFT");
        $count = $query -> where($map) -> count();
        // 查询满足要求的总记录数
        $Page = new \Page($count, $page['size']);

        //分页跳转的时候保证查询条件
        if ($params !== false && is_array($params)) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        return $this -> apiReturnSuc(array("show" => $show, "list" => $list));
    }


}