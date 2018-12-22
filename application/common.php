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

function getArrKey($r,$key,$err='invalid array key'){
  if($err && !isset($r[$key])) throws($err.':'.$key);
}

// 获取某个name的配置值,解析后返回
function getConfig($key='',$time=600) {
  return (new \src\sys\core\SysConfigLogic)->getConfig($key);
}
function getDatatree($name='',$k='id',$v='title',$time=600) {
  $c = (new \src\sys\core\SysDatatreeLogic)->getDatatree($name,$k,$v);
  return $c;
}
function getUserById($id=0,$field='name',$null='') {
  $temp = $id  ? (new \src\user\user\UserLogic)->getAllInfo($id) : [];
  return isset($temp[$field]) ? $temp[$field] : $null;
}
// seems not work ...
// ob_start('ob_gzip');
// echo $s;
// end or ob_end_flush();
function ob_gzip($content){
  if(!headers_sent() && extension_loaded("zlib") && strstr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip")){
    $content = gzencode($content,5);
    // $content = gzcompress($content);bzcompress($content)
    header("Content-Encoding: gzip");
    header("Vary: Accept-Encoding");
    header("Content-Length: ".strlen($content));
  }
  return $content;
}

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

// trim 第二个参数 不能带非空
function ltrim_fix($str='',$s='') {
  return preg_replace('/^'.$s.'(\w*?)$/', "$1", $str);
}
/**
  * 下划线转驼峰
  * 思路:
  * step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
  * step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
  */
function strtocamel($str,$sep='_') {
  $str = $sep. str_replace($sep, " ", strtolower($str));
  return ltrim(str_replace(" ", "", ucwords($str)), $sep );
}
/**
  * 驼峰命名转下划线命名
  */
function strtounderscore($str,$sep='_') {
  return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $sep . "$2", $str));
}
// 请在输出前过滤 or 保存时html实体转码
// Thinkphp5.1 Think\Template.php  : 398
// $content = call_user_func_array('tpl_filter',[$content]);
// todo : gzip +
function tpl_filter($s) {
  // $this->view->filter(function($s){
  $n = PHP_EOL;
  // php   /*.*/ //  不用去 php_fpm : load_comment/.
  // html  <!--.-->
  // css   /*.*/
  // js    /*.*/ //
  // 统一换行 \n
  $s = str_replace(["\r\n", "\r"], "\n", $s);
  // 去掉多行注释
  // /*.*/ <!--.-->(<!--[if ]开头的除外)
  $s = preg_replace("#((/\*.*?\*/)|(<!--(?!\[if )(.|\n)*?-->))(\n){0,}#u", "", $s);
  // 去掉js单行注释 //(行//或 空格//)
  $s = preg_replace("#\n#u","\n\n",$s); // 必须
  $s = preg_replace(["#\n//.*?\n#u","#\n(.*?)( |\t|}|\)|;)//.*?\n#u"],["\n\n","\n$1$2  \n"],$s);
  // 空格换行合并
  $s = preg_replace(["# {2,}#u","#\s*\n\s*#u"],[" ","\n"],$s);
  // 去掉某些换行
  // $s = preg_replace(["#,\n#u","#\n,#u","#;\n#u","#\{\n#u","#>\n+<#"],[",",",",";","{ ","><"],$s);
  // bug : to optimize
  // $s = preg_replace("#\}\n#u","} ",$s);
  return $s;
  // });
}
/**
 * 获取时间戳
 * @param  mixed $time  int,string
 * @return int          时间戳
 */
function fixTimeStamp($time=0,$thr=false){
  $ret = 0;
  if($time){// 非空
    if(is_numeric($time)){//数字
      $ret = (int) $time;
    }else{ // 字符串
      $ret = strtotime($time);
      if(false === $ret){
        $thr && throws('时间格式错误');
      }
    }
  }
  return $ret;
}

