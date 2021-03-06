<?php
namespace app\admin\controller;

use src\sys\role\SysRoleLogic as RoleLogic;
use src\sys\session\SysSessionLogic as SessionLogic;
use src\user\user\UserLogic;
use src\user\role\UserRoleLogic;
use \ErrorCode;

class CheckLogin extends Base {
  protected $uid = 0;
  protected $id  = 0;
  protected $page = []; // extend
  protected $sort = []; // extend
  protected $banDelIds = [];         // 禁止删除的id
  protected $banEditFields = ['id']; // 禁止单独编辑的字段
  protected $where     = [];        // where
  protected $isSuper   = 0;         // 是否为super
  protected $isAdmin   = 0;         // 是否为admin
  protected $isSaler   = 0;         // 是否为saler
  protected $isTopSaler   = 0;      // 是否为level1 saler
  // set的字段 [必须,可选]
  protected $jsf_field = [
    '',''
  ];
  protected $jsf_tpl = []; //预定义模板
  protected $queryField = '*'; //查询字段
  protected $pagePara = []; //分页条件
  protected $extraFields = false; //extra字段数组
  protected $uinfo = null; //当前账户信息
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

  // public function clearall(){
  //   $result = Db::execute("TRUNCATE `itboye_api_call_his`;");
  //   $this->success("操作成功");
  // }

  public function initialize(){
    parent::initialize();
    $uid = SessionLogic::isAdminLogin(); // 是否登陆了后台
    if($uid > 0){

      // if(!defined('UID'))
      define('UID', $uid);
      $uinfo = (new UserLogic)->getAllInfo($uid);
      define('UINFO',$uinfo);
      $role = $uinfo['role_id'];
      !$role && throws('用户角色异常',ErrorCode::Need_Login);
      define('ROLE',$role);

      $this->uid = $this->_param('uid/d',0);
      $this->assign('uid',$this->uid);
      $this->id  = $this->_param('id/d',0);
      $this->assign('id',$this->id);

      $this->isSuper = $this->isSuper() ? 1 :0;
      $this->isAdmin = $this->isAdmin() ? 1 :0;
      $this->isSaler = $this->isSaler() ? 1 :0;
      $this->isTopSaler = $this->isTopSaler() ? 1 :0;
      $this->assign([
        'isSuper'=>$this->isSuper,
        'isAdmin'=>$this->isAdmin,
        'isSaler'=>$this->isSaler,
        'isTopSaler'=>$this->isTopSaler,
      ]);
      $this->uinfo = $this->uid ? ( $this->uid == $uid ? $uinfo : (new UserLogic)->getAllInfo($this->uid)) : [];
      // add return layui-btn
      $this->assign('html_return',html_return());
    }else{
      if(IS_AJAX){
        $this->err('请重新登录',url('login/index'),ErrorCode::Need_Login);
      }else{
        $this->redirect('login/index');
      }
    }
  }

