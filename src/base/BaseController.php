<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-12-24 14:27:22
 * Description : 控制器拦截器
 */

namespace src\base;
use think\Controller;
use src\base\traits\Trans;
use src\base\traits\Post;
use src\sys\core\SysConfigLogic as ConfigLogic;
use src\sys\core\SysDatatreeLogic as DatatreeLogic;
use src\sys\core\SysModelLogic as ModelLogic;
abstract class BaseController extends Controller{
  use Trans,Post;

  protected $delay = true; // 是否延迟 一次性
  protected $logic = null;
  protected $module_id = null; // common
  protected $model_id  = null; // common
  protected $session_id;
  protected $seo = null;
  protected $cfg = null;
  protected $ip = null;
  protected $business = '';
  protected $suc_url = '';
  protected $err_url = '';
  protected $page = ['page'=>1,'size'=>10];
  protected $sort = 'id desc';
  const ALLOW_DOMAIN = [
    "http://tp51",
    "http://cdn.my"
  ];

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

    //设置程序版本 - test
    $this->seo = [
      'title'       => config('site_title'),
      'keywords'    => config('site_keyword'),
      'description' => config('site_desc'),
    ];
    $this->cfg = [
      'theme'    => config('admin_theme'),
      'template' => 'df',
      // 'business' => '', // change _setLogic
    ];

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
    $this->_setLogic($this->model['dir']);
    // set page and sort
    $this->page = ['page'=>$this->_get('page/d',1),'size'=>$this->_get('size/d',10)];
    $this->sort = $this->_get('field','id').' '.$this->_get('order','desc');
    $this->init();
    $this->assign(['seo'=>$this->seo,'cfg'=>$this->cfg]);
    // !$this->logic && $this->error('需要配置主logic');
  }
  // set main login if possible
  protected function _setLogic($dir='') {
    // 模块 : sys
    $module_name = lcfirst($this->model['module_name']);
    $src_module = '\src\\'.$module_name.'\\';
    // [Module][Dir]Modal
    $c = ltrim_fix(lcfirst(CONTROLLER_NAME),$module_name);
    $c = $c ? $c : $module_name;
    $cs = explode('_',strtounderscore(CONTROLLER_NAME));
    $c1 = $dir ? $dir : (isset($cs[1]) ? $cs[1] : $cs[0]); // module/ + $dir/$c1
    $c2 = CONTROLLER_NAME;
    // $c2 = ucfirst($c); // module/$c1/ + $c2Logic
    if($module_name == 'sys'){
      $logicPath = $src_module.'core\\'.$c2.'Logic';
      if(!class_exists($logicPath)){ // 不为系统核心
        $logicPath = $src_module.$c1.'\\'.$c2.'Logic';
      }
    }else{
      $logicPath = $src_module.$c1.'\\'.$c2.'Logic';
    }
    $this->logicPath = $logicPath;
    if(class_exists($logicPath)){
      $this->logic = new $logicPath;
    }
    // echo $logicPath;die();
    $this->business = $this->model['title'];
    $this->cfg['business'] = $this->business;
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
    /**
   * 赋值页面标题值
   */
  protected function assignTitle($title){
    $this->seo['title'] = $title;
    $this->assign("seo",$this->seo);
  }
  protected function _setAjax(){
    $req = $this->request;
    $sDomain = $req->domain();
    if (in_array($sDomain,self::ALLOW_DOMAIN)) {
      header('Access-Control-Allow-Origin:*');
      header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
      header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, sessionId");
    }
    header('X-Powered-By:'.POWER);

    $header = $req->header();
    $sessionId = isset($header['sessionid']) ? $header['sessionid'] : null;
    !empty($sessionId) && session_id($sessionId);
  }
  /* 空操作，用于输出404页面 */
  public function _empty() {
    header('HTTP/1.1 404 Not Found');
    header("status: 404 Not Found");
    header("Cache-Control: no-cache");
    header("Pragma: no-cache");
    if(!config('APP_DEBUG')){
      header('Location: '.__ROOT__. '/public/404.html');
    }else{
      throws("resource not found!",\ErrorCode::NOT_FOUND_RESOURCE);
      // echo '{"status": "404","msg": "resource not found!"}';
    }
    exit();
  }

  // 报错自动回滚事务/成功提交事务
  // require : trait Trans
  protected function ajaxRet($msg,$url='',$data = [],$count=0,$time=0,$code=0){
    if($this->trans){
      if($code){ $this->rollback();
      }else{     $this->commit();
      }
    }
    $r = ['time'=>NOW_TIME];
    $time = $this->delay ? $time : 0;
    $this->delay = true;
    // 0:success,1+:error_code
    $iCode = intval($code);
    $sMsg  = $msg ? $msg : LL('op '.($r['code'] ?'err':'suc'));

    $r['url']   = $url;   //跳转地址
    $r['delay'] = $time;  //跳转延时
    $r['count'] = $count; //layui 列表数据有效
    $r['data']  = $data;
    // json($r)->send();die(0);
    // echo json_encode($r);die(0);
    throws($sMsg,$iCode,$r);
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




  abstract protected function _parsePara($str,$need=false);
  /**
   * 合并必选和可选参数并返回
   * $str: 需要检查的post参数
   * $oth_str: 不需检查的post参数
   */
  protected function _getPara($str='',$oth_str=''){
      return array_merge($this->_parsePara($str,true),$this->_parsePara($oth_str,false));
  }

}