// return http_err_code and exit
function retCode($code,$msg='',$data=[]){
  header('HTTP/1.1 '.$code);
  // //$code = (int) $code;
  // $msgs = [ // 其他待测 -> http
  //   301=>'Moved Permanently',
  //   304=>'Not Modified',
  //   401=>'Unauthorized',
  //   403=>'Forbidden',
  //   404=>'Not Found',
  // ];
  // $msg || $msg = (isset($msgs[$code]) ? $msgs[$code] : '');
  // header('HTTP/1.1 '.$code);
  // echo $msg;
  exit;
}
// throw BaseException
function throws($msg='excetion',$code=-1,$data=[]){
  if(defined('BIND_MODULE')){
    $a = BIND_MODULE;
    if($a == 'index'){
      $e = '\src\base\exception\ApiException';
    }else{
      $e = '\src\base\exception\BaseException';
    }
  }
  throw new $e($msg,$code,$data);
  // throw new \think\Exception($msg,$code);
}
// 全空需在外面处理
function getWhereTime($field,$start='',$end=''){
  empty($field) && throws('invalid:where_time_call_1');
  // $start = fixTimeStamp($start);
  // $end = fixTimeStamp($end);
  if($start){
    if($end){ // start-end
      $map = [$field,'between time',[$start,$end]];
    }else{ // start-
      $map = [$field,'>=',$start];
    }
  }else{
    if($end){ // -end
      $map = [$field,'<=',$end];
    }else{ // ~-~
      throws('invalid:where_time_call_2');
    }
  }
  return $map;
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

/**
 * 中文分词处理方法
 *+---------------------------------
 * @author Nzing
 * @access public
 * @version 1.0
 *+---------------------------------
 * @param stirng  $string 要处理的字符串
 * @param boolers $sort=false 根据value进行倒序
 * @param Numbers $top=0 返回指定数量，默认返回全部
 *+---------------------------------
 * @return void
 */
function scws($text, $top = 5, $return_array = false, $sep = ',') {
    // include('./pscws4.php');
    $cws = new \pscws4\pscws4('utf-8');
    $cws -> set_charset('utf-8');
    $cws -> set_dict('../extend/pscws4/etc/dict.utf8.xdb');
    $cws -> set_rule('../extend/pscws4/etc/rules.utf8.ini');
    //$cws->set_multi(3);
    $cws -> set_ignore(true);
    //$cws->set_debug(true);
    //$cws->set_duality(true);
    $cws -> send_text($text);
    $ret = $cws -> get_tops($top, 'r,v,p');
    $result = null;
    foreach ($ret as $value) {
        if (false === $return_array) {
            $result .= $sep . $value['word'];
        } else {
            $result[] = $value['word'];
        }
    }
    return false === $return_array ? substr($result, 1) : $result;
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

function getLastSql(){
  return \think\Db::getLastSql();
}
// 头像地址
function avaUrl($uid,$size='',$fresh=true){
  return config('avatar_url').'?uid='.$uid.( $size ? '&size='.$size : '').( $fresh ? '&fresh=1' : '');
}
// 图片地址
function imgUrl($id,$size='',$fresh=true){
  return config('picture_url').'?id='.$id.( $size ? '&size='.$size : '').( $fresh ? '&fresh=1' : '');
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

// 账号密码加密 - hash php5.5+
function getPassHash($pass='',$alg=PASSWORD_DEFAULT,array $options=[]){
  return password_hash($pass,$alg,$options);
}
// 账号密码验证 - hash php5.5+
function checkPassHash($pass,$hash){
  return password_verify($pass,$hash);
}
// 账号密码加密 - md5
function think_ucenter_md5($str, $key = 'UCenter'){
  return '' === $str ? '' : md5(sha1($str) . $key);
}

// todo :
function addLog(){

}

// todo : 转移
function getKeyStatus($ks){
  switch ($ks) {
    case 110401:
      return '正常使用';
    case 110402:
      return '待接受';
    case 110405:
      return '已冻结';
    case 110408:
      return '已删除';
    case 110410:
      return '已重置';
    default:
      return '未知';
  }
}
function getKeyType($ks){
  switch ($ks) {
    case 0:
      return '管理员';
    case 1:
      return '用户';
    case 2:
      return '租户管理员';
    case 3:
      return '租户用户';
    default:
      return '未知'.$ks;
  }
}
// todo :
function addTestLog($get,$post='',$ext=''){
    $model = \think\Db::table('test_log');
    $get  = $get  ? var_export($get,true) :'null';
    $post = $post ? var_export($post,true):'null';
    $ext  = $ext  ? var_export($ext,true) :'null';
    $entry = [
        'get'        =>$get,
        'post'       =>$post,
        'ext'        =>$ext,
        'create_time'=>date('Y-m-d H:i:s',time()),
    ];
    return $model ->insertGetId($entry);
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

function cslog($val){
    $debug = debug_backtrace(); //调用堆栈
    unset($debug[0]['args']);
    echo '<script> try{',
         'console.log('. json_encode(str_repeat ( "~~~" ,  40 )). ');',
         'console.log('. json_encode($debug[0]). ');',
         'console.log('. json_encode($val). ');',
         'console.log('. json_encode(str_repeat ( "~~~" ,  40 )). ');',
         '}catch(e){}</script>';
}
/**
 * lang() alias 方法别名
 * @param [type] $name [description]
 * @param array  $vars [description]
 * @param string $lang [description]
 */
function L($name, $vars = [], $lang = ''){
  return Lang::has($name) ? lang($name, $vars, $lang) : '';
}
/**
 * 缺少参数函数别名
 * @Author
 * @DateTime 2016-12-13T10:20:27+0800
 * @param string|exception $lang [description]
 */
function Llack($name,$throw=false){
  $r = LL('lack '.$name);
  if($throw){
    throws($r,\ErrorCode::LACK_PARA);
  }
  return $r;
}
function Linvalid($name,$throw=false){
  $r = LL('invalid '.$name);
  if($throw){
    throws($r,\ErrorCode::INVALID_PARA);
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