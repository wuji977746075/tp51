<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-05-12 09:21:02
 * Description : [cms 留给模块主页 统计/..]
 */

namespace app\admin\controller;
// use

class CmsCate extends CheckLogin {

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
      'url'    =>'链接',
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
          $info['level'] > 2 && $this->error(L('cate-over-level'));
        }
      }
      $this->assign('parent',$parent);

      // 查询2级菜单 tree
      $menus = $this->logic->getAllMenu(false,2);
      array_unshift($menus, ['id'=>0,'name'=>'* 顶级 *']);
      $this->jsf_tpl = [
        ['*name'],
        ['*parent|selects','',$menus],
        ['url','input-long','',3],
        ['icon|icon','input-long'],
        ['show|radio','','',3],
        ['desc|textarea','input-long'],
        ['sort|number']
      ];
      return parent::set();
    }else{ //save
      $paras = $this->_getPara('name','url,icon,show|0|int,desc,sort|0|int,parent|0|int');

      if($id){ // edit
        $info = $this->logic->getInfo(['id'=>$id]);
        empty($info) && $this->err(Linvalid('id'));

        if($parent != intval($info['parent'])){ // 无下级才允许修改parent
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
          $r['level'] > 2 && $this->err(L('cate-over-level'));
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