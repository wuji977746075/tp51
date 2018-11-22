<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-08-20 16:49:24
 * Description : [Description]
 */

namespace app\admin\controller;
use src\user\user\UserLogic;
use src\user\role\UserRoleLogic;
use src\fy\fy\FyAccountLogic;
use src\fy\fy\FyAccountUserLogic;
use src\fy\fy\FyInviteLogic;

class FyAccount extends CheckLogin {
  protected $model_id = 8;
  protected $banEditFields = ['id','pid'];
  function init() {
    $this->cfg['theme'] = 'layer';
  }

  function index() {
    $this->checkUserRight(0,'index');
    $send = $this->_get('send/s','');
    $this->assign('send',$send);
    return $this->show();
  }

  // 分佣统计
  function count() {
    // 查询每日情况
    return $this->show();
  }
  // ajax 分佣统计
  function ajaxCount() {
    $r = (new FyInviteLogic)->queryGroup($this->where,$this->page,$this->sort,'DATE_FORMAT( deteline,"%Y-%m-%d")','sum() as ');
    parent::ajax();
  }

  function mine() {
    $this->checkUserRight(0,'index');

    $send = $this->_get('send/s','');
    $this->assign('send',$send);
    $uinfo = UINFO;
    $this->assign('level',intval($uinfo['level'])+1);
    $this->assign('invite',UID);
    return $this->show();
  }

  // ajax excel
  function excel() {
    if(!IS_AJAX){ // 下载
      $this->error('暂未开放');
    }else{ // ajax
      $data     = $_POST['data'];
      $adds     = [];
      $pid_srcs = [];
      $c        = 0;
      !$this->isAdmin && $this->err('权限不足');
      $this->trans();
      try{
        foreach ($data as $v) {
          $temp_pid = $this->getOriPid($v[1]);
          $adds[] = [
            'uid'           => UID,
            'pid'           => $temp_pid,
            'name'          => $v[0],
            'pid_src'       => $v[1],
            'invite_url'    => $v[2],
            'invite_url_sm' => isset($v[3]) ? $v[3] : '',
            'token'         => isset($v[4]) ? $v[4] : '',
          ];
          if($this->logic->getInfo(['pid'=>$temp_pid])){
            $this->err('pid:'.$temp_pid.'已经导入过了');
          }
        }
        $adds && $c = count($this->logic->addAll($adds));
      }catch(\Exception $e){
        $this->err($e->getMessage(),$e->getCode());
      }
      $this->suc('导入成功,共'.$c.'条');
    }
  }

  // show qrcode
  function qrcode() {
    $paras = $this->_getPara('text','size|4|int');
    extract($paras);
    // 图片缓存设置
    // $dt = date("D, d M Y H:m:s GMT");
    // header("Last-Modified: $dt");
    // header("Cache-Control: max-age=844000");
    header('Content-type: image/png');
    $a = new \Qrcode($text);
    echo $a->image($size);
    exit;
  }

  // 淘宝客账号分配
  // todo 相互分配问题 只分配<level的user
  function setUser() {
    $this->checkUserRight(0,'allot');
    $uid = $this->_param('uid/d','','需要选择分配用户');
    $ids = $this->_param('ids/a',[]);
    empty($ids) && $this->err('需要选择分配ID');
    $ids = array_unique($ids);
    $ccc = count($ids);   // 请求分配个数
    if(!$this->isAdmin){ // 非超级用户
      // ? 自己的ids
      $c = (int) (new FyAccountUserLogic)->count([['uid','=',UID],['pid','in',$ids]]);
      // dump(getLastSql());die();
      if($ccc !== $c) $this->err('发现'.($ccc-$c).'个推广位所有权错误');
      $level = UINFO['level'];
      // ? 全未分配
      $c = (new FyAccountUserLogic)->count([['parent_uid','=',UID],['pid','in',$ids]]);
    }else{
      $level = 0;
      // ? 全未分配
      $c = (new FyAccountUserLogic)->count([['level','=',$level],['pid','in',$ids]]);
    }
    $c && $this->err('发现'.$c.'个推广位已分配');
    $adds = [];
    foreach ($ids as $v) {
      $adds[] = [
        'uid'=> $uid,
        'pid'=> $v,
        'parent_uid'=> UID,
        'level'=>$level,
      ];
    }
    $r = 0;
    $adds && $r = (new FyAccountUserLogic)->addAll($adds);
    $this->suc($r ? '操作成功' : '未做更改');
  }

