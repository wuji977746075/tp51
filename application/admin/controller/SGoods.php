<?php
/**
 * 周舟  hzboye010@163.com
 * sublime3 - home edited
 */

namespace app\admin\controller;


class ScoreGoods extends Admin{

  protected function _initialize(){
    parent::_initialize();
    $this->assign('types',ScoreGoodsApi::$types);
  }

  public function index($p=1){
    $r = apiCall(ScoreGoodsApi::ORPAGE,array(array('status'=>0),array('curpage'=>$p,'size'=>10),'sort desc'));
    if($r['status'])
    {
      $list = $r['info']['list'];
      foreach ($list as &$v) {
        $type   = intval($v['type']);
        $tpl_id = intval($v['tpl_id']);
        if($type===2 || $type===3){
          $rs = apiCall(ScoreGoodsApi::GET_TPL_NAME,array($type,$tpl_id));
          if(!$rs['status']) $v['tpl_name'] = '失效模板(请修改)';//$this->error($rs['info']);
          else $v['tpl_name'] = $rs['info'] ? $rs['info']:'未知模板';
        }else{
          if($tpl_id)  $v['tpl_name'] = '邮费(RMB):'.$tpl_id;
          else $v['tpl_name'] = '免邮';
        }
        // $v['tpl_name'] =
        // $list[$k]['detail'] = mb_substr(htmlspecialchars_decode($v['detail']),0,20);
      }
      // dump($list);exit;
      $this->assign('list',$list);
      $this->assign('show',$r['info']['show']);
    }else{
      $this->error($r['info']);
    }

    $this->display();
  }

  public function delete($gid){
    //修改status //密码
    if(IS_GET){
      $r = apiCall(ScoreGoodsApi::SAVE,array(array('id'=>$gid),array('status'=>-1)));
      if($r['status'])
        $this->success('删除成功');
      else
        $this->error('删除失败');
    }
  }


  public function add(){
    if(IS_GET){
      $this->display();
    }else if(IS_POST){
      $map = $this->_param(''); //自动绑定
      $map['main_img'] = intval($map['main_img']);
      $map['score']    = intval($map['score']);
      if($map['score']<1) $this->error('最少需1萌币');
      $map['count']    = $this->_param('count',1,'intval');
      $map['price']    = round($this->_param('price',0.00,'float'),2);
      if($map['type'] == 1 )  $map['tpl_id'] = intval($map['tpl_num']); //邮费
      else $map['tpl_id'] = intval($map['tpl_id']);//模板ID;

      $r = apiCall(ScoreGoodsApi::ADD,array($map));
      if($r['status']) $this->success('添加成功',U('ScoreGoods/index'));
      else $this->error('添加失败');
    }
  }
  public function edit($gid){
    if(IS_GET){
      $r = apiCall(ScoreGoodsApi::GET_INFO,array(array('id'=>$gid)));
      if(!$r['status'])     exit($r['info']);
      if(empty($r['info'])) exit('gid错误');
      $sgood = $r['info'];
      $sgood['tpl_name'] = '';
      $type = intval($sgood['type']);
      if(in_array($type,array(2,3))){
        $r = apiCall(ScoreGoodsApi::GET_TPL_NAME,array($sgood['type'],$sgood['tpl_id']));
        if(!$r['status'])     $r['info']='错误模板:E';//exit($r['info']);
        if(empty($r['info'])) $r['info']='失效模板:N';//exit('type或tpl_id错误');
        $sgood['tpl_name'] = $r['info'];
      }
      $this->assign('entry',$sgood);
      $this->display();

    }else if(IS_POST){
      $map = $this->_param(''); //自动绑定
      // dump($map);exit;
      $map['main_img'] = intval($map['main_img']);
      $map['score']    = intval($map['score']);
      if($map['score']<1) $this->error('最少需1萌币');
      $map['count']    = $this->_param('count',1,'intval');
      $map['price']    = round($this->_param('price',0.00,'float'),2);
      if($map['type'] == 1 )  $map['tpl_id'] = intval($map['tpl_num']); //邮费
      else $map['tpl_id'] = intval($map['tpl_id']);//模板ID;
      $r = apiCall(ScoreGoodsApi::SAVE,array(array('id'=>$gid),$map));
      // dump($r);exit;
      if($r['status'])
        $this->success(L('RESULT_SUCCESS'),U('ScoreGoods/index'));
      else
        $this->error(L('RESULT_FAIL'));
    }
  }
  public function change($gid,$onshelf){
    $r = apiCall(ScoreGoodsApi::CHANGE,array($gid,$onshelf));
    // dump($r);exit;
    if($r['status'])
      $this->display('index');
    else
      $this->error('更改失败');
  }

  public function search($p=1){
    $kword = $this->_param('kword');
    // dump($kword);exit;
      $this->assignTitle($kword.' - 搜索结果');
      if($kword) $where = '(`id` like \'%'.$kword.'%\' or `name` like \'%'.$kword.'%\') and ';
      $where .=' `status`=0';//<>-1';
      $r = apiCall(ScoreGoodsApi::ORPAGE,array($where,array('curpage'=>$p,'size'=>10),'sort desc'));
      // dump($r->lastSql());
      if($r['status'])
      {
          $list = $r['info']['list'];
          // foreach ($list as $k => $v) {
          //     $list[$k]['desc'] = mb_substr(htmlspecialchars_decode($v['description']),0,20);
          // }
          $this->assign('list',$list);
          $this->assign('show',$r['info']['show']);
      }else{
          $this->error('ScoreGoodsApi:SEARCH:ERROR');
      }
      $this->display('index');
    // }
  }

  /**
   *
   */
  public function select(){
    // dump(I('get.'));exit;
    $type = $this->_param('type',0,'int');
    $q    = $this->_param('q', '', 'trim');
    $where = 'where tpl.status=1';
    if($q){
      $where .= ' and (tpl.id='.$q.' or dt.name like \'%'.$q.'%\')';
    }
    if($type ===2){
      //查询红包模板
      $model = 'itboye_red_envelope_tpl';
    }elseif($type === 3){
      //查询优惠券模板
      $model = 'itboye_coupon_tpl';
    }else{
      $this->success(array());
    }
    //不分页
    $list = M()->query('select dt.name,tpl.dtree_type,tpl.id from '.$model.' as tpl left join common_datatree as dt on dt.id=tpl.dtree_type '.$where);
    if(false === $list) echo json_encode(array());
    else echo json_encode($list);
    exit;
  }
}