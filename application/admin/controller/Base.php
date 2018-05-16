<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use src\config\ConfigLogic;

class Base extends Controller{

  protected $logic = null;
  protected $module_id = 2;
  protected $session_id;
  protected $trans = false;
  protected $suc_url = '';
  protected $err_url = '';
  protected $page = ['page'=>1,'size'=>10];
  protected $sort = 'id desc';
  protected $seo  = [
    'title'       =>'',
    'keywords'    =>'',
    'description' =>'',
  ];
  protected $cfg = [
    'owner'=>'rainbow',
    'theme'=>'df'
  ];

  protected function trans(){
    $this->trans = true;
    Db::startTrans();
  }
  //初始化
  protected function initialize(){
    $this->_setAjax();
    session("?session_id");
    $this->session_id = session_id();
    empty($this->session_id) && $this->error("Session 未初始化");

    // 查询设置系统配置
    $sysConfig = (new ConfigLogic)->queryGroup(ConfigLogic::SYSTEM,0);
    \Config::set(['app'=>array_merge($sysConfig,config('app.'))]);

    //设置程序版本 - test
    $this->_assignVars(['title'=>config('site_title'),'keywords'=>config('site_keyword'),'description'=>config('site_desc')],['theme'=>config('admin_theme')]);
    $this->_defined();

    // set page and sort
    $this->page = ['page'=>$this->_get('page/d',1),'size'=>$this->_get('size/d',10)];
    $this->sort = $this->_get('field','id').' '.$this->_get('sort','desc');
    // set main logic
    $this->setLogic();
    $this->init();
    // !$this->logic && $this->error('需要配置主logic');
  }
  // set main login if possible
  protected function setLogic($dir='') {
    $logic_dir = $dir ? $dir : lcfirst(CONTROLLER_NAME);
    $logicPath = '\src\\'.$logic_dir.'\\'.CONTROLLER_NAME.'Logic';
    if(class_exists($logicPath)){
      $this->logic = new $logicPath;
    }else{
      $logic_dir = preg_replace('/([A-Z]{1}.*)?/', '', $logic_dir);
      $logicPath = '\src\\'.$logic_dir.'\\'.CONTROLLER_NAME.'Logic';
      if(class_exists($logicPath)){
        $this->logic = new $logicPath;
      }
    }
  }

  protected function init(){
  }
  // protected function checkLogin(){
  //   return session('uid');
  // }

  // protected function getLoginUid(){
  //   return 1;
  // }

  protected function show($file='',$theme=''){
    if(false !== $theme) $theme = 'df';
    if(!empty($file)){
      return $this->fetch($theme.'/'.$file);
    }else{
      return $this->fetch($theme."/". request()->controller().'/'.request()->action());
    }
  }
  //解析分页api分页返回 - 并导入数据到模板
  protected function retPager($r,$url='',$param=[]){
    $this->assign('list',$r['info']['data']);
    $total = $r['info']['total'];
    // $total = ceil($total/$r['info']['per_page']);
    $show = $this->getPager($total,$r['info']['per_page'],$url,$param);
    $this->assign('show',$show);
  }

