<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use src\sys\core\ConfigLogic;
use src\sys\core\DatatreeLogic;
use src\sys\core\ModelLogic;

class Base extends Controller{
  protected $logic = null;
  protected $module_id = null; // common
  protected $model_id  = null; // common
  protected $session_id;
  protected $trans = 0;
  protected $suc_url = '';
  protected $err_url = '';
  protected $delay = true; // 是否延迟 一次性
  protected $page = ['page'=>1,'size'=>10];
  protected $sort = 'id desc';
  protected $business = '';
  protected $seo = null;
  protected $cfg = null;
  protected $ip = null;

  protected function trans() {
    $this->trans = intval($this->trans) + 1;
    Db::startTrans();
  }
  // ajaxRet : $this->trans >0
  // suc err opSuc opErr 等ajax操作自动提交
  protected function commit() {
    $this->trans = intval($this->trans) - 1;
    // $this->trans = 0 ;
    Db::commit();
  }
  // ajaxRet : $this->trans >0
  // suc err opSuc opErr 等ajax操作自动提交
  protected function rollback() {
    $this->trans = intval($this->trans) - 1;
    // $this->trans = 0 ;
    Db::rollback();
  }
  //初始化
  protected function initialize(){
    $this->_setAjax();
    session("?session_id");
    $this->session_id = session_id();
    empty($this->session_id) && $this->error("Session 未初始化");

    // 缓存最新 config(app.)
    (new ConfigLogic)->clearCache();
    // get datatrees
    (new DatatreeLogic)->clearCache();

    $this->_checkIp();
    !defined('PRE') && define('PRE',config('table_df_pre'));

    // get modals
    $models = (new ModelLogic)->queryCache(false);
    $models = getArr($models,'id');
    !$this->model_id && throws('未知模型');
    !isset($models[$this->model_id]) && throws('未知模块');
    $this->model     = $models[$this->model_id];
    $this->module_id = $this->model['module_id'];
    // require upper
    $this->_defined();
    // set main logic : require upper
    $this->_setLogic();

    //设置程序版本 - test
    $this->seo = [
      'title'       => config('site_title'),
      'keywords'    => config('site_keyword'),
      'description' => config('site_desc'),
    ];
    $this->cfg = [
      'theme'    => config('admin_theme'),
      'template' => 'df',
      'business' => $this->business,
    ];
    $this->assign(['seo'=>$this->seo,'cfg'=>$this->cfg]);

    // set page and sort
    $this->page = ['page'=>$this->_get('page/d',1),'size'=>$this->_get('size/d',10)];
    $this->sort = $this->_get('field','id').' '.$this->_get('order','desc');
    $this->init();
    // !$this->logic && $this->error('需要配置主logic');
  }
  // set main login if possible
  protected function _setLogic($dir='') {
    $logicPath = '\src\\sys\core\\'.CONTROLLER_NAME.'Logic';
    if(class_exists($logicPath)){ // 是否为系统的
      $this->logic = new $logicPath;
    }else{
      $module_name = $this->model['module_name'].'\\';
      $logic_dir = $dir ? $dir : lcfirst(CONTROLLER_NAME);

      $logicPath = '\src\\'.$module_name.$logic_dir.'\\'.CONTROLLER_NAME.'Logic';
      if(class_exists($logicPath)){
        $this->logic = new $logicPath;
      }else{
        $logic_dir = $module_name.preg_replace('/([A-Z]{1}.*)?/', '', $logic_dir);
        $logicPath = '\src\\'.$logic_dir.'\\'.CONTROLLER_NAME.'Logic';
        if(class_exists($logicPath)){
          $this->logic = new $logicPath;
        }
      }
    }

    // echo $logicPath;die();
    $this->business = $this->model['title'];
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
    $theme = $theme ? $theme : $this->cfg['theme'];
    if(!empty($file)){
      return $this->fetch($theme.'/'.$file,[],[
        'filter' => 'tpl_filter'
      ]);
    }else{
      return $this->fetch($theme."/". request()->controller().'/'.request()->action(),[],[
        'filter' => 'tpl_filter'
      ]);
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
   * 检测IP访问
   */
  protected function _checkIp() {
    $this->ip = get_client_ip();
    $allowIp  = config('admin_allow_ips');
    $banIp    = config('admin_ban_ips');
    // ? 允许访问
    if ($allowIp && !in_array($this->ip, explode(',', $allowIp))) {
      retCode(403);
    }
    // ? 拒绝访问
    if ($banIp && in_array($this->ip, explode(',', $banIp))) {
      retCode(403);
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
  /**
   * 赋值页面标题值
   */
  protected function assignTitle($title){
    $this->seo['title'] = $title;
    $this->assign("seo",$this->seo);
  }
  protected function _setAjax(){
    header('Access-Control-Allow-Origin:*');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, sessionId");
    header('X-Powered-By:'.POWER);

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

    if(!defined("MODEL_ID")){
      define("MODEL_ID", $this->model_id);
      define("MODEL", $this->model);
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

  // 报错自动回滚事务/成功提交事务
  protected function ajaxRet($msg,$url='',$data = [],$count=0,$time=0,$code=0){
    if($this->trans){
      if($code){ $this->rollback();
      }else{     $this->commit();
      }
    }
    $r = [];
    $time = $this->delay ? $time : 0;
    $this->delay = true;
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
  protected function opErr($msg='',$url='',$data=[],$time=0,$ajax=false){
    // echo $msg; echo $url;
    if(IS_AJAX || $ajax){
      $this->err($msg,$url);
    }else{
      if($this->trans) $this->rollback();
      $this->error($msg,$url,$data,ceil($time/1000));
    }
  }
  // 成功 有跳转则延时
  protected function suc($msg='',$url='',$data=[],$count=0,$time=3000){
    $msg = $msg ? $msg : LL('op suc');
    $url = $url ? $url : $this->suc_url;
    $count = $count ? $count : count($data);
    $this->ajaxRet($msg,$url,$data,$count,$time);
  }
  protected function opSuc($msg='',$url='',$time=3000){
    if(IS_AJAX){
      $this->suc($msg,$url,[],0,$time);
    }else{
      if($this->trans) $this->commit();
      $this->success($msg,$url,[],ceil($time/1000));
    }
  }

  // 数据库返回 / apiReturn
  protected function checkOp($r,$suc='',$err='',$time=3000){
    if(is_array($r)){
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
      if(is_string($val)){
        $val = trim($val);
        if($df == $val && !empty($emptyErr)){
            $this->err($emptyErr);
        }
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
    if(is_string($val)){
      $val = trim($val);
      if($df == $val && !empty($emptyErr)){
        $this->err($emptyErr);
      }
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
    if(is_string($val)){
      $val = trim($val);
      if($df == $val && !empty($emptyErr)){
        $this->err($emptyErr);
      }
    }
    return $val;
  }
}