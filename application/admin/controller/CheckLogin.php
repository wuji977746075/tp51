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
  protected $jsf_tpl = []; //预定义模板
  // 表单字段
  protected $jsf = []; //自定义表单字段
  protected $jsf_df = [ //默认表单字段
    'content'=>'内容',
    'desc'   =>'描述',
    'icon'   =>'图标',
    'img'    =>'图片',
    'parent' =>'上级',
    'sort'   =>'排序',
    'status' =>'状态',
    'title'  =>'标题',
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

  // require
  function jsf_tpl($field,$type='text',$val='',$css='',$extra,$tip=0){
    $need  = (substr($field, 0,1)=='*' ? '*':'') ;
    $field = ltrim($field,'*');
    !isset($this->jsf[$field]) && $this->error(L('need-jsf-name').':'.$field);
    $name = $this->jsf[$field];
    $hide = $type == 'hidden' ? 'hide' : '';
    $tpl = '';
    $jsf = str_replace('_', '-', $field);
    $tpl.= '<div class="layui-form-item '.$hide.'">';
    $tpl.=   '<label for="jsf-'.$jsf.'" class="layui-form-label">'.$need.' '.$name.'</label>';
    $tpl.=   '<div class="layui-input-inline '. $css.'">';
    $hold = $need ? L('type-in').$name : '';
    if($type == 'text'){ // text
      $tpl.= '<input type="text" name="'.$field.'" id="jsf-'.$jsf.'" value="'.$val.'" class="layui-input" placeholder="'.$hold.'"  autocomplete="off" required  lay-verify="required" >';
    }else if($type == 'hidden'){ // hidden
      $tpl.= '<input type="hidden" name="'.$field.'" id="jsf-'.$jsf.'" value="'.$val.'" class="layui-input" placeholder="'.$hold.'"  autocomplete="off" required  lay-verify="required" >';
    }else if($type == 'icon'){ // icon
      $tpl.= '<input type="text" name="'.$field.'" id="jsf-'.$jsf.'" value="'.$val.'" class="layui-input js-jsf-icon" placeholder="'.$hold.'"  autocomplete="off" required  lay-verify="required" >';
      $tpl .= '</div>'.($val ? '<div class="layui-input-inline"><i class="'.$val.'"></i></div>' : '').'<div>';
    }else if($type == 'radio'){ // radio
      $tpl .= '<input type="checkbox" name="'.$field.'" id="jsf-'.$jsf.'" lay-skin="switch" value="0" '.($val ? 'checked' : '').'>';
    }else if($type == 'number'){ // number
      $tpl.='<input type="number" min="0" name="'.$field.'" id="jsf-'.$jsf.'" value="'.$val.'" placeholder="'.$hold.'" class="layui-input">';
    }else if($type == 'select'){ // select(k=>v)
      $tpl.= '<select name="'.$field.'" id="jsf-'.$jsf.'" required  lay-verify="required" >';
      $tpl.= '<option value="">'.L('select-df').'</option>';
      foreach ($extra as $k=>$v) {
        $tpl.= '<option value="'.$k.'"'.($k==$val ? ' selected="selected"':'').'>'.$v.'</option>';
      }
      $tpl.= '</select>';
    }else if($type =='selects'){ //select(id,name,child)
      $tpl.= '<select name="'.$field.'" id="jsf-'.$jsf.'" required  lay-verify="required" >';
      $tpl.= '<option value="">'.L('select-df').'</option>';
      foreach ($extra as $v) {// level1
        $tpl.= '<option value="'.$v['id'].'"'.($v['id']==$val ? ' selected="selected"':'').'>'.$v['name'].'</option>';
        if(isset($v['child']) && $v['child']){
        foreach ($v['child'] as $v2) {// level2
          $tpl.= '<option value="'.$v2['id'].'"'.($v2['id']==$val ? ' selected="selected"':'').'>|_ '.$v2['name'].'</option>';
          if(isset($v2['child']) && $v2['child']){
          foreach ($v2['child'] as $v3) {// level3
            $tpl.= '<option value="'.$v3['id'].'"'.($v3['id']==$val ? ' selected="selected"':'').'>|__ '.$v3['name'].'</option>';
          }
          }
        }
        }
      }
      $tpl.= '</select>';
    }else if($type =='textarea'){ //textarea
      $tpl.='<textarea name="desc" id="jsf-'.$jsf.'" class="layui-textarea" placeholder="'.$hold.'">'.$val.'</textarea>';
    }else if($type =='btimg'){ //btimg imgselect
      $show_add = false;
      $vals = explode(',', $val);
      $l = count($vals);
      if($l>1){ // 多个
        $extra>$l && $show_add = true;
      }else{ // 单个
        $val = intval($val);
        $val<1 && $show_add = true;
      }
      // $extra = max
      $tpl .= '<input type="hidden" name="'.$field.'" id="jsf-'.$jsf.'" value="'.$val.'" >';
      $tpl .= '<div class="wxuploaderimg clearfix" data-maxitems="'.$extra.'">';
      $tpl .= ' <div class="img-preview clearfix " style="display:'.($val ? 'inline-block': 'none').'">';
      if($val){
        foreach ($vals as $v) {
          $tpl .='  <div class="pull-left clearfix img-item">';
          $tpl .='    <img class="img-responsive thumbnail" src="'.imgUrl($v,120).'" alt="image" data-imageid="'.$v.'"/>';
          $tpl .='    <div class="edit_pic_wrp"><a href="javascript:;" class="js_delete fa fa-lg fa-trash"></a></div>';
          $tpl .='  </div>';
        }
      }
      $tpl .= ' </div>';
      $tpl .= ' <div class="add" style="'.($show_add ? '': 'display:none').'"><i class="fa fa-plus"></i></div>';
      $tpl .= '</div>';
    }
    $tpl.=    '</div>';
    if($field == 'sort'){
      $note = L('sort-desc');
    }else if($field == 'icon'){
      $note = L('icon-class');
    }else{
      $note = L('tip-'.lcfirst(CONTROLLER_NAME).'-'.$field);
    }
    if($note){
      if($tip){
        $tip = intval($tip);
        $tpl.= '<div class="layui-form-mid tooltip" data-text="'.$note.'" data-type="'.$tip.'"><i class="fa fa-bell"></i></div>';
      }else{
        $tpl.=  '<div class="layui-form-mid layui-word-aux">'.$note.'</div>';
      }
    }
    $tpl.= '</div>';
    return $tpl;
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
        $info = isset($info) ? $info : $this->logic->getInfo(['id'=>$id]);
        empty($info) && $this->error(Linvalid('id'));
        $this->assign('info',$info);
      }else{    // add
      }
      // 合并表单字段说明,后盖前
      $this->jsf = $this->jsf ? array_merge($this->jsf_df,$this->jsf) : $this->jsf_df;
      $this->assign('jsf',$this->jsf);
      // 表单模板
      if($this->jsf_tpl){
        $jsf_tpl = [];
        foreach ($this->jsf_tpl as $v) {
          $v[1] = isset($v[1]) ? $v[1] : ''; // class
          $v[2] = isset($v[2]) ? $v[2] : []; // extra
          $v[3] = isset($v[3]) ? $v[3] : 0; // tip
          $vs = explode('|', $v[0]);
          $vs[1] = (isset($vs[1]) && $vs[1]) ? $vs[1] : 'text';
          $vs[2] = isset($vs[2]) ? $vs[2] : '';
          $f=ltrim($vs[0],'*');$t=$vs[1];
          $val = $id ? (isset($info[$f]) ? $info[$f] : $vs[2]) : '';
          $jsf_tpl[$f] = $this->jsf_tpl($vs[0],$t,$val,$v[1],$v[2],$v[3]);
        }
        $this->assign('jsf_tpl',$jsf_tpl);
      }
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