  /**
   * 根据配置类型解析配置
   * @param  integer $type  配置类型
   * @param  string  $value 配置值
   */
  private static function parse($type, $value) {
    switch ($type) {
      case 3 :
        //解析数组
        $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
        if (strpos($value, ':')) {
          $value = array();
          foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
          }
        } else {
          $value = $array;
        }
        break;
    }
    return $value;
  }

  /**
   * 检测IP是否在运行访问的IP里
   */
  public function checkAllowIP() {
    $allowIP = C('ADMIN_ALLOW_IP');
    if (!IS_ROOT && $allowIP) {
      // 检查IP地址访问
      if (!in_array(get_client_ip(), explode(',', $allowIP))) {
        $this -> error('403:禁止访问');
      }
    }
  }
  //构建分页条字符串
  protected function getPager($count,$size,$base_url='',$param=array()){
    $Page = new \Page($count, $size);
    //分页跳转的时候保证查询条件
    if (false  !== $param) {
      foreach ($param as $key => $val) {
        $Page -> parameter[$key] = urlencode($val);
      }
    }
    // 实例化分页类 传入总记录数和每页显示的记录数
    $show = $Page -> show();
    return $show;
  }

  protected function getApi($type,$data,$ver=100,$checkStatus = true){
    $t = array(
      'api_ver'  => $ver,
      'notify_id'=> time(),
      'alg'      => 'md5',
      'type'     => $type,
    );
    if(substr($type,0,3)=='AM_'){
      $service = new BoyeServiceApi('admin');
      $t['aid']= UID;
    }else{
      $service = new BoyeServiceApi();
    }

    $data = array_merge($t, $data);
    $r = $service->callRemote("",$data,false);
    //check error
    if(!isset($r['status'])) $this->error('操作错误或异常');//exit($r);
    if($checkStatus && !$r['status']) $this->error($r['info']);
    return $r;
  }

  protected function assignVars($seo=['title'=>'标题','keywords'=>'关键词','description'=>'描述',],  $cfg=['owner'=>''])
  {
    $this->_assignVars($seo);
  }
  /*
   * Seo 配置
   * */
  protected function _assignVars($seo=['title'=>'标题','keywords'=>'关键词','description'=>'描述',],  $cfg=['owner'=>'']){
    $this->seo = array_merge($this->seo,$seo);
    $this->cfg = array_merge($this->cfg,$cfg);

    $this->assign("seo",$this->seo);
    $this->assign("cfg",$this->cfg);
  }
  /**
   * 赋值页面标题值
   */
  protected function assignTitle($title){
    $this->seo = array_merge($this->seo,array('title'=>$title));
    $this->assign("seo",$this->seo);
  }
  protected function _setAjax(){
    header('Access-Control-Allow-Origin:*');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, sessionId");

    $header = $this->request->header();
    $sessionId = isset($header['sessionid']) ? $header['sessionid'] : null;

    if(!empty($sessionId)) {
        session_id($sessionId);
    }
  }

  protected function _defined(){
    if(!defined("CONTROLLER_NAME")){
      define("CONTROLLER_NAME", $this->request->controller());
    }

    if(!defined("ACTION_NAME")){
      define("ACTION_NAME", $this->request->action());
    }

    if(!defined("IS_POST")){
      define("IS_POST", $this->request->isPost());
    }

    if(!defined("IS_GET")){
       define("IS_GET", $this->request->isGet());
    }

    if(!defined("IS_AJAX")){
      define("IS_AJAX", $this->request->isAjax());
    }
  }

  /* 空操作，用于输出404页面 */
  public function _empty() {
    header('HTTP/1.1 404 Not Found');
    header("status: 404 Not Found");
    header("Cache-Control: no-cache");
    header("Pragma: no-cache");
    if(!defined('DEBUG')){
      header('Location: '.__ROOT__. '/public/404.html');
    }else{
      echo '{"status": "404","msg": "resource not found!"}';
      exit();
    }
  }

  protected function ajaxRet($msg,$url='',$data = [],$count=0,$time=0,$code=0){
    if($this->trans){
      $this->trans = false;
      if($code) Db::rollback();
      else Db::commit();
    }
    $r = [];
    $r['code']  = intval($code); // 0:success,1+:error_code
    $r['msg']   = $msg ? $msg : LL('op '.($r['code'] ?'err':'suc'));
    $r['url']   = $url;   //跳转地址
    $r['delay'] = $time;  //跳转延时
    $r['count'] = $count; //layui 列表数据有效
    $r['data']  = $data;
    // json($r)->send();die(0);
    echo json_encode($r);die(0);
  }
  // 失败 有跳转则不延时
  protected function err($msg,$url='',$code=1){
    $msg = $msg ? $msg : LL('op fail');
    $url = $url ? $url : $this->err_url;
    $this->ajaxRet($msg,$url,[],0,0,$code);
  }
  protected function opErr($msg=''){
    $this->err($msg);
  }
  // 成功 有跳转则延时
  protected function suc($msg='',$url='',$data=[],$count=0,$time=1500){
    $msg = $msg ? $msg : LL('op suc');
    $url = $url ? $url : $this->suc_url;
    $this->ajaxRet($msg,$url,$data,$count,$time);
  }
  protected function opSuc($msg='',$url='',$time=1500){
    $this->suc($msg,$url,[],0,$time);
  }

  // 数据库返回 / apiReturn
  protected function checkOp($r,$suc='',$err='',$time=1500){
    if($r){
      if(isset($r['count']) && isset($r['list'])){
        $this->suc($suc,'',$r['list'],$r['count'],$time);
      }elseif(isset($r['status'])){
        $r['status'] && $this->suc($suc,'',$r,count($r),$time);
      }else{
         $this->suc($suc,'',$r,count($r),$time);
      }
    }
    $this->opErr($err);
  }

  /**
   * 获取post参数并返回
   * $need:是否必选
   * a|0|int   默认0
   * a         默认''
   * a|p       默认'p'
   * a||mulint 数字','链接字符串
   * a||float  小数
   * @DateTime 2016-12-13T10:25:17+0800
   * @param    [type]                   $str  [description]
   * @param    boolean                  $need [description]
   * @return   [type]                         [description]
   */
  protected function _parsePara($str,$need=false){
      if(empty($str) || !is_string($str)) return [];
      $r = [];
      $arr = explode(',', $str);
      $data = input('param.');
      $jsf = $this->jsf;
      foreach ($arr as $v) {
          //补全预定义
          $p = explode('|', $v);
          !isset($p[1]) && $p[1]='';   //默认值空字符串
          !isset($p[2]) && $p[2]='str';//默认类型字符串
          $key = $p[0];
          $err = isset($jsf[$p[0]]) ? $jsf[$p[0]] : $p[0];
          //_post number bug
          // if($need) $post = $this->_post($p[0],$p[1],Llack($p[0]));
          // else  $post = $this->_post($p[0],$p[1]);
          // fix bug
          !isset($data[$key]) && $data[$key]='';

          if($need && $data[$key] === ''){
              $this->err(Llack($err));
          }
          $post = $data[$key]==='' ? $p[1] : $data[$key];
          if($p[2] === 'int'){
              $post = intval($post);
          }elseif($p[2] === 'float'){
              $post = floatval($post);
          }elseif($p[2] === 'mulint'){
              $post = array_unique(explode(',', $post));
              $temp = [];
              foreach ($post as $v) {
                  if(is_numeric($v)){
                      $temp[] = $v;
                  }else{
                      $this->err(Linvalid($err));
                  }
              }
              $post = implode(',', $temp);
          }
          $r[$p[0]] = $post;
      }
      return $r;
  }
  /**
   * 合并必选和可选参数并返回
   * $str: 需要检查的post参数
   * $oth_str: 不需检查的post参数
   */
  protected function _getPara($str='',$oth_str=''){
      return array_merge($this->_parsePara($str,true),$this->_parsePara($oth_str,false));
  }

  // 推荐
  protected function _param($key,$df='',$emptyErr=''){
      $val = input('param.'.$key,$df);
      if($df == $val && !empty($emptyErr)){
          $this->err($emptyErr);
      }
      return $val;
  }

  /**
   * @param $key
   * @param string $df
   * @param string $emptyErr  为空时的报错
   * @return mixed
   */
  protected function _post($key,$df='',$emptyErr=''){
    $val = input('post.'.$key,$df);
    if($df == $val && !empty($emptyErr)){
      $this->err($emptyErr);
    }
    return $val;
  }

  /**
   * 路由参数取不值 非?参数取不到
   * @param $key
   * @param string $df
   * @param string $emptyErr  为空时的报错
   * @return mixed
   */
  protected function _get($key,$df='',$emptyErr=''){
    $val = input('get.'.$key,$df);
    if($df == $val && !empty($emptyErr)){
      $this->err($emptyErr);
    }
    return $val;
  }
}