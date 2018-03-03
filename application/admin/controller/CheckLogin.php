<?php
namespace app\admin\controller;

use src\session\SessionLogic;

class CheckLogin extends Base{
  protected $uid = null;
  protected $id  = 0;
  protected $page = []; // extend
  protected $sort = []; // extend
  protected $banDelIds = [];         // 禁止删除的id
  protected $banEditFields = ['id']; // 禁止编辑的字段
  protected $where     = [];         // where
  // set的字段 [必须,可选]
  protected $jsf_field = [
    '',''
  ];
  // 表单字段
  protected $jsf = []; //自定义表单字段
  protected $jsf_df = [ //默认表单字段
    'desc'   =>'描述',
    'sort'   =>'排序',
    'img'    =>'图片',
    'status' =>'状态',
  ];
  public function initialize(){
    parent::initialize();
    $uid = SessionLogic::isAdminLogin(); // 是否登陆了后台
    if($uid > 0){
      if(!defined('UID')) define('UID', $uid);
      $this->uid = $uid;
      $this->id  = $this->_param('id/d',0);

      // add return layui-btn
      $this->assign('html_return',html_return());
    }else{
      $this->redirect('login/index');
    }
  }

  // 首页
  public function index(){
    return $this->show();
  }

  public function set(){
    $id = $this->id;
    if(IS_GET){ // view
      $this->assign('op',L($id ? 'edit' : 'add'));
      $this->assign('id',$id);
      if($id){  // edit
        $info = $this->logic->getInfo(['id'=>$id]);
        empty($info) && $this->error(Linvalid('id'));
        $this->assign('info',$info);
      }else{    // add
      }
      // 合并表单字段说明,后盖前
      $this->jsf = $this->jsf ? array_merge($this->jsf_df,$this->jsf) : $this->jsf_df;
      $this->assign('jsf',$this->jsf);
      return $this->show();
    }else{ // save
      $paras = $this->_getPara($this->jsf_field[0],$this->jsf_field[1]);
      if(isset($paras['id'])) unset($paras['id']);
      if($id){  // edit
        $this->logic->save(['id'=>$id],$paras);
      }else{    // add
        $id = $this->logic->add($paras);
      }
      $this->opSuc();
    }
  }

  // logic ajax page-list
  public function ajax(){
    // $kword = $this->_get('kword',''); // 搜索关键词
    $r = $this->logic->queryCount($this->where,$this->page,$this->sort);
    $this->checkOp($r);
  }


  // 一个字段(id外)修改 , 由id标志
  public function editOne(){
    $field = htmlspecialchars($this->_param('field','',Llack('field')));
    if(in_array($field,$this->banEditFields)) $this->err(L('ban-op-field').$field);
    $val   = htmlspecialchars(trim($this->_param('val','')));

    $this->logic->save(['id'=>$this->id],[$field=>$val]);
    $this->opSuc();
  }

  // 删除, 由id标志, check是检查是否含parent=id
  public function del(){
    $id    = $this->id;
    $check = $this->_param('check/d',0);
    // ? id
    if($id<=0) $this->err(Linvalid('op'));
    if(in_array($id,$this->banDelIds)) $this->err(L('ban-del').' id: '.$id);
    // ? check
    if($check && $this->logic->getInfo(['parent'=>$id])){
      $this->err(L('need-del-down'));
    }
    $this->logic->delete(['id'=>$id]);
    $this->opSuc('','',0);
  }

  // 批量删除, 由id标志
  public function dels(){
    $ids   = $this->_param('ids/a',[]);
    $check = $this->_param('check/d',0);
    // ? id
    if(empty($ids)) $this->err(Linvalid('op'));
    $banIds = array_intersect($this->banDelIds,$ids);
    if($banIds) $this->err(L('ban-del').' id: '.implode(L('&'), $banIds));
    // ? check
    if($check && $this->logic->getInfo([['parent','in',$ids]])){
      $this->err(L('need-del-down'));
    }
    $this->logic->delete([['id','in',$ids]]);
    $this->opSuc('','',0);
  }

}