  function set() {
    $this->checkUserRight(0,'set');
    $id = $this->id;
    $this->jsf = array_merge($this->jsf,[
      'name'          => '推广位名',
      'pid'           => 'PID',
      'invite_url'    => '长连接',
      'invite_url_sm' => '短链接',
      'token'         => '淘口令',
    ]);
    if(IS_GET){ // view
      $this->jsf_tpl = [
        ['name','input-long'],
        ['*pid'],
        ['*invite_url','input-long'],
        ['invite_url_sm','input-long'],
        ['token']
      ];
    }else{      // save
      $this->jsf_field = ['pid,invite_url','name,invite_url_sm,token'];
      $this->suc_url = url(CONTROLLER_NAME.'/index');
    }
    return parent::set();
  }
  // logic ajax page-list
  // 全部推广位管理
  function ajax() {
    $this->checkUserRight(0,'index');
    $paras = $this->_getPara('','kword,start,end,send|0|int');
    extract($paras);
    $map = [];
    $send ===1 && $map[] = ['fau.uid','exp',\think\Db::raw('is not null')];// 已分配
    $send ==2 && $map[] = ['fau.uid','exp',\think\Db::raw('is null')]; // 未分配
    $kword && $map[] = ['fa.pid|fa.name|fa.pid_src','like',$kword.'%'];
    $map[] = ['fau.level','exp',\think\Db::raw('=0 or fau.level is null')];
    ($start || $end) && $map[] = getWhereTime('fa.create_time',$start,$end);
    $this->where = $map;
    $this->queryField = 'fa.id,fa.uid as add_uid,fa.create_time,fa.name,fa.pid,fa.invite_url,fa.invite_url_sm,fa.token,ifnull(fau.uid,0) as uid,ifnull(u.id,0) as to_uid,ifnull(u.name,"") as to_name';
    // parent::ajax();
    $r = $this->logic->queryCountWithUser($this->where,$this->page,$this->sort,$this->pagePara,$this->queryField);
    // dump(\think\Db::getLastSql());die();
    if($r['list']){
      foreach ($r['list'] as &$v) {
        $temp = $v['add_uid'];
        $v['add_name'] = getUserById($temp,'name','');
      } unset($v);
    }
    $this->checkOp($r);
  }

  // 我的推广位
  function ajaxChild() {
    $this->checkUserRight(0,'index');
    $paras = $this->_getPara('','kword,start,end,send|0|int');
    extract($paras);
    $map = [];
    $kword && $map[] = ['fa.pid|fa.name|fa.pid_src','like',$kword.'%'];
    ($start || $end) && $map[] = getWhereTime('fa.create_time',$start,$end);
    $map[] = ['fau.uid','=',UID]; //分配给我的
    $this->where = $map;
    $this->queryField = "fa.id,fa.create_time,fa.name,fa.pid,fa.invite_url,fa.invite_url_sm,fa.token";
    $r = $this->logic->queryCountWithUser($this->where,$this->page,$this->sort,$this->pagePara,$this->queryField);
    if($r['list']){
      foreach ($r['list'] as &$v) {
        // 查询是否分配了
        $temp = $v['id'];
        $temp2 = (new FyAccountUserLogic)->getInfo(['parent_uid'=>UID,'pid'=>$temp]);
        $v['to_uid'] = $temp2 ? $temp2['uid'] : 0;
        $v['to_name']= getUserById($v['to_uid'],'name','');
      } unset($v);
    }
    $this->checkOp($r);
  }

  private function getOriPid($pid) {
    // mm_119852206_76700059_16661700406
    $a = explode('_',$pid);
    return end($a);
  }

}