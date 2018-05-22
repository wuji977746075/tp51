<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
/**
 * 生成假数据对象列表
 * eg:
 * $map = ['id'=>['numberBetween',[1,99]],'name'=>['firstName',[]]];
 * getFaker($map,rand(1,5));
 * return faker object list
 */
// require '../extend/faker/autoload.php';
// function getFaker(array $rules,$count=1){
//   // vendor('faker.autoload');
//   $faker = \Faker\Factory::create('zh_CN');
//   $r = [];
//   for ($i=0; $i < $count; $i++) {
//     $map = [];
//     foreach ($rules as $k => $v) {
//       $map[$k] = call_user_func_array([$faker,$v[0]], $v[1]);
//     }
//     $r[] = $map;
//   }
//   return $r;
// }

function cache_get($key=''){
  !is_string($key) && $key=serialize($key);
  return Cache::get($key,null);
}

function cache_set($key='',$val='',$time=600){
   Cache::set($key,$val,$time);
}
/**
 * [cache_clear description]
 * @Author
 * @DateTime 2017-12-19T09:02:56+0800
 * @param    string $key    [description]
 * @param    boolean $return [description]
 * @return   [mixed] volid or cache_value
 */
function cache_clear($key='',$return=false){
  if($key){
    !is_string($key) && $key=serialize($key);
    if($return){
      return Cache::pull($key);
    }else{
      Cache::rm($key);
    }
  }else{
    Cache::clear;
  }
}

// 驼峰式 转下划线
function humpToLine($str,$flag='_'){
    $str = preg_replace_callback('/([A-Z]{1})/',function($matches){
        return $flag.strtolower($matches[0]);
    },$str);
    return $str;
}
/**
* msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
* $str:要截取的字符串
* $start=0：开始位置，默认从0开始
* $length：截取长度
* $charset=”utf-8″：字符编码，默认UTF－8
* $suffix=true：是否在截取后的字符后面显示省略号，默认true显示，false为不显示
* 调用如下
* {$vo.title|msubstr=5,5,'utf-8′,false}
* 解释：截取字符串$vo.title，从第5个字符开始，截取5个，编码为UTF-8，不显示省略号
 * @param  [type]  $str     [description]
 * @param  integer $start   [description]
 * @param  [type]  $length  [description]
 * @param  string  $charset [description]
 * @param  boolean $suffix  [description]
 * @return [type]           [description]
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=false){
 if(function_exists("mb_substr")){
  $r = mb_substr($str, $start, $length, $charset);
  return ($suffix && $r!=$str) ? $r.'...':$r;
 }elseif(function_exists('iconv_substr')) {
  $r = iconv_substr($str,$start,$length,$charset);
  return ($suffix && $r!=$str) ? $r.'...':$r;
 }
 $re['utf-8'] = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
 $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
 $re['gbk'] = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
 $re['big5'] = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
 preg_match_all($re[$charset], $str, $match);
 $slice = join("",array_slice($match[0], $start, $length));
 return ($suffix && $slice!=$str) ? $slice.'...':$slice;
}

define('_VIC_WORD_DICT_PATH_',__DIR__.'/../vendor/lizhichao/word/Data/dict.igb');
ini_set("memory_limit","100M");
function getFenci($w){
  // require php_igbinary.dll pecl
  $fc = new \Lizhichao\Word\VicWord('igb');
  //长度优先分词
  // $ar = $fc->getWord($w);
  //细切分
  // $ar = $fc->getShortWord($w);
  //自动 这种方法最耗时
  $ar = $fc->getAutoWord($w);
  return changeArrKey($ar);
}
/**
 * 获取链接
 * 传入U方法可接受的参数或以http开头的完整链接地址
 * @return 链接地址
 */
function getURL($str, $param = '') {
  if (trim($str) == '#') {
    return '#';
  }
  if (strpos($str, '?') === false) {
    $str = $str . '?' . $param;
  } else {
    $str = $str . '&' . $param;
  }
  if (strpos($str, "http") === 0) {
    return $str;
  }

  return U($str);
}


// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
  $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
  if (strpos($string, ':')) {
    $value = array();
    foreach ($array as $val) {
      list($k, $v) = explode(':', $val);
      $value[$k] = $v;
    }
  } else {
    $value = $array;
  }
  return $value;
}
/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
  $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
  for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
  return round($size, 2) . $delimiter . $units[$i];
}
/**
 * 结束时间距离现在时间 禁言用到
 * @param  [type] $time1    [description]
 * @param  [type] $dateline [description]
 * @return [type]           [description]
 */
function getDateInfo($time1,$dateline=NOW_TIME){
  $time = $time1-$dateline;
  if($time < 0){
    return "已结束";
  }else if($time <= 60){
    return "马上";
  }else if($time > 60 && $time <= 3600){
    return floor($time/60)."分钟";
  }else if($time > 3600 && $time <= 3600*24){
    return floor($time/3600)."小时";
  }else if($time > 3600*24){
    return date('Y-m-d',$dateline);
  }
}
//取出数组的某一列
function getArr($arr,$key_f='',$val_f=null){
  if(version_compare(PHP_VERSION,'5.5.0','>=')){
    return array_column($arr, $val_f,$key_f); //php5.5+
  }else{
    $r = [];
    foreach ($arr as $v) {
      if($val_f && isset($v[$val_f])){
        if($key_f && isset($v[$key_f])){
          $r[$v[$key_f]] = $v[$val_f];
        }else{
          $r[] = $v[$val_f];
        }
      }else{
        if($key_f && isset($v[$key_f])){
          $r[$v[$key_f]] = $v;
        }else{
          $r[] = $v;
        }
      }
    }
    return $r;
  }
}
//身份证验证
function isIdCard($id_number=''){
  return preg_match('/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/', $id_number);
}


