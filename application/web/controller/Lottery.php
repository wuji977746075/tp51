<?php
/**
 * 周舟 hzboye010@163.com
 * addby sublime snippets
 */
namespace Webview\Controller;

use Common\Api\GameApi;
use Common\Api\GamePrizeApi;
use Common\Api\GameLogApi;
use Common\Api\GamePrizegethisApi;
// use Admin\Api\MemberApi;
// use Admin\Api\DatatreeApi;
// use Webview\Api\SigninApi;
// use Shop\Api\ShoppingCartApi;
// use Shop\Api\RedEnvelopeApi;

class GameController extends WebViewController{
    /**
     * 初始化
     */
    public function _initialize(){
        parent::_initialize();

        //查询最新抽奖信息
        // $map    = array('id' => $gid);
        // $result = apiCall(GameApi::FIND,array($map));
        $result = apiCall(GameApi::FIND,array(array('end_time'=>array('egt',NOW_TIME))));
        // dump($result);
        if($result['status'])
        {
            if(!$result['info']) $this->error('最近无抽奖');
            $this->assign('game',$result['info']);
            $this->gid = $result['info']['id'];
            $this->assignTitle($result['info']['name']);
        }else{
            $rhis->error('GameApi : Find : error');
        }
    }
    /**
     * get the gamePrize by game id 获取抽奖对应奖品
     * @param  [type] $gid [description]
     * @return [type]      [description]
     */
    public function getPrize(){
        if(IS_AJAX){
            $result = apiCall(GamePrizeApi::GET_PRIZE,array(array('game_id',$this->gid)));
            if($result['status']){
                foreach ($result['info'] as &$v) {
                    unset($v['prize_cnt']);
                    unset($v['probability']);
                }
                echo json_encode($result);
            }

        }
    }
    public function getLog($start=0){
        $start = is_numeric($start) ? intval($start):0;
        if(IS_AJAX)
           echo json_encode(apiCall(GameLogApi::PAGE,array($this->uid,$this->gid,$start)));
           // echo json_encode(apiCall(GameLogApi::FINDALL,array($gid)));
    }
    /**
     * 抽奖页面入口
     */
    public function index(){
        //only for test
        $uid = $this->uid;
        $gid = $this->gid;
        // if($uid != 82){
        //     header("Content-Type: text/html;charset=utf-8");
        //     echo '<h1>抽奖功能更新中</h1>';
        //     exit;
        // }
        $this->assign('uid',$uid);
        // $r   = $this->preRoll($uid,$gid);
        $r   = apiCall(GameApi::CHECK,array($uid,$gid));
        if(!$r['status'])
            $r['info'] = array('remainA'=>5,'remainF'=>0,'signin'=>false,'score'=>0);
        //最新的15条中奖信息
        $r2  =  apiCall(GamePrizegethisApi::FINDS,array($gid,1,15));
        // dump($r2);exit;
        if($r2['status']) $this->assign('list',$r2['info']['list']);
        $this->assign('msg',$r['msg']);
        $this->assign('info',$r['info']);
        $this->display();
    }
    /**
     * ajax执行抽奖
     * @param  int      the game id
     * @return [json]
    */
    public function roll(){
        // echo '2323';exit;
        echo json_encode(apiCall(GameApi::ROLL,array($this->uid,$this->gid)));
    }

    // public function rolltest(){
    //     echo json_encode(apiCall(GameApi::ROLL,array(82,2)));
    // }

    // public function test(){
        // echo json_encode(apiCall(GameApi::TEST,array($uid,$gid)));
        // // echo date('y-m-d',1459390374);
    // }
}