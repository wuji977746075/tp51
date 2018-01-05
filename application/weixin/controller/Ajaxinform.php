<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 吃草的鱼 <783188184@qq.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\weixin\controller;
use app\src\user\logic\MemberConfigLogic;
use app\src\wallet\logic\WalletOrderLogicV2;
use app\src\wallet\logic\WalletHisLogicV2;
use app\src\wallet\logic\WalletLogic;
use app\src\wxpay\logic\WxaccountLogic;
use Think\Controller;
use Weixin\Api\WxuserApi;
use Weixin\Api\WxaccountApi;
use Common\Api\WeixinApi;
use app\weixin\controller\Order;
use app\wxapp\controller\BaseController;
use think\Db;
class Ajaxinform extends Base
{
    public $newmember;

    public $role;

    public function __construct()
    {
        parent::__construct();
        // $this->getConfig();
        $result=(new WxaccountLogic())->getInfo(['id'=>1]);
        if ($result['status'] && is_array($result['info'])) {
            $this->wxaccount = $result['info'];
        } else {
            exit("公众号信息获取失败，请重试！");
        }
    }
    /*
     * 异步结果通知地址
     * */
    public function ajaxinform(){
        ignore_user_abort(TRUE);
        //如果客户端断开连接，不会引起脚本abort
        set_time_limit(0);
        $xml = file_get_contents("php://input");
        if(!$xml){
            $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        }
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if(empty($array_data['out_trade_no'])){
            echo "<xml>
                    <return_code><![CDATA[FAIL]]></return_code>
                    <return_msg><![CDATA[数据接收失败]]></return_msg>
                 </xml>";
        }else{

            $order_code = $array_data['out_trade_no'];
            $wallet_order=(new WalletOrderLogicV2())->getInfo(['order_code'=>$order_code]);
            if(!empty($wallet_order['info'])){
                if(!empty($wallet_order['info']['pay_code'])){
                    echo "<xml>
                    <return_code><![CDATA[FAIL]]></return_code>
                    <return_msg><![CDATA[数据已经回调处理]]></return_msg>
                 </xml>";
                    exit;
                }
            }



            //开启事务
            Db::startTrans();
            $flag = false;
            $map['order_code'] = $array_data['out_trade_no'];//返回订单号
            $save['pay_type'] = '1';
            $save['pay_status'] = '1';
            $save['pay_code'] = $array_data['transaction_id'];//微信支付订单号
            $order_wall=(new WalletOrderLogicV2())->save($map,$save);
            if($order_wall){
                $wxmap['openid'] = $array_data['openid'];
                $orderres = (new MemberConfigLogic())->getInfo(['wxopenid'=>$wxmap['openid']]);
                if($orderres['status']){
                    $money=$array_data['total_fee'];
                    $note='用户充值加库存积分';
                    $uid=$orderres['info']['uid'];
                    $wallet=$this->wallet_update($note,$money,'0','0',$uid);
                    if($wallet){
                    }else{
                        $flag = true;
                    }
                }else{
                    $flag = true;
                }
            }else{
                $flag = true;
            }
            if($flag){
                Db::rollback();
                echo "<xml>
                    <return_code><![CDATA[FAIL]]></return_code>
                    <return_msg><![CDATA[数据接收失败]]></return_msg>
                 </xml>";
            }else{
                Db::commit();
                echo "<xml>
                      <return_code><![CDATA[SUCCESS]]></return_code>
                      <return_msg><![CDATA[OK]]></return_msg>
                  </xml>";
            }
        }

    }


public function test($note){
    $log_list=[
        'uid'=>123,
        'order_code'=>time(),
        'money'=>2222,
        'note'=>$note,
        'status'=>'1',
        'create_time'=>time(),
        'update_time'=>time(),
        'from'=>'1',
        'pay_type'=>1,
        'pay_status'=>0,
        'pay_money'=>'0',
        'pay_code'=>'',
        'trade_no'=>''
    ];
    $order_wall=(new WalletOrderLogicV2())->add($log_list);
}



    public function wallet_update($note,$money,$type,$status,$uid){

        $wallet=(new WalletLogic())->getInfo(['uid'=>$uid]);

        //扣除积分
        $wallet=(new WalletLogic())->getInfo(['uid'=>$uid]);
        $update_time=time();
        if($type==0){
            if($status==0){
                $after_money=$wallet['info']['stock_points']+$money;

                $wallet_enity=['stock_points'=>$after_money,'update_time'=>$update_time];
            }
            if($status==1){
                $after_money=$wallet['info']['stock_points']-$money;
                $wallet_enity=['stock_points'=>$after_money,'update_time'=>$update_time];
            }
        }elseif($type==1){
            if($status==0){
                $after_money=$wallet['info']['cash_points']+$money;
                $wallet_enity=['cash_points'=>$after_money,'update_time'=>$update_time];
            }
            if($status==1){
                $after_money=$wallet['info']['cash_points']-$money;
                $wallet_enity=['cash_points'=>$after_money,'update_time'=>$update_time];
            }
        }

        $miu=(new WalletLogic())->save(['uid'=>$uid],$wallet_enity);

        if($type==0) {
            $miu_log_info = [
                'uid'           => $uid,
                'before_points' => $wallet['info']['stock_points'],
                'points_type'   => '0',
                'after_money'   => $after_money,
                'create_time'   => time(),
                'reason'        => $note,
                'dtree_type'    => 0,
                'wallet_type'   => '0',
                'order'=>'-1'
            ];
            if($status==0) {$miu_log_info['plus']=$money;$miu_log_info['minus']='0';}
            if($status==1) {$miu_log_info['plus']='0';$miu_log_info['minus']=$money;}

        }else{
            $miu_log_info = [
                'uid'           => $uid,
                'before_points' => $wallet['info']['cash_points'],
                'points_type'   => '1',
                'after_money'   => $after_money,
                'create_time'   => time(),
                'reason'        => $note,
                'dtree_type'    => 0,
                'wallet_type'   => '0',
                'order'=>'-1'
            ];
            if($status==0) {$miu_log_info['plus']=$money;$miu_log_info['minus']='0';}
            if($status==1) {$miu_log_info['plus']='0';$miu_log_info['minus']=$money;}
        }

        $miu_log=(new WalletHisLogicV2())->add($miu_log_info,'id');

        if($miu['status']&&$miu_log){
            return true;
        }
    }