function html_head_tip($html='',$pre=true){
  return '<blockquote class="layui-elem-quote head-tip">'.($pre ? '<strong><i class="fa fa-fw fa-info-circle"></i>提示：</strong>' : '').'<span style="display:inline-flex;">'.$html.'</span></blockquote>';
}

function html_return($url='',$msg='返回'){
  $url = $url ? $url : 'javascript:history.go(-1)';
    return '<a href="'.$url.'" class="layui-btn layui-btn-primary ml10">'.$msg.'</a>';
}
/**
 * @desc  im:十进制数转换成三十六机制数
 * @param (int)$num 十进制数
 * @return bool|string
 */
function get_36HEX($num) {
    $num = intval($num);
    if ($num <= 0)
        return 0;
    $charArr = array("0","1","2","3","4","5","6","7","8","9",'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $char = '';
    do {
        $key = ($num - 1) % 36;
        $char= $charArr[$key] . $char;
        $num = floor(($num - $key) / 36);
    } while ($num > 0);
    return $char;
}

//db_object to array
function Obj2Arr($r,$key=false){
  $l = [];
  foreach ($r as $v) {
    $data = $v->getData();
    if($key) $l[$data[$key]] = $data;
    else $l[] = $data;
  }
  return $l;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

// 头像地址
function avaUrl($uid,$size=120){
  return config('avatar_url').'?uid='.$uid.'&size='.$size;
}
// 图片地址
function imgUrl($id,$size=120){
  return config('picture_url').'?id='.$id.'&size='.$size;
}
// ajax 返回
function ajaxReturn($msg,$url='',$data = [],$count=0,$time=0,$code=0){
  $r = [];
  $r['code']  = intval($code); // 0:success,1+:error_code
  $r['msg']   = $msg ? $msg : ('操作'.($r['code'] ? '失败':'成功'));
  $r['url']   = $url;  //跳转地址
  $r['delay'] = $time; //跳转延时 ms
  $r['count'] = $count; //layui 列表数据有效
  $r['data']  = $data;
  return json($r);
}
// ajax 返回失败 有跳转则不延时
function ajaxReturnErr($msg,$url='',$code=1){
  return ajaxReturn($msg,$url,[],0,0,$code);
}
// ajax 返回成功 有跳转则延时
function ajaxReturnSuc($msg,$url='',$data=[],$count=0,$time=1500){
  return ajaxReturn($msg,$url,$data,$count,$time);
}

/**
 * 放到utils中，作为工具类
 * @brief 干掉emoji
 * @autho chenjinya@baidu.com
 * @param {String} $strText
 * @return {String} removeEmoji
 **/
function escapeEmoji($strText, $bool = false) {
  $preg = '/\\\ud([8-9a-f][0-9a-z]{2})/i';
  if ($bool == true) {
    $boolPregRes = (preg_match($preg, json_encode($strText, true)));
    return $boolPregRes;
  } else {
    $strPregRes = (preg_replace($preg, '', json_encode($strText, true)));
    $strRet = json_decode($strPregRes, JSON_OBJECT_AS_ARRAY);

    if ( is_string($strRet) && strlen($strRet) == 0) {
        return "";
    }

    return $strRet;
  }
}

// 账号密码加密
function think_ucenter_md5($str, $key = 'UCenter'){
  return '' === $str ? '' : md5(sha1($str) . $key);
}

// todo :
function addLog(){

}
/**
 * 自定义语言变量
 * @param $str  字符串
 * @param $dif  分割符
 * @param $add  链接符
 * @return string is8n字符串
 * add by zhouhou
 */
function LL($str='',$dif=' ',$add = ''){
    return implode($add,array_map('lang',explode($dif, trim($str))));
}
/**
 * lang() alias 方法别名
 * @param [type] $name [description]
 * @param array  $vars [description]
 * @param string $lang [description]
 */
function L($name, $vars = [], $lang = ''){
  return lang($name, $vars, $lang);
}
/**
 * 缺少参数函数别名
 * @Author
 * @DateTime 2016-12-13T10:20:27+0800
 * @param    [type]                   $name [description]
 */
function Llack($name,$throw=false){
  $r = LL('lack '.$name);
  if($throw){
    throw new \InvalidArgumentException($r);
  }
  return $r;
}
function Linvalid($name,$throw=false){
  $r = LL('invalid '.$name);
  if($throw){
    throw new \InvalidArgumentException($r);
  }
  return $r;
}
function returnErr($msg,$trans=false){
  if($trans) \think\Db::rollback();
  return ['status'=>false,'info'=>$msg];
}
function returnSuc($data){
  return ['status'=>true,'info'=>$data];
}