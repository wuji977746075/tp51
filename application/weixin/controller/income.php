<?php
/**
 * Created by PhpStorm.
 * User: 64
 * Date: 2017/3/23 0023
 * Time: 14:26
 */
namespace app\weixin\controller;
use app\src\goods\logic\ProductSkuLogic;
use app\src\goodsstock\logic\GoodsStockLogic;
use app\src\product\logic\ProductclassifyLogic;
use app\src\user\action\RelationAction;
use app\src\user\logic\UcenterMemberLogic;
use app\src\user\logic\UserGroupLogic;
use app\src\user\logic\MemberLogic;
use app\src\user\logic\UserMemberLogic;
use app\src\user\logic\MemberConfigLogic;
use app\src\wallet\logic\WalletLogic;
use app\src\wallet\logic\WalletOrderLogicV2;
use app\src\wallet\logic\WalletHisLogicV2;
use app\src\wxpay\logic\WxaccountLogic;
use app\pc\helper\PcFunctionHelper;
use app\src\product\logic\ProductimageLogic;
use app\src\shoppingCart\action\ShoppingCartQueryAction;
use app\src\product\logic\ProductLogic;
use app\src\user\logic\UserRelationLogic;
use app\weixin\Logic\WeixinLogic;
use think\Db;
class Income
{
    //收益处理
    public function test(){
        $this->ahead_income('196','1','1','2','8');
    }


    //前级收益和前前级收益
    /*
     *我的uid，给上级的收益数量，给上级的单个收益钱数，给上上级的收益钱数
     */
    public function ahead_income($my_uid,$count,$each_ahead,$each_double_ahead,$pid=''){
        $each_ahead=$each_ahead*100;
        $each_double_ahead=$each_double_ahead*100;
        if(!empty($pid)){
            $classify=(new ProductclassifyLogic())->getInfo(['pid'=>$pid]);
            $each_ahead=$classify['info']['each_ahead'];
            $each_double_ahead=$classify['info']['each_double_ahead'];
        }

        //查询我的前级
        $upper_count=0;
        $me=(new UserMemberLogic())->getInfo(['uid'=>$my_uid]);
        $my_ahead_uid=$me['info']['father_uid'];
        $my_ahead=(new UserMemberLogic())->getInfo(['uid'=>$my_ahead_uid]);
        $my_ahead_type=$my_ahead['info']['type'];
        if($count==267)$count=237;
        if(!empty($my_ahead_uid)&&$my_ahead_type!==1){
            //如果我有前级，进行收益增加操作
            $note='后级消费，获得奖励，增加提现积分';
            $income_ahead=$count*$each_ahead;
            $a=$this->wallet_update($note,$income_ahead,'1','0',$my_ahead_uid,config('datatree.ahead_plus'));
            $upper_count=$income_ahead;
            if(!$a) return(['status'=>false]);
            //查询是否有前前级
            $my_double_ahead_uid=$my_ahead['info']['father_uid'];
            $my_double_ahead=(new UserMemberLogic())->getInfo(['uid'=>$my_double_ahead_uid]);

            if(!empty($my_double_ahead_uid)&&$my_double_ahead['info']['type']!==1&&($me['info']['group_id']!==1)){
                //如果我有前级，进行收益增加操作
                $note='后后级消费，获得奖励，增加提现积分';
                $income_double_ahead=$count*$each_double_ahead;
                $b=$this->wallet_update($note,$income_double_ahead,'1','0',$my_double_ahead_uid,config('datatree.double_ahead_plus'));
                $upper_count=$upper_count+$income_double_ahead;
                if(!$b) return(['status'=>false]);

            }
        }

        return(['status'=>true,'count'=>$upper_count]);
    }



    //type:0库存积分，1提现积分，
    //status：0加积分，1减积分
    public function wallet_update($note,$money,$type,$status,$uid,$data_tree='0'){

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
        $order=empty(session('order'))?0:session('order');
        if($type==0) {
            $miu_log_info = [
                'uid'           => $uid,
                'before_points' => $wallet['info']['stock_points'],
                'points_type'   => '0',
                'after_money'   => $after_money,
                'create_time'   => time(),
                'reason'        => $note,
                'dtree_type'    => $data_tree,
                'wallet_type'   => '0',
                'order'=>$order
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
                'dtree_type'    =>  $data_tree,
                'wallet_type'   => '0',
                'order'=>$order
            ];
            if($status==0) {$miu_log_info['plus']=$money;$miu_log_info['minus']='0';}
            if($status==1) {$miu_log_info['plus']='0';$miu_log_info['minus']=$money;}
        }

        $miu_log=(new WalletHisLogicV2())->add($miu_log_info,'id');

        if($miu['status']&&$miu_log){
            return true;
        }
    }


    //库存管理-增加库存
    /*
     * 用户购买身份商品，增加一定数量库存
     * pid=pid_type
     */
    public function add_goods_stock($uid,$pid,$count){
        //判断该用户是否已有此商品库存
        $order=empty(session('order'))?0:session('order');
        $goods=(new GoodsStockLogic())->getInfo(['uid'=>$uid,'pid'=>$pid]);
        if(!empty($goods['info'])){
            //若已有该库存，则增加
            $new_count=$goods['info']['count']+$count;
            $update_time=time();
            $entity=[
                'count'=>$new_count,
                'update_time'=>$update_time
            ];
            $save_goods=(new GoodsStockLogic())->save(['id'=>$goods['info']['id']],$entity);
            if($save_goods){
                return true;
            }else{
                return false;
            }
        }else{
            //若没有该库存，则新增
            $entity=[
                'uid'=>$uid,
                'count'=>$count,
                'pid'=>$pid,
                'create_time'=>time(),
                'update_time'=>time(),
                'order'=>$order
            ];
            $add_goods=(new GoodsStockLogic())->add($entity,'');
            if($add_goods){
                return true;
            }else{
                return false;
            }
        }
    }