  // todo : 根据user在admin的role 判断当前控制器的当前操作权限
  // 暂时用MenuAuth + isSuper ...控制
  protected function checkUserRight($uid=0,$op=''){
    if($uid > 0){
      $curr = $uid == UID ? 1 :0;
    }else{
      $uid = UID;
      $curr = 1;
    }
    // 非超级管理员 检查权限
    if(!$this->isSuper($uid)) {
      if($op){ // 'user_add'
        if(strpos('_',$op)===false){
          $op = CONTROLLER_NAME.'_'.$op;
        }
      }else{
        $op = CONTROLLER_NAME.'_'.ACTION_NAME;
      }
      if(!(new RoleLogic)->checkApiAuth($uid,$op,CLIENT_ID)){
        $this->opErr(($curr ? '您': '用户'.$uid).'禁止此操作');
      }
    }
  }
  // uid 是否为超级用户
  protected function isSuper($uid=0){
    !$uid && $uid=UID;
    return (new UserLogic)->isSuper($uid,CLIENT_ID);
  }
  // 当前用户角色 是否为管理员
  protected function isAdmin(){
    return $this->isSuper || RoleLogic::isAdmin(ROLE);
  }
  // 当前用户角色 是否为管理员
  protected function isSaler(){
    return $this->isSuper || RoleLogic::isSaler(ROLE);
  }
  // 当前用户角色 是否为管理员
  protected function isTopSaler(){
    return $this->isSuper || (new UserLogic)->isTopSaler(UID);
  }
  // 表单字段模板构造器
  // $field : *field*|type*|df
  // $css   : input_parent_css|input_css
  // $tip   : layui_tip_type
  function jsf_tpl($field,$type='text',$val='',$css='',$extra='',$tip=0){
    $need = (substr($field, 0,1)=='*' ? '*':'') ; // ? 必须字段
    $need_ipt = $need ? ' required  lay-verify="required" ' : ''; // ? layui 验证开启
    $field = ltrim($field,'*'); // 字段名
    !isset($this->jsf[$field]) && $this->error(L('need-jsf-name').':'.$field);
    $name = $this->jsf[$field]; // 字段显示名
    $ipt_disable = false;
    if($type != rtrim($type,'*')){ // disable
      $type = rtrim($type,'*');
      $ipt_disable = true;
    }
    $hide = $type == 'hidden' ? 'hide' : ''; //是否为 隐藏域
    $hold = $need ? L('type-in').$name : ''; // 表单placeholder
    $jsf = str_replace('_', '-', $field); // 表单id

    $tpl = ''; // 模板html
    $tpl.= '<div class="layui-form-item '.$hide.'">';
    $tpl.=   '<label for="jsf-'.$jsf.'" class="layui-form-label">'.$need.' '.$name.'</label>';
    $csses = explode('|',$css);
    $css   = $csses[0];
    $css2  = (isset($csses[1]) ? $csses[1] : '').($ipt_disable ? ' layui-disabled' : '');
    $tpl.=   '<div class="layui-input-inline '. $css.'">';
    if(in_array($type,['text','hidden','number','password'])){
      $ipt_disable && $extra .= ' disabled';
      $tpl.= '<input type="'.$type.'" name="'.$field.'" id="jsf-'.$jsf.'" value="'.$val.'" class="layui-input '.$css2.'" placeholder="'.$hold.'"  autocomplete="off" '.$need_ipt.' title="'.$val.'" '.$extra.'>';
    }else if($type == 'cropper'){ // cropper
      $tpl .= '<input type="hidden" name="'.$field.'" id="jsf-'.$jsf.'" value="'.$val.'">';
      $tpl .= '<a id="jsf-'.$jsf.'-extra" data-img="'.avaUrl($extra).'" style="cursor:pointer;" title="更改">
        <img src="'.avaUrl($extra,120).'" class="layui-upload-img" style="width:120px;">
      </a>';
    }else if($type == 'radio'){ // radio
      $tpl .= '<input type="checkbox" name="'.$field.'" id="jsf-'.$jsf.'" lay-skin="switch" value="1" '.($val ? 'checked' : '').' '.$extra.'>';
    }else if($type =='textarea'){ // textarea
      $tpl.='<textarea name="'.$field.'" id="jsf-'.$jsf.'" class="layui-textarea '.$css2.'" placeholder="'.$hold.'" '.$need_ipt.'>'.$val.'</textarea>';
    }else if($type == 'check'){ // check : todo
      $tpl.= '<div> todo ... </div>';
    }else if($type == 'time'){ // time
      $val &&  $val = date($extra,intval($val));
      $tpl.= '<input type="text" name="'.$field.'" id="jsf-'.$jsf.'" value="'.$val.'" class="layui-input js-datetime-picker '.$css2.'" data-format="'.$extra.'" placeholder="'.$hold.'"  autocomplete="off" '.$need_ipt.' >';
    }else if($type == 'select'){ // select(k=>v)
      $tpl.= '<select lay-search name="'.$field.'" id="jsf-'.$jsf.'"  '.$need_ipt.'>';
      $tpl.= '<option value="">'.L('select-df').'</option>';
      foreach ($extra as $k=>$v) {
        $tpl.= '<option value="'.$k.'"'.($k==$val ? ' selected="selected"':'').'>'.$v.'</option>';
      }
      $tpl.= '</select>';
    }else if($type =='selects'){ // select(id,name) / select(id,name,child)
      $tpl.= '<select lay-search name="'.$field.'" id="jsf-'.$jsf.'"  '.$need_ipt.'>';
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
    }else if($type == 'icon'){ // icon 自定义图标选择
      $tpl.= '<input type="text" name="'.$field.'" id="jsf-'.$jsf.'" value="'.$val.'" class="layui-input js-jsf-icon '.$css2.'" placeholder="'.$hold.'"  autocomplete="off"  '.$need_ipt.'>';
      $tpl .= '</div>'.($val ? '<div class="layui-input-inline" style="margin-top: 7px;"><i class="'.$val.'"></i></div>' : '').'<div>';
    }else if($type =='btimg'){ // btimg 自定义图片选择
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
      $tpl .= '<input type="hidden" name="'.$field.'" id="jsf-'.$jsf.'" value="'.$val.'"  '.$need_ipt.'>';
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
    }else if($type == 'icon'){
      $note = L('tip-default-icon');
    }else if($type == 'btimg'){
      $note = L('tip-default-btimg');
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
    // $this->checkUserRight();
    return $this->show();
  }

  // this->logic 是否定义了
  protected function checkLogic($msg=''){
    if(!is_subclass_of($this->logic,'\src\\base\\BaseLogic')){
      $msg = $msg ? $msg : L('need-set-logic').':'.$this->logicPath.' not  a instance of BaseLogic';
      $this->opErr($msg);
    }
  }

  //关于jsf 一般一+二:
  // 第一位 :
  //  *(必选,入构)+字段(入构)+*(强制默认)+|+input类型+*(隐藏标志)|+默认
  // 第二位 : input父类的class+|+input的class
  // 第三位 : extra
  //  不定,select(选项)/radio/time(格式)/bt_img(max)..
  // 第四位 : tip
  //  不填为lang里的tip-{class}-{field}
  public function set(){
    $id = $this->id;
    if(IS_GET){ // view
      // $this->checkUserRight(0,'index');

      $this->assign('op',L($id ? 'edit' : 'add'));
      $this->assign('id',$id);
      if($id){  // edit
        $this->checkLogic();
        $info = $this->logic->getInfo(['id'=>$id]);
        // todo : 判断不了重载的$info thinking...
        empty($info) && $this->error(Linvalid('id'));
        if($this->extraFields){
          try{
            $info['extra'] = $info->extra;
          }catch(\Exception $e){
            throws('必须设置模型关联');
          }
        }
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
          $vs   = explode('|', $v[0]); // field_extra
          $v[1] = isset($v[1]) ? $v[1] : ''; // class
          $v[2] = isset($v[2]) ? $v[2] : ''; // extra
          $v[3] = isset($v[3]) ? $v[3] : 0; // tip
          $vs[1] = (isset($vs[1]) && $vs[1]) ? $vs[1] : 'text'; // input type
          $vs[2] = isset($vs[2]) ? $vs[2] : ''; // df_val

          $f   = rtrim(ltrim($vs[0],'*'),'*'); // field
          $t   = $vs[1]; // input_type
          $val = $vs[2]; // df_val
          if($id){
            if(rtrim($vs[0],'*') == $vs[0]){ //field右无*
              if(isset($info[$f])){
                $val = $info[$f];
              }elseif($this->extraFields){
                // && isset($info['extra'][$f])
                // 不在info在extra中 但未发现直接抛异常
                $val = $info['extra'][$f];
              }
            }
          }
          $jsf_tpl[$f] = $this->jsf_tpl(rtrim($vs[0],'*'),$t,$val,$v[1],$v[2],$v[3]);
        }
        $this->assign('jsf_tpl',$jsf_tpl);
      }
      return $this->show();
    }else{ // save
      // $this->checkUserRight(0,$id ? 'edit' : 'add');
      $this->checkLogic();
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
    // $this->checkUserRight(0,'index');
    $this->checkLogic();
    // $kword = $this->_get('kword',''); // 搜索关键词
    $r = $this->logic->queryCount($this->where,$this->page,$this->sort,$this->pagePara,$this->queryField);
    $this->checkOp($r);
  }


  // 一个字段(id外)修改 , 由id标志
  public function editOne() {
    // $this->checkUserRight(0,'edit');
    $this->checkLogic();
    $field = htmlspecialchars($this->_param('field','',Llack('field')));
    // ? 是否禁止编辑
    if(in_array($field,$this->banEditFields)) $this->err(L('ban-op-field').$field);
    $val   = htmlspecialchars(trim($this->_param('val','')));
    $where = $this->id ? ['id'=>$this->id] : [['id','in',$this->_param('ids/a',[])]];
    $this->logic->save($where,[$field=>$val]);
    $this->opSuc('','',0);
  }

  // 删除, 由id标志, check是检查是否含parent=id
  public function del(){
    // $this->checkUserRight(0,'del');
    $this->checkLogic();
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
    // $this->checkUserRight(0,'del');
    $this->checkLogic();
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