    /*
   * 要放在回调中处理
  * 判断一次性消费多少 进行等级升级
   * 天使会员和股东会员默认不进行该操作
   * 被邀请的会员会进行相应的操作
  * */
    public function upgrade($fee){
        $vip = $this->newmember['vip_type'];
        $gradeid = $this->newmember['role_grade'];
        if($vip == 1){
            $map['consume_money'] = array(
                'ELT' ,$fee
            );

            $res = apiCall(RoleApi::GET_INFO,array($map,'save_money desc'));
            if($res['status']){
                if($gradeid < $res['info']['id'] && $res['info']['id'] > 1){
                    //进行升级
                    $save = array(
                        'role_grade' => $res['info']['id'],
                        'invite_power' => '1',
                        's_gold_mumber' => $res['info']['become_gold'],
                        's_platina_mumber'=> $res['info']['become_platina'],
                    );
                    $newmap['wx_id'] = $this->newmember['wx_id'];
                    $res = apiCall(NewmemberApi::SAVE,array($newmap,$save));
                    //结果测试即可
                }
            }
        }

    }

    /*
     * 进行VIP消费判断 看看是否是被邀请的人
     * */
    public function vip_consume($invid,$fee){
        $map['wx_id'] = $invid;
        $userinfo = apiCall(NewmemberApi::GET_INFO,array($map));
        if($userinfo['status']){
            $vip = $userinfo['info']['vip_type'];
            $grade = $userinfo['info']['role_grade'];
            if($vip == 1){
                $rolemap['id'] = $grade;
                $roleres = apiCall(RoleApi::GET_INFO,array($rolemap));
                if($roleres['status']){
                    $consume_reward = $roleres['info']['consume_reward'];
                    //判断一下改用户第一次储值的钱是否已经用光了   用光了储值消费也算到消费奖励里面
                    $stroelogmap = array(
                        'uid'=>$this->newmember['wx_id']
                    );
                    $storelogres = apiCall(UserLogApi::GET_INFO,array($stroelogmap,'time asc'));
                    if($storelogres['status']){
                        $firststore = $storelogres['info']['number'];
                        $buylogmap = array(
                            'op_type'=>'98',
                            'uid'=>$this->newmember['wx_id'],
                            'time'=>array('LT',time())
                        );
                        $buylogres = apiCall(UserLogApi::SUM,array($buylogmap));
                        if($buylogres['status']){
                            if(($buylogres['info']+$firststore)<0){
                                $rewardmoney = $fee*$consume_reward;
                                $moneymap['id'] = $map['wx_id'];
                                $this->addmoney($moneymap,$rewardmoney);
                            }
                        }
                    }

                }
            }

        }
    }


    /*
     * 消费获得相应积分
     * */
    public function add_points($fee){
        $nmap['wx_id'] = $this->newmember['wx_id'];
        $save['points'] = $this->newmember['points']+$fee*$this->role['integral_multiple'];
        $pointres = apiCall(NewmemberApi::SAVE,array($nmap));
        if($pointres['status']){

            $add = array(
                'op_type' => '95',
                'number' => $fee*$this->role['integral_multiple'],
                'time' => time(),
                'uid' => $nmap['wx_id'],
                'note' => '支付现金得到积分',
                'num_type' => '0'
            );

            $logres = apiCall(UserLogApi::ADD,array($add));
        }

    }

    /*
     * 增加支付记录
     * */
    public function consume_record($fee){
        $add = array(
            'op_type' => '94',
            'number' => $fee,
            'time' => time(),
            'uid' => $this->newmember['wx_id'],
            'note' => '现金支付',
            'num_type' => '3'
        );
        $logres = apiCall(UserLogApi::ADD,array($add));
    }


    /*
    * 消费奖励
    * */
    public function addmoney($map,$rewardmoney){
        $wxuserres = apiCall(WxuserApi::GET_INFO,array($map));
        if($wxuserres['status']){
            $lastmoney = $wxuserres['info']['money'];
            $nowmoney = bcadd($lastmoney,$rewardmoney,2);
            $save = array(
                'money' => $nowmoney,
            );
            $res = apiCall(WxuserApi::SAVE,array($map,$save));

            //增加账户能体现的记录
            if($res['status']){
                $add = array(
                    'op_type' => '96',
                    'number' => $rewardmoney,
                    'time' => time(),
                    'uid' =>$map['id'],
                    'note' => '消费奖励',
                    'num_type' => '2'
                );
                $addlogres = apiCall(UserLogApi::ADD,array($add));
            }
        }
    }


}