    /*
     * 消耗库存
     */
    public function mius_goods_stock($uid,$pid,$group_pid,$stock_points,$count,$price,$note='',$datetree=''){
        $goods=(new GoodsStockLogic())->getInfo(['uid'=>$uid,'pid'=>$group_pid]);
        $classify=(new ProductclassifyLogic())->getInfo(['pid'=>$group_pid]);
        $type=$classify['info']['type'];
        if($stock_points==0) $this->error('数据出错，库存查询');
        $user_member=(new UserMemberLogic())->getInfo(['uid'=>$uid]);
        $role=$user_member['info']['group_id'];
        $product=(new ProductSkuLogic())->getInfo(['product_id'=>$group_pid]);
        if($type==1){
            $price=$product['info']['price']*0.95;
        }else{
            if($role==4) $price=$product['info']['price']*0.95;
            if($role==1) $price=$classify['info']['angel_price'];
            if($role==2) $price=$classify['info']['elite_price'];
            if($role==3) $price=$classify['info']['leader_price'];
        }
        if($count==0){
            $count=floor($stock_points/$price);
        }

        $order=empty(session('order'))?0:session('order');
        if(!empty($goods['info'])){
            if($goods['info']['count']>=$count){
                //库存足够

                $entity=[
                    'count'=>$goods['info']['count']-$count,
                    'update_time'=>time(),
                    'order'=>$order
                ];
                $save=(new GoodsStockLogic())->save(['id'=>$goods['info']['id']],$entity);
                if($save) return true;
                return false;
            }else{
                //库存不足，库存加库存积分
                $wallet=(new WalletLogic())->getInfo(['uid'=>$uid]);
                if(!$wallet['status']){
                    return false;
                }else{
                    $urplus=($count-$goods['info']['count'])*$price;

                    if($wallet['info']['stock_points']>=$urplus){
                        $order=empty(session('order'))?0:session('order');
                        $entity=[
                            'count'=>0,
                            'update_time'=>time(),
                            'order'=>$order
                        ];
                        $save=(new GoodsStockLogic())->save(['id'=>$goods['info']['id']],$entity);
                        //减掉库存
                        //减掉积极分
                        $this->wallet_update($note,$urplus,'0','1',$uid,$datetree);
                        if($save) return true;
                        return false;
                    }else{
                        return false;
                    }
                }
            }
        }else{
            //库存不足，积分是否足够
            $wallet=(new WalletLogic())->getInfo(['uid'=>$uid]);
            if(!$wallet['status']){
                return false;
            }else{
                if($wallet['info']['stock_points']>=$stock_points){
                    $order=empty(session('order'))?0:session('order');
                    $entity=[
                        'count'=>$goods['info']['count']-$count,
                        'update_time'=>time(),
                        'order'=>$order
                    ];
                    $save=(new GoodsStockLogic())->save(['id'=>$goods['info']['id']],$entity);
                    //减掉库存
                    //减掉积极分
                    $this->wallet_update($note,$stock_points,'0','1',$uid,$datetree);
                    if($save) return true;
                    return false;
                }else{
                    return false;
                }
            }
        }
    }

    /*
     * 判断库存是否足够
     */
    public function is_enough($uid,$money=0,$count=0,$price=0,$pid,$group_pid=0){
        if($group_pid==0){
            $pid_classify=(new ProductclassifyLogic())->getInfo(['pid'=>$pid]);
            $group_pid=$pid_classify['info']['pid_type'];
        }

        $classify=(new ProductclassifyLogic())->getInfo(['pid'=>$group_pid]);
        $type=$classify['info']['type'];
        if($money==0) $this->error('数据出错，库存查询');
        $user_member=(new UserMemberLogic())->getInfo(['uid'=>$uid]);
        $role=$user_member['info']['group_id'];
        $product=(new ProductSkuLogic())->getInfo(['product_id'=>$group_pid]);

        if($type==1){
            $price=$product['info']['price']*0.95;
        }else{
            if($role==4) $price=$product['info']['price']*0.95;
            if($role==1) $price=$classify['info']['angel_price'];
            if($role==2) $price=$classify['info']['elite_price'];
            if($role==3) $price=$classify['info']['leader_price'];
        }
        if($count==0){
            $count=floor($money/$price);
        }

        $goods=(new GoodsStockLogic())->getInfo(['uid'=>$uid,'pid'=>$classify['info']['pid_type']]);

        if(!empty($goods['info'])){
            if($goods['info']['count']>=$count){
                //库存足够
                return true;
            }else{
                //库存不足，库存加库存积分
                $wallet=(new WalletLogic())->getInfo(['uid'=>$uid]);
                if(!$wallet['status']){
                    return false;
                }else{
                    $urplus=$money-$count*$price;
                    if($wallet['info']['stock_points']>=$urplus){
                       return true;
                    }else{
                        return false;
                    }
                }
            }
        }else{
            //没有库存，积分是否足够
            $wallet=(new WalletLogic())->getInfo(['uid'=>$uid]);

            if(!$wallet['status']){
                return false;
            }else{
                if($wallet['info']['stock_points']>=$money){
                   return true;
                }else{
                    return false;
                }
            }
        }
    }


}