<?php
namespace app\admin\controller;

class Menu extends CheckLogin{
  protected $business = '菜单';
  // 递进型菜单首页
  public function index(){
    $parent = $this->_param('parent/d',0);
    $up     = $this->_param('up/d',0); //  ? parent的上级
    if($parent && $up){
      $r = $this->logic->getInfo(['id'=>$parent]);
      if($r) $parent = $r['parent'];
    }
    $this->assign('parent',$parent);
    return $this->show();
  }

  // drag型菜单首页
  public function drag(){
    $nodes = $this->logic->getAllMenu(false);
    $this->assign('nodes',$nodes);
    return $this->show();
  }

  // drag型菜单排序
  public function dragSort(){
    $ids = $this->_param('ids/a',[]);
    if($ids){
      // 最多3级
      foreach ($ids as $k=>$v) {
        $id = intval($v['id']);
        $this->logic->saveById($id,['sort'=>$k]);

        if(isset($v['children'])){
        foreach ($v['children'] as $k2=>$v2) {
          $id = intval($v2['id']);
          $this->logic->saveById($id,['sort'=>$k2]);

          if(isset($v2['children'])){
          foreach ($v2['children'] as $k3=>$v3) {
            $id = intval($v3['id']);
            $this->logic->saveById($id,['sort'=>$k3]);
          }
          }
        }
        }
      }
      $this->suc();
    }else{
      $this->err(Linvalid('op'));
    }
  }

  // ajax 返回菜单单页数据
  public function ajax(){
    $parent = $this->_get('parent/d',0);
    $this->where = ['parent'=>$parent];
    return parent::ajax();
  }

  public function set(){
    $id = $this->id;
    $this->jsf = array_merge($this->jsf,[
      'name'   =>'标题',
      'parent' =>'父级',
      'url'    =>'链接',
      'params' =>'链接参数',
      'show'   =>'显示',
    ]);
    $super  = $this->_param('super/d',0); // 是否为高级模式
    $parent = $this->_param('parent/d',0);
    if(IS_GET){ // view
      $this->assign('super',$super);
      if($id){ // edit
        $info = $this->logic->getInfo(['id'=>$id]);
        empty($info) && $this->error(Linvalid('id'));
        $parent = $info['parent'];
      }else{ // add
        if($parent){
          $info = $this->logic->getInfo(['id'=>$parent]);
          empty($info) && $this->error(Linvalid('parent'));
          $info['level'] > 2 && $this->error(L('menu-over-level'));
        }
      }
      $this->assign('parent',$parent);

      // 查询2级菜单 tree
      $menu = $this->logic->getAllMenu(false,2);
      array_unshift($menu, ['id'=>0,'name'=>'* 顶级 *','child'=>[]]);
      $this->assign('menu',$menu);

      $this->jsf_tpl = [
        ['*name'],
        ['*parent|selects','',$menu],
        ['url'],
        ['icon|icon'],
        ['params'],
        ['show|radio','','',3],
        ['desc|textarea','input-long'],
        ['sort|number'],
      ];
      return parent::set();
    }else{ //save
      $paras = $this->_getPara('name','url,icon,params,show|0|int,desc,sort|0|int,parent|0|int');
      if($id){ // edit
        $info = $this->logic->getInfo(['id'=>$id]);
        empty($info) && $this->err(Linvalid('id'));

        if($parent != intval($info['parent'])){
          $info = $this->logic->getInfo(['parent'=>$id]);
          $info && $this->err(L('need-del-down'));
        }elseif($parent == $id){
          $this->err(Linvalid('op'));
        }
        $r = $this->logic->save(['id'=>$id],$paras);
      }else{ // add
        if($parent){
          $r = $this->logic->getInfo(['id'=>$parent]);
          empty($r) && $this->err(Linvalid('parent'));
          $r['level'] > 2 && $this->err(L('menu-over-level'));
          $paras['level'] = intval($r['level']) + 1;
        }else{
          $paras['level'] = 1;
        }
        $id = $this->logic->add($paras);
      }
      if($super){
        $this->suc_url= url(CONTROLLER_NAME.'/drag');
      }else{
        $this->suc_url= url(CONTROLLER_NAME.'/index',['parent'=>$parent]);
      }
      $this->opSuc();
    }
  }
}