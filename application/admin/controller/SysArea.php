<?php
namespace app\admin\controller;

class SysArea extends CheckLogin{
  protected $model_id = 10;
  protected $banEditFields = ['parent','id'];

  // edit / add
  public function set(){
    $this->jsf = [
      'code'     =>'编码',
      'province' =>'省份',
      'city'     =>'城市',
      'district' =>'市区',
    ];
    if(IS_GET){ // view
      $this->jsf_tpl = [
        ['code'],
        ['province','input-long'],
        ['city','input-long'],
        ['district','input-long'],
        ['sort|number'],
      ];
    }else{ // save
      $this->jsf_field = ['code','province,city,district,sort|0'];
    }
    return parent::set();
  }

  // 设置
  function house() {
    // session('accode', null);
    $logic = $this->logic;
    // $logic = new \src\sys\area\SysAreaLogic;
    if(IS_POST) {
      $code = $this->_param('code','');
      if($code){
        // is city ?
        $info = $logic->getInfo(['code'=>$code],false,'code,is_open,city','城市id错误');
        if(0 == $info['is_open']) $this->err('请先开通'.$code);
        session('accode', $info['code']);
        session('acname', $info['city']);
        $req_uri = session('req_uri');
        if(empty($req_uri)) $req_uri = url('House/index');
        // header('Location: '.$req_uri);exit;
        $this->suc_url = $req_uri;
        $this->opSuc();
      }else{
        $this->err('请选择城市');
      }
    }else{
      //查询已开通城市
      $map = [['is_open','=',1],['city','<>','']];
      $r   = $logic->query($map,'id desc','id,code,city');
      $this->assign('list',$r);
      return parent::index();
    }
  }

  public function index(){
    $parent = $this->_param('parent/d',1);
    $up     = $this->_param('up/d',0); //  ? parent的上级
    if($parent && $up){
      $r = $this->logic->getInfo(['id'=>$parent]);
      if($r) $parent = $r['parent'];
    }
    if($parent){
      $r = $this->logic->getInfo(['id'=>$parent]);
      $name = $r['city'] ? $r['city'] : $r['province'];
    }
    $this->assign('parent',$parent);
    return $this->show();
  }


  // ajax 返回菜单单页数据
  public function ajax(){
    $parent = $this->_get('parent/d',1);
    $this->sort  = 'sort desc';
    $this->where = ['parent'=>$parent];
    return parent::ajax();
  }

}