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
    $this->jsf = array_merge($this->jsf,[
      'reason'     =>'原因',
      'start_time' =>'开始时间',
      'end_time'   =>'结束时间',
    ]);
    if(IS_GET){ // add - view
      // $jsfs = [
      //   ['nick','input-long'],
      //   ['avatar|btimg','',1],
      //   ['phone'],
      //   ['email'],
      //   ['status|radio'],
      //   ['*role|selects|'.$role_id,'',$roles]
      // ];
      // $this->jsf_tpl = $jsfs;
      return parent::set();
    }else{      // add - save
      $paras = $this->_getPara('nick','avatar,phone,email,status|0|int,pass');
      $this->suc_url = url(CONTROLLER_NAME.'/index');
      $this->suc(LL('op suc'));
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

  /**
   * 添加禁言
   */
  public function banAdd(){
      if(IS_AJAX){
          $uids   = input('uids/s','');
          $start  = input('banStart/s','');
          $end    = input('banEnd/s','');
          $rule   = input('rule/d',0);
          $reason = htmlspecialchars(input('reason/s',''));
          !$uids && $this->error('需要用户','');
          !$rule && $this->error('需要规则','');
          !$reason && $this->error('需要备注','');
          $start = $start ? strtotime($start) : 0;
          $end   = $end ? strtotime($end) : 0;
          // ? uid
          $uids_arr = array_unique(explode(',', trim($uids)));
          $ret = '';$adds = [];
          foreach ($uids_arr as $v) {
              $uid = $v;
              if(is_numeric($v) && $v>0){
                  // ? uid
                  $r = (new MemberLogic)->getInfo(['uid'=>$uid]);
                  if(!$r['status']) $this->error($r['info'],'');
                  if(empty($r['info'])) $this->error('无此用户:'.$v,'');
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
          if($adds){
              $r = (new BbsBanLogicV2)->addAll($adds);
          }
          $this->success('操作成功');
      }else{
          $this->error('未知请求','');
      }
  }
}