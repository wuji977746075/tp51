<?php
namespace app\admin\controller;

use src\base\BaseController;

class Base extends BaseController {
  protected function init() {
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
  //解析分页api分页返回 - 并导入数据到模板
  protected function retPager($r,$url='',$param=[]){
    $this->assign('list',$r['info']['data']);
    $total = $r['info']['total'];
    // $total = ceil($total/$r['info']['per_page']);
    $show = $this->getPager($total,$r['info']['per_page'],$url,$param);
    $this->assign('show',$show);
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

}