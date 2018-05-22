<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/10/26
 * Time: 14:31
 */

namespace app\index\controller;

use think\Controller;
// use Think\Controller\RestController;
use php\Uploader;
use src\file\UserPictureLogic;

class Ueditor {

    private $CONFIG;
    private $url ;// 当前上传图片保存到的域名

    /**
     * 获取相对网站目录的地址
     * @param $path
     * @return string
     */
    private function getRelativePath($path){
        return __ROOT__.$path;
    }

    public function upload(){

        date_default_timezone_set("Asia/chongqing");
        error_reporting(E_ERROR);
        header("Content-Type: text/html; charset=utf-8");

        $config_path = APP_PATH.'../extend/php/config.json';
        $this->url = config('site_url'); //目前为 site_URL 即可
        $urlInfo = parse_url($this->url);
        $this->url = $urlInfo['scheme'].'://'.$urlInfo['host'];

        $this->CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($config_path)), true);

        $this->CONFIG['imagePathFormat'] = $this->getRelativePath($this->CONFIG['imagePathFormat']);
        $this->CONFIG['scrawlPathFormat'] = $this->getRelativePath($this->CONFIG['scrawlPathFormat']);
        $this->CONFIG['snapscreenPathFormat'] = $this->getRelativePath($this->CONFIG['snapscreenPathFormat']);
        $this->CONFIG['catcherPathFormat'] = $this->getRelativePath($this->CONFIG['catcherPathFormat']);
        $this->CONFIG['videoPathFormat'] = $this->getRelativePath($this->CONFIG['videoPathFormat']);
        $this->CONFIG['filePathFormat'] = $this->getRelativePath($this->CONFIG['filePathFormat']);
        $this->CONFIG['imageManagerListPath'] = $this->getRelativePath($this->CONFIG['imageManagerListPath']);
        $this->CONFIG['fileManagerListPath'] = $this->getRelativePath($this->CONFIG['fileManagerListPath']);

        $action = $_GET['action'];

        switch ($action) {
            case 'config':
                $result =  json_encode($this->CONFIG);
                break;

            /* 上传图片 */
            case 'uploadimage':
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $result = $this->action_upload();
                break;

            /* 列出图片 */
            case 'listimage':
            /* 列出文件 */
            case 'listfile':
                $result = $this->action_list();
                break;

            /* 抓取远程文件 */
//            case 'catchimage':
//                $result = include("action_crawler.php");
//                break;

            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
    }

    private function action_upload(){
        // vendor('Uploader',APP_PATH.'Api/Vendor/php/','.class.php');
        /* 上传配置 */
        $base64 = "upload";
        $action = htmlspecialchars($_GET['action']);
        $uid    = input('param.uid/d',0);
        switch ($action) {
            case 'uploadimage':
                $config = array(
                    "pathFormat" => $this->CONFIG['imagePathFormat'],
                    "maxSize" => $this->CONFIG['imageMaxSize'],
                    "allowFiles" => $this->CONFIG['imageAllowFiles']
                );
                $fieldName = $this->CONFIG['imageFieldName'];
                break;
            case 'uploadscrawl':
                $config = array(
                    "pathFormat" => $this->CONFIG['scrawlPathFormat'],
                    "maxSize" => $this->CONFIG['scrawlMaxSize'],
                    "allowFiles" => $this->CONFIG['scrawlAllowFiles'],
                    "oriName" => "scrawl.png"
                );
                $fieldName = $this->CONFIG['scrawlFieldName'];
                $base64 = "base64";
                break;
            case 'uploadvideo':
                $config = array(
                    "pathFormat" => $this->CONFIG['videoPathFormat'],
                    "maxSize" => $this->CONFIG['videoMaxSize'],
                    "allowFiles" => $this->CONFIG['videoAllowFiles']
                );
                $fieldName = $this->CONFIG['videoFieldName'];
                break;
            case 'uploadfile':
            default:
                $config = array(
                    "pathFormat" => $this->CONFIG['filePathFormat'],
                    "maxSize" => $this->CONFIG['fileMaxSize'],
                    "allowFiles" => $this->CONFIG['fileAllowFiles']
                );
                $fieldName = $this->CONFIG['fileFieldName'];
                break;
        }

        /* 生成上传实例对象并完成上传 */
        $up = new Uploader($fieldName, $config, $base64);

        /**
         * 得到上传文件所对应的各个参数,数组结构
         * array(
         *     "original" => "",       //原始文件名
         *     "size" => "",           //文件大小
         *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
         *     "title" => "",          //新文件名
         *     "type" => ""            //文件类型
         *     "url" => "",            //返回的地址
         * )
         */
        $fileinfo = $up->getFileInfo();
        if($action == 'uploadimage' && $fileinfo['state'] == 'SUCCESS'){
            // 上传图片正确
            $add = [
                'path'        => $fileinfo['url'],
                'uid'         => $uid,
                'ori_name'    => $fileinfo['original'],
                'savename'    => $fileinfo['title'],
                'size'        => $fileinfo['size'],
                'url'         => '',//图片链接
                'imgurl'      => $this->url.$fileinfo['url'],//完整显示地址
                'md5'         => $fileinfo['md5'],
                'sha1'        => $fileinfo['sha1'],
                'type'        => 'ueditor',
                'ext'         => ltrim($fileinfo['type'],'.'),
                'create_time' => time(),
            ];
            $id = (new UserPictureLogic)->add($add);
        }
        /* 返回数据 */
        $fileinfo['url'] = $this->url.$fileinfo['url'];
        return json_encode($fileinfo);
    }

    private function action_list(){

        /* 判断类型 */
        $action = $_GET['action'];
        switch ($action) {
            /* 列出文件 */
            case 'listfile':
                $allowFiles = $this->CONFIG['fileManagerAllowFiles'];
                $listSize = $this->CONFIG['fileManagerListSize'];
                $path = $this->CONFIG['fileManagerListPath'];
                break;
            /* 列出图片 */
            case 'listimage':
            default:
                $allowFiles = $this->CONFIG['imageManagerAllowFiles'];
                $listSize = $this->CONFIG['imageManagerListSize'];
                $path = $this->CONFIG['imageManagerListPath'];
        }
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = intval($start) + intval($size);

        /* 获取文件列表 */
        $path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;

        if($action == 'listimage'){
            // 从本地获取图片 2018-05-22 16:46:29
            $list = (new UserPictureLogic)->queryCount(['status'=>1],['start'=>$start,'size'=>$size],'id desc',[],'id,imgurl as url,create_time as mtime');
            $result = json_encode(array(
                "state" => "SUCCESS",
                "list" => $list['list'],
                "start" => $start,
                "total" => $list['count']
            ));
        }else{
            $files = $this->getfiles($path, $allowFiles);
            if (!count($files)) {
                return json_encode(array(
                    "state" => "no match file",
                    "list" => array(),
                    "start" => $start,
                    "total" => count($files)
                ));
            }
            /* 获取指定范围的列表 */
            $len = count($files);
            for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
                $list[] = $files[$i];
            }
            /* 返回数据 */
            $result = json_encode(array(
                "state" => "SUCCESS",
                "list" => $list,
                "start" => $start,
                "total" => count($files)
            ));

        }
        return $result;
    }

    /**
 * 遍历获取目录下的指定类型的文件
 * @param $path
 * @param array $files
 * @return array
 */
    function getfiles($path, $allowFiles, &$files = array())
    {
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getfiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                        $files[] = array(
                            'url'=> $this->url.substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }

}