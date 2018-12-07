<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-28 17:02:32
 * Description : [Description]
 */

namespace app\admin\controller;
// use

class BbsBan extends CheckLogin{
  protected $model_id = 26;
  protected $banEditFields = ['uid','id'];
  function init(){
    $this->cfg['theme'] = 'layer';
  }


  function index() {
    return parent::index();
  }


  function ajax() {
    $this->checkLogic();
    $uid  = $this->_get('uid',''); // uid
    $rule = $this->_get('rule',''); // uid
    $map = [];
    if($uid){ // 查某人的
      $map['uid'] = $uid;
      $this->where = $map;
      $r = $this->logic->queryCount($this->where,$this->page,$this->sort,[],"*,0 as count");
    }else{
      $r = $this->logic->querySlient($this->where,$this->page,$this->sort,[],"*,count(uid) as count");
    }
    if($r['list']){
      $list = $r['list'];
      foreach ($list as &$v) {
        if($uid){
        }else{
          // 修复 group不能排序
          $v = array_merge($v,(array)$this->logic->getInfo(['uid'=>$v['uid']],'create_time desc'));
        }
        $v['rule_desc'] = $this->logic->getRuleDesc($v['rule']);
        $v['uname'] = '';
        $v['unick'] = '';
        $v['uname'] = getUserById($v['uid']);
      } unset($v);
      $r['list'] = $list;
    }
    $this->checkOp($r);
  }

  function set(){
    if(IS_GET){ // add - view
      return parent::set();
    }else{      // add - save
      $this->jsf = array_merge($this->jsf,[
        'reason'     =>'原因',
        'rule'       =>'规则',
        // 自定义
        'start' =>'开始时间',
        'end'   =>'结束时间',
        'uids'  =>'用户',
      ]);
      $paras = $this->_getPara('uids||mulint,reason,rule|0|int','start,end');
      extract($paras);
      $reason = htmlspecialchars($reason);
      $start  = $start ? strtotime($start) : 0;
      $end    = $end ? strtotime($end) : 0;
      // ? uid
      $adds = [];
      foreach ($uids as $uid) {
        if(is_numeric($uid) && $uid>0){
          // ? uid
          $r = (new UserLogic)->isValidInfo($uid,'id');
          // ?
          $adds[] = [
            'uid'        =>$uid,
            'start_time' =>$start,
            'end_time'   =>$end,
            'rule'       =>$rule,
            'reason'     =>$reason,
          ];
        }
      }
      $adds && $this->logic->addAll($adds);
      // $this->suc_url = url(CONTROLLER_NAME.'/index');
      $this->opSuc();
    }
  }

  function dels() { }
  function del() {
    $id = $this->id;
    $r  = $this->logic->isValidInfo($id,'id');
    if(intval($r['rule'])){
      if($this->logic->save(['id'=>$id],['rule'=>0,'reason'=>['exp',"concat('reason',';解除')"]])){
          $this->opErr('操作成功');
      }else{
          $this->opErr('操作失败');
      };
    }else{
      $this->opErr('无效操作,因为已解除了');
    }
  }
}