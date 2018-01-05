<?php
/**
 * Created by PhpStorm.
 * User: boye
 * Date: 2017/3/1
 * Time: 9:23
 */

namespace app\weixin\controller;


use app\admin\controller\AuthGroupAccess;
use app\src\address\logic\AddressLogic;
use app\src\buyorder\logic\BuyOrderLogic;
use app\src\goods\logic\ProductImageLogic;
use app\src\goods\logic\ProductLogic;
use app\src\goods\logic\ProductSkuLogic;
use app\src\order\logic\OrdersContactinfoLogic;
use app\src\order\logic\OrdersLogic;
use app\src\powersystem\logic\AuthGroupAccessLogic;
use app\src\powersystem\logic\AuthGroupLogic;
use app\src\product\logic\ProductclassifyLogic;
use app\src\role\logic\RoletaskLogic;
use app\src\role\model\RoleTask;
use app\src\user\logic\UserLogLogic;
use app\src\user\logic\UserMemberLogic;
use app\src\user\model\UserMember;
use app\src\wallet\logic\WalletHisLogicV2;
use app\src\wallet\logic\WalletLogic;
use app\src\wallet\model\Wallet;
use PayPal\Api\Address;
use think\Db;

class Order extends Home {
    public function index() {
        $this->assignTitle('我的订单');
        $status = $this->_param('status', 0);;
        /**
         *  0: 全部订单 1: 待付款 2:待发货 3: 待收货 7: 待评价
         */
        if (!in_array($status, [0, 1, 2, 3, 7])) $status = 0;
        $list[0] = [
            'id'           => '1',
            'order_code'   => '1',
            'note'         => '11111111',
            'price'        => 99,
            'query_status' => '0',
            'items_count'  => '1',
            'items'        => [
                [
                    'id'    => '1',
                    'name'  => '11',
                    'img'   => '',
                    'price' => 99,
                    'count' => '1'
                ]

            ],
        ];
        $this->assign('status', $status);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function detail() {
        $this->assignTitle('订单详情');
        return $this->fetch();
    }


    /*
       * 是否有购买所有商品的次数及权限的判断
       * */
    public function buy_power() {
        //若是非手机注册用户，则什么也不能购买；
        $is_mobile_login = session('is_login');
        if ($is_mobile_login == 1) {
        } else {
            $this->error('您的手机号没有注册', 'user/mobile_register');
        }

        $role = $this->role;
        $type = input('type');

        //若在48小时任务内，则任何商品不可购买
        $uid       = $this->memberinfo['id'];
        $role_task = (new RoletaskLogic())->getInfo(['from_uid' => $uid,'type'=>['in','1,3,4,5'],'status'=>'0']);
        if (!$role_task['status']) $this->error('查询出错');
        if (!empty($role_task['info'])) $this->error('您有升级定时任务，暂时不可购买');

        //当前身份与商品属性
        //商品类型1：不计入积分商品2：计入积分普通商品3：计入积分,天使级商品4：精英5：领袖

        if (empty($type)) {
            return true;
        }

        if ($type == 1) {//不计入积分商品，所有身份可以购买
            return true;
        } elseif ($type == 2) {//计入积分商品，所有身份可以购买
            return true;
        } elseif ($type == 3) {//天使商品，只有注册会员可以购买
            if ($role == 4) return true;
            $this->error('当前身份不可购买此商品,1-1');
        } elseif ($type == 4) {//精英商品，只有天使和注册会员可以购买
            if ($role == 4 || $role == 1) return true;
            $this->error('当前身份不可购买此商品,1-2');
        } elseif ($type == 5) {//领袖商品，领袖不能购买
            if ($role == 3) $this->error('当前身份不可购买此商品,1-3');
            return true;
        }

    }


    /*
     * 订单信息确认
     * */
    public function checkout() {
        $this->assignTitle('确认订单');
        session('pid', input('pid'));
        session('count', input('count','1'));
        session('type', input('type'));
        session('buy_status', input('buy_status'));
        //session('sku_id', input('sku_id'));
        $isbuy = $this->buy_power();
        if (!$isbuy) $this->error('当前状态不可购买此商品', 'index/index');
        //收货地址的判断
        $addremap['uid'] = $this->memberinfo['id'];
        //判断是否有可供选择的地址没有就需要新建
        $hasaddress = (new AddressLogic())->count($addremap);

        if ($hasaddress['status']) {
            $this->assign('addrecount', $hasaddress['info']);
        }

        //判断是否进行地址选择如果选择了就用选择的 没有选择用默认的
        $default_id = $this->address;


        $default_info = (new AddressLogic())->getInfo(['id'=>$default_id]);
        if(empty($default_id))$default_id='0';
        $this->assign('default_id',$default_id);

        $default_info=($default_info['info']);
        $this->assign('default_info', $default_info);

        $uid=$this->memberinfo['id'];
        $addressmap['uid']=$uid;
        $selectres = (new AddressLogic())->queryNoPaging($addressmap);
        if ($selectres['status']) {
            $address=json_encode($selectres['info']);
            //session('addressid', $selectres['info']['id']);//为的是在生成订单时获得相应的地址信息的方便
            $this->assign('address',$address);

        }




        $pid    = !empty(input('pid')) ? input('pid') : session('pid');
        $count  = !empty(input('count')) ? input('count') : session('count');
        $type  = !empty(input('type')) ? input('type') : session('type');
        $buy_status  = !empty(input('buy_status')) ? input('buy_status') : session('buy_status');
        $this->assign('pid', $pid);
        $this->assign('count', $count);

        //判断一下商品数量
        $this->assign('buy_status',$buy_status);
        $this->assign('type',$type);

        //sku信息

        if (!empty($sku_id)) {
            $quantity = (new ProductSkuLogic())->getInfo(array('sku_id' => $sku_id, 'product_id' => $pid));
        } else {
            $quantity = (new ProductSkuLogic())->getInfo(array('product_id' => $pid));
        };


        if ($quantity['status']) {
            if ($quantity['info']['quantity'] < $count) {
                $this->error('对不起库存不足', 'index/index');
            }
            $this->assign('skuid', $quantity['info']['id']);
            $this->assign('sku_desc', $quantity['info']['sku_desc']);
        }

        //product信息
        //$pinfores = apiCall(ProductApi::GET_INFO,array(array('id'=>$pid)));
        $pinfores = (new ProductLogic())->getInfo(['id' => $pid]);

        if ($pinfores['status']) {
            $this->assign('info', $pinfores['info']);
        }

        //查询真实积分
        $role = $this->role;

        $price['real_price'] = $this->get_real_price($type, $role, $quantity['info']);
        $price['price']      = $quantity['info']['price'];

        $price['totalprice'] = $price['real_price'] * $count;
        $this->assign('price', $price);

        //获取商品图片
        $main_img_type = 6015;

        $result = (new ProductImageLogic())->queryNoPaging(['pid'=>$pid,'type'=>'6015']);
        if(!$result['status']){
            return $result;
        }
        $main_img = "";
        foreach($result['info'] as $vo){
            if($vo['type'] == $main_img_type){
                $main_img = $vo['img_id'];
            }
        }
        $this->assign('img',$main_img);

//        var_dump($main_img);
//        var_dump($selectres['info']);
//        echo('###########hasaddress');
//        var_dump($hasaddress['info']);
//        echo('###########pinfores');
//        var_dump($pinfores['info']);
//        echo('###########');
//        var_dump($price);

        return $this->fetch();
    }

    //提取身份商品的购买价格，提取积分商品的身份价格
    public function get_real_price($type, $role, $skuinfo) {
        $pid     = $skuinfo['product_id'];
        $o_price = $skuinfo['price'];

        $classify = (new ProductclassifyLogic())->getInfo(['pid' => $pid]);
        if (!$classify['status']) $this->error('查询出错301');


        if (empty($classify['info'])) $this->error('查询出错302');



        if ($role == 4) $price = $o_price * 0.95;
        if ($role == 1) $price = $classify['info']['angel_price'];
        if ($role == 2) $price = $classify['info']['elite_price'];
        if ($role == 3) $price = $classify['info']['leader_price'];

        if (!in_array($role, [1,2,3,4,99])) $this->error('获取身份出错');
        if ($type == 1) {
            return ($o_price);
        } elseif ($type == 2) {
            if ($role == 4) $price = $o_price * 0.95;
            if ($role == 1) $price = $classify['info']['angel_price'];
            if ($role == 2) $price = $classify['info']['elite_price'];
            if ($role == 3){$price =$classify['info']['leader_price'];}
            return($price);
        } else {
            return ($o_price);
        }
    }

    //提取身份商品的提货身份价格
    public function get_stock_price($role,$skuinfo){
        $pid     = $skuinfo['product_id'];
        $classify = (new ProductclassifyLogic())->getInfo(['pid' => $pid]);
        if (!$classify['status']) $this->error('查询出错301');
        if (empty($classify['info'])) $this->error('查询出错302');

        if ($role == 4) $this->error('出错了，代码404');
        if ($role == 1) $this->error('出错了，代码401');
        if ($role == 2) $this->error('出错了，代码402');
        if ($role == 3) $price = $classify['info']['leader_price'];
        return ($price);
    }



    public function need_address($address_id, $qulity){

        if(empty($address_id)||$address_id==0){
            $this->error('收货地址缺失');
        }

        $result =(new AddressLogic())->getInfo(['id'=>$address_id]);
        if($result['status']){
            if(is_null($result['info'])){
                $this->error('收货地址缺失');
            }
        }else{
            $this->error('收货地址缺失');
        }

        $address = $result['info'];
        unset($address['id']);

        $entity = array();

        //合并收货地址
        return($entity = array_merge($entity,$address));
    }

    /*
   * 订单生成
   *
   * */
    public function product_order() {
       // if($this->is_test()) $this->error('系统正在升级，请稍后再试');

        //if($this->memberinfo['id']!=196)$this->error('本功能暂不开放');
        //判断是否还有购买商品的权限

        //购买人的用户ID
        $uid        = $this->memberinfo['id'];
        $ids        = empty(input('pid'))?session('buy_se')['pid']:input('pid');
        $numarr     = empty(input('count'))?session('buy_se')['count']:input('count','1');
        $address_id = empty(input('addressid'))?session('buy_se')['address_id']:input('addressid');//收货地址ID
        $note       = input('note');




        //有购物车会出现多个物品一起提交

        $pay_type = 2; //支付类型 1:支付宝

        $qulity = (new ProductLogic())->getInfo(['id' => $ids]);
        if (!$qulity['status'] || empty($qulity['info'])) $this->error('读取不到商品信息');
        $type=$qulity['info']['type'];



        //1. 收货地址
        $entity = [];
        $entity = $this->need_address($address_id, $qulity);
        //联系人等信息



        //1. 本商城生成的支付编号
        $result = (new ProductSkuLogic())->getInfo(['product_id' => $ids]);
        if (!$result['status']) $this->error('读取不到商品信息');

        $price       = $this->get_real_price($qulity['info']['type'], $this->role, $result['info']);
        $total_price = $price * $numarr;

        $items = array();


        $name = $qulity['info']['name'];
        //$weight = $qulity['info']['weight'];
        $imgmap['pid']  = $result['info']['product_id'];
        $imgmap['type'] = '6015';
        $imgid          = (new ProductImageLogic())->getInfo($imgmap);

        $tmp = array(array(
            'p_id'              => $result['info']['product_id'],
            'name'              => $name,
            'ori_price'         => $result['info']['ori_price'],
            'price'             => $price,
            'count'             => $numarr,
            'sku_id'            => $result['info']['sku_id'],
            'psku_id'           => $result['info']['id'],
            'sku_desc'          => $result['info']['sku_desc'],
            'createtime'        => time(),
            'dt_origin_country' => $qulity['info']['dt_origin_country'],
            'weight' =>'',
            'taxrate'           => '0',
            'has_sku'           =>'0'
        ));

        if ($imgid['status']) {
            $img             = $imgid['info']['img_id'];
            $tmp['0']['icon_url'] = $img;
        }
        $items = $tmp;





        //为普通商品时

        $is_enough=$this->enough_money($total_price);
        if(!$is_enough){
            $buy_session=['type'=>$type,'pid'=>$ids,'count'=>$numarr,'address_id'=>$address_id,'buy_type'=>1];
            session('buy_se',$buy_session);
            $my_wallet=(new WalletLogic())->getInfo(['uid'=>$uid]);
            (new User())->direct_recharge($total_price-$my_wallet['info']['stock_points'],'1','1');
        }
        Db::startTrans();

        $order=$this->buy_order($uid,$ids,$numarr,$total_price);
        if(!empty($order)){
            session('order',$order['info']);
        }
        $flag = false;
        //增加积分日志和储值日志
        if($type==1){
            $note='购买非积分商品'.$note;
        }elseif($type==2){
            $note='购买积分商品'.$note;
        }elseif($type==3){
            $note='购买天使入门商品'.$note;
        }elseif($type==4){
            $note='购买精英入门商品'.$note;
        }elseif($type==5){
            $note='购买领袖入门商品'.$note;
        }

        $order = array(
            'uid'            => $this->memberinfo['id'],
            'order_code'     => getOrderid($uid),
            'price'          => $total_price,
            'post_price'     => '0',
            'note'           => $note,
            'pay_status'     => 1,
            'comment_status' => 0,
            'storeid'        => '0',
            'idcode'         => '0',
            'discount_money' => '0',
            'from'           => '1',
            'tax_amount'     => '0',
            'goods_amount'   => $total_price,
            'pay_type'       => 4,
            'pay_balance'    => '',
            'items'          => $items,
            'address_id'     => $address_id,
        );
        $entity = array_merge($entity, $order);
        $result =(new OrdersLogic())->add_Order($entity);

        //扣除积分
        if($type==1){
        $wallet=(new WalletLogic())->getInfo(['uid'=>$uid]);
        $last_points=$wallet['info']['stock_points']-$total_price;
        $wallet_enity=['stock_points'=>$last_points];
        $miu=(new WalletLogic())->save(['uid'=>$uid],$wallet_enity);
        $miu_log_info=[
            'uid'=>$uid,
            'before_points'=>$wallet['info']['stock_points'],
            'points_type'=>'1',
            'plus'=>'0',
            'minus'=>$total_price,
            'after_money'=>$last_points,
            'create_time'=>time(),
            'reason'=>$note,
            'dtree_type'=>0,
            'wallet_type'=>'0'
        ];
        $miu_log=(new WalletHisLogicV2())->add($miu_log_info);
        if(!$miu['status']) $flag=false;
        if(!$miu_log)$flag=false;
        }

        //检查收益
        if ($flag) {
            Db::rollback();
            $this->error('对不起 订单生成出现问题');
        } else {
            //若订单生成成功，那么收益分配，人物升级
            if($type==2){
                $income=$this->income('2',$ids,$numarr);
                if(!$income){
                    Db::rollback();
                    $this->error('收益处理出现问题');
                }
            }
            Db::commit();
            session('buy_se','');
            $this->success('下单成功', url('shop/product_list'));
        }


    }

    public function enough_money($total_money){
        $me=$this->memberinfo;
        $my_wallet=$me['wallet'];
        if($my_wallet['stock_points']<$total_money) return false;
        return true;
    }


    public function income($type,$pid,$count) {
             //非升级商品，产生收益
            if (empty($type) || empty($pid) || empty($count)) $flag=true;
            $can_buy = $this->buy_can($type, 0);
            if (!$can_buy) $flag=true;
            $my_uid        = $this->memberinfo['id'];
            $me            = (new UserMemberLogic())->getInfo(['uid' => $my_uid]);
            $my_upper_id   = $me['info']['goods_uid'];
            $my_upper      = (new UserMemberLogic())->getInfo(['uid' => $my_upper_id]);
            $my_upper_type = $my_upper['info']['type'];
            if (!empty($my_upper['info'])) {
                $upper_role = $my_upper['info']['group_id'];
            } else {
                $upper_role = 99;
            }

            $my_upper_wallet = (new WalletLogic())->getInfo(['uid' => $my_upper_id]);
            if (!empty($my_upper_wallet)) $my_upper_wallet_stock = $my_upper_wallet['info']['stock_points'];
            $role           = $this->role;
            $skuinfo        = (new ProductSkuLogic())->getInfo(['product_id' => $pid]);
            $my_price       = $this->get_real_price($type, $role, $skuinfo['info']);
            $my_total_money = $count * $my_price;
            $is_enough      = $this->enough_money($my_total_money);
            if (!$is_enough) $flag=true;


            $order = $this->buy_order($my_uid, $pid, $count, $my_total_money);
            if (!empty($order)) {
                session('order', $order['info']);
            }

            $flag = false;
            if ($type == 2) {
                //如果是普通货物，加货。则涉及从上级取货。
                if($upper_role==99){
                    $upper_price=99999;
                }else{
                    $upper_price       = $this->get_real_price($type, $upper_role, $skuinfo['info']);
                }
                $upper_total_money = $count * $upper_price;
                $my_price          = $this->get_real_price($type, $role, $skuinfo['info']);
                $my_total_money    = $count * $my_price;
                //我的库存积分增加，提现积分减少
//                $note           = '购买货物加库存积分';
//                $money          = $my_total_money;
//                $type           = '0';
//                $status         = '0';
//                $wallet_project = $this->wallet_update($note, $money, $type, $status, $my_uid, config('datatree.stock_points_plus'));
//                if (!$wallet_project) {
//                    $flag=true;
//                }
                $note           = '购买货物减库存积分';
                $money          = $my_total_money;
                $type           = '0';
                $status         = '1';

                $wallet_project = $this->wallet_update($note, $money, $type, $status, $my_uid, config('datatree.stock_points_minus'));

                if (!$wallet_project) {
                    $flag=true;
                }

                //处理前级和前前级收益
                $ahead = (new Income())->ahead_income($my_uid, $count, '6', '4',$pid);

                if ($ahead['status']) {
                    //上级出货人需要付出的提成收益
                    $upper_count = $ahead['count'];
                }

                //先判断上级库存够不够
                if ($my_upper_type != 1) {
                    //若上级是总部，则不用处理收益
                    $classify=(new ProductclassifyLogic())->getInfo(['pid'=>$pid]);
                    $is_enough=(new Income())->is_enough($my_upper_id,$upper_total_money,'1',$upper_price,$pid,$classify['info']['pid_type']);
                    if($is_enough) {
                    //if ($my_upper_wallet_stock >= $upper_total_money) {
                        //上级的库存是足够的，则上级减库存，加提现
                        $note           = '下级补充货减库存积分';
                        $money          = $upper_total_money;
                        $type           = '0';
                        $status         = '1';
                        $wallet_project =(new Income())->mius_goods_stock($my_upper_id,$pid,$classify['info']['pid_type'],$money,'1',$upper_price,$note,config('datatree.after_buy_minus'));
                        //$wallet_project = $this->wallet_update($note, $money, $type, $status, $my_upper_id, config('datatree.after_buy_minus'));
                        if (!$wallet_project) {
                            $flag=true;
                        }
                        $note  = '下级补充货加提现积分';
                        $money = $my_total_money;
                        if (!empty($upper_count)) $money = $my_total_money - $upper_count;
                        $wallet_project = $this->wallet_update($note, $money, '1', '0', $my_upper_id, config('datatree.after_buy_plus'));
                        if (!$wallet_project) {
                            $flag=true;
                        }
                    } else {
                        //上级库存不足，通知上级，自我库存补上。并使上级开启48小时任务
                        //开启定时任务
                        if (!empty($upper_count)) $my_total_money = $my_total_money - $upper_count;
                        $add_task = (new \app\src\task\logic\RoleTaskLogic())->add_task($my_upper_id, $my_uid, '2', $my_total_money, $upper_total_money, '0',$pid);
                        if (!$add_task) {
                            $flag=true;
                        }
                    }
                }
            }

        return true;
    }

    public function is_test(){
        $auth=(new AuthGroupAccessLogic())->queryNoPaging(['group_id'=>5]);
        $array=array_column($auth['info'],'uid');
        if(!in_array($this->memberinfo['id'],$array)) return true;
        return false;
    }

    //补货和升级商品，都是从上级取货
    //  $type，￥uid，￥
    public function up_level(){

        //if($this->is_test()) $this->error('系统正在升级，请稍后再试');
        $type=empty(input('type'))?session('buy_se')['type']:input('type');
        $pid=empty(input('pid'))?session('buy_se')['pid']:input('pid');
        $count=empty(input('count'))?session('buy_se')['count']:input('count','1');
        $address_id=empty(input('addressid'))?session('buy_se')['address_id']:input('addressid');

        if(empty($type)||empty($pid)||empty($count)) $this->error('选择有误');

        $can_buy=$this->buy_can($type,1);

        if(!$can_buy) $this->error('选择有误');
        $my_uid=$this->memberinfo['id'];
        $me=(new UserMemberLogic())->getInfo(['uid'=>$my_uid]);
        $my_or_type=$me['info']['group_id'];
        $my_upper_id=$me['info']['goods_uid'];
        $my_recommend=$me['info']['recommender'];
        $my_upper=(new UserMemberLogic())->getInfo(['uid'=>$my_upper_id]);
        $my_upper_type=$my_upper['info']['type'];
        if(!empty($my_upper['info'])){
            $upper_role=$my_upper['info']['group_id'];
        }else{
            $upper_role=99;
        }
        $classify=(new ProductclassifyLogic())->getInfo(['pid'=>$pid]);
        $my_upper_wallet=(new WalletLogic())->getInfo(['uid'=>$my_upper_id]);
        $my_wallet=(new WalletLogic())->getInfo(['uid'=>$my_uid]);
        if(!empty($my_upper_wallet))$my_upper_wallet_stock=$my_upper_wallet['info']['stock_points'];
        $role=$this->role;
        $skuinfo=(new ProductSkuLogic())->getInfo(['product_id'=>$pid]);
        $my_price=$this->get_real_price($type,$role,$skuinfo['info']);
        $my_total_money=$count*$my_price;
        //$is_enough=$this->enough_money($my_total_money);
        //if(!$is_enough) $this->error('积分不足');
        if($my_total_money>$my_wallet['info']['stock_points']){

            $buy_session=['type'=>$type,'pid'=>$pid,'count'=>$count,'address_id'=>$address_id,'buy_type'=>2];
            session('buy_se',$buy_session);

            (new User())->direct_recharge($my_total_money-$my_wallet['info']['stock_points'],'1','2');
        };


        Db::startTrans();

        $order=$this->buy_order($my_uid,$pid,$count,$my_total_money);
        if(!empty($order)){
            session('order',$order['info']);
        }

        $flag=false;
        if($type==2){
            //如果是普通货物，加货。则涉及从上级取货。
            $this->error('系统升级，无需再存货物');
            $upper_price=$this->get_real_price($type, $upper_role, $skuinfo['info']);
            $upper_total_money=$count*$upper_price;
            $my_price=$this->get_real_price($type,$role,$skuinfo['info']);
            $my_total_money=$count*$my_price;
            //我的库存积分增加，提现积分减少
            $note='补充货加库存积分';$money=$my_total_money;$type='0';$status='0';
            $wallet_project=$this->wallet_update($note,$money,$type,$status,$my_uid,config('datatree.stock_points_plus'));
            if(!$wallet_project){
                Db::rollback();
                $this->error('操作失败，代码101');
            }
            $note='补充货减提现积分';$money=$my_total_money;$type='1';$status='1';
            $wallet_project=$this->wallet_update($note,$money,$type,$status,$my_uid,config('datatree.stock_points_minus'));
            if(!$wallet_project){
                Db::rollback();
                $this->error('操作失败，代码102');
            }

            //处理前级和前前级收益

            $ahead=(new Income())->ahead_income($my_uid,$count,'6','4',$pid);
            if($ahead['status']){
                //上级出货人需要付出的提成收益
                $upper_count=$ahead['count'];
            }

            //先判断上级库存够不够
            if($my_upper_type!=1){
                //若上级是总部，则不用处理收益
                if($my_upper_wallet_stock>=$upper_total_money){
                    //上级的库存是足够的，则上级减库存，加提现

                    $note='下级补充货减库存积分';$money=$upper_total_money;$type='0';$status='1';
                    $wallet_project=$this->wallet_update($note,$money,$type,$status,$my_upper_id,config('datatree.after_buy_minus'));
                    if(!$wallet_project){
                        Db::rollback();
                        $this->error('操作失败，代码103');
                    }
                    $note='下级补充货加提现积分';
                    $money=$my_total_money;
                    if(!empty($upper_count)) $money=$my_total_money-$upper_count;
                    $wallet_project=$this->wallet_update($note,$money,'1','0',$my_upper_id,config('datatree.after_buy_plus'));
                    if(!$wallet_project){
                        Db::rollback();
                        $this->error('操作失败，代码104');
                    }
                }else{
                    //上级库存不足，通知上级，自我库存补上。并使上级开启48小时任务
                    //开启定时任务

                    if(!empty($upper_count)) $my_total_money=$my_total_money-$upper_count;
                    $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($my_upper_id,$my_uid,'2',$my_total_money,$upper_total_money,'0',$pid);
                    if(!$add_task){
                        Db::rollback();
                        $this->error('操作失败，代码105');
                    }
                }}
            Db::commit();

        }elseif($type==3){
            //升级到天使
            $my_price=$this->get_real_price($type,$role,$skuinfo['info']);
            $my_total_money=$count*$my_price;
            //我的积分
            $note='升级到天使减库存积分';$money=$my_total_money;$type='0';$status='1';
            $wallet_project=$this->wallet_update($note,$money,$type,$status,$my_uid,config('datatree.become_angel_minus'));
            if(!$wallet_project){
                Db::rollback();
                $this->error('操作失败');
            }
            //要通过订单
            $uid=$my_uid;$ids=$pid;$numarr=$count;$note=$note;

            $this->angel_order($uid,$ids,$numarr,$address_id,$note);
            $my_recommend_people=(new UserMemberLogic())->getInfo(['uid'=>$my_recommend]);
            $recommender_role_now=$my_recommend_people['info']['group_id'];

            //下级升级到天使，给予推荐奖励
            //首先推荐人不是平台
            if($my_recommend_people['info']['type']!=1) {
                $money          = $classify['info']['become_angel'];
                $note           = '下级升级到天使，给予推荐奖励加提现积分';
                $wallet_project = $this->wallet_update($note, $money, '1', '0', $my_recommend,config('datatree.recommend_plus'));
                //下级变成天使给予推荐奖励。
                if ($recommender_role_now != 1) {
                    //若我的推荐人不是天使，则给予我的推荐人奖励
                    $classify = (new ProductclassifyLogic())->getInfo(['pid' => $ids]);
                    if ($recommender_role_now == 2) {
                        $money       = $classify['info']['angel_price'];
                        $upper_price = $classify['info']['elite_price'];
                    }
                    if ($recommender_role_now == 3) {
                        $money       = $classify['info']['angel_price'];
                        $upper_price = $classify['info']['leader_price'];
                    }
                    $wallet = (new WalletLogic())->getInfo(['uid' => $my_recommend]);

                    $is_enough=(new Income())->is_enough($my_recommend,$money,'1',$upper_price,$pid,$classify['info']['pid_type']);
                    if($is_enough) {
                    //if ($wallet['info']['stock_points'] >= $upper_price) {

                        $note           = '下级升级到天使，给予差价奖励加提现积分';
                        $wallet_project = $this->wallet_update($note, $money, '1', '0', $my_recommend, config('datatree.after_become_angel_plus'));

                        $note           = '下级升级到天使，给予差价奖励减库存积分';
                        $money          = $upper_price;
                        (new Income())->mius_goods_stock($my_recommend,$pid,$classify['info']['pid_type'],$money,'1',$upper_price,$note,config('datatree.after_become_angel_minus'));
                        //$wallet_project = $this->wallet_update($note, $money, '0', '1', $my_recommend, config('datatree.after_become_angel_minus'));
                    }else{
                        $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($my_recommend,$my_uid,'2',$money,$upper_price,'0',$pid);
                        if(!$add_task){
                            Db::rollback();
                            $this->error('操作失败,错误301');
                        }
                    }
                } else {
                    //我的推荐人是天使，查询我的推荐人的上级，给予他差价奖励
                    $my_recommend_upper = $my_recommend_people['info']['goods_uid'];
                    if (!empty($my_recommend_upper)) {
                        //查看我推荐人的上级，若存在
                        $my_recommend_upper_people = (new UserMemberLogic())->getInfo(['uid' => $my_recommend_upper]);
                        if ($my_recommend_upper_people['info']['type'] != 1) {
                            $classify = (new ProductclassifyLogic())->getInfo(['pid' => $ids]);
                            if ($my_recommend_upper_people['info']['group_id'] == 2) {
                                $money       = $classify['info']['angel_price'];
                                $upper_price = $classify['info']['elite_price'];
                            }
                            if ($my_recommend_upper_people['info']['group_id'] == 3) {
                                $money       = $classify['info']['angel_price'];
                                $upper_price = $classify['info']['leader_price'];
                            }
                            //查询库存是否足够
                            $wallet = (new WalletLogic())->getInfo(['uid' => $my_recommend_upper]);
                            $is_enough=(new Income())->is_enough($my_recommend_upper,$upper_price,$classify['info']['count'],$upper_price,$pid);
                            if($is_enough) {
                            //if ($wallet['info']['stock_points'] > $upper_price) {
                                $note           = '下级升级到天使，给予差价奖励加提现积分';
                                $wallet_project = $this->wallet_update($note, $money, '1', '0', $my_recommend_upper,config('datatree.after_become_angel_plus'));


                                $note           = '下级升级到天使，给予差价奖励减库存积分';
                                (new Income())->mius_goods_stock($my_recommend_upper,$pid,$classify['info']['pid_type'],$upper_price,'1',$upper_price,$note,config('datatree.after_become_angel_minus'));
//                                $money          = $upper_price;
//                                $wallet_project = $this->wallet_update($note, $money, '0', '1', $my_recommend_upper, config('datatree.after_become_angel_minus'));




                            }else{
                                $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($my_recommend_upper,$my_uid,'2',$money,$upper_price,'0',$pid);
                                if(!$add_task){
                                    Db::rollback();
                                    $this->error('操作失败');
                                }
                            }
                        }
                    }
                }
            }
            if(!$wallet_project){
                Db::rollback();
                $this->error('操作失败');
            }

            $add=(new User())->become_agent($me,'1');
            if(!$add){
                Db::rollback();
                $this->error('操作失败');
            }

            Db::commit();
        }elseif($type==4||$type==5){
            //做升级或新增任务，升级到精英或领袖
            //升级，涉及上级库存问题。涉及自己库存问题。涉及升级任务问题，涉及库存任务问题。
            $my_price=$this->get_real_price($type,$role,$skuinfo['info']);
            $my_total_money=$count*$my_price;
            if($role==4){
                //新增，从注册会员升级到精英或领袖，如果是精英，那么有个领袖出货。如果是领袖，那么是系统出货
                //我的积分
                if($type==4)$note='升级到精英减库存积分';
                if($type==5)$note='升级到领袖减库存积分';
                $money=$my_total_money;$money_type='0';$status='1';
                $wallet_project=$this->wallet_update($note,$money,$money_type,$status,$my_uid,config('datatree.become_elite_minus'));
                if(!$wallet_project){
                    Db::rollback();
                    $this->error('操作失败');
                }
                //增加用户的库存
                $stock_pid=$classify['info']['pid_type'];
                $stock_count=$classify['info']['count'];
                $add_goods=(new Income())->add_goods_stock($my_uid,$stock_pid,$stock_count);
                if(!$add_goods){
                    Db::rollback();
                    $this->error('操作失败');
                }
//                if($type==4)$note='升级到精英加库存积分';
//                if($type==5)$note='升级到领袖加库存积分';
//                $money=$my_total_money;$money_type='0';$status='0';
//                $wallet_project=$this->wallet_update($note,$money,$money_type,$status,$my_uid,config('datatree.become_elite_plus'));
//                if(!$wallet_project){
//                    Db::rollback();
//                    $this->error('操作失败');
//                }



                //建立我的关系
                if($type==4) $role_after='2';
                if($type==5) $role_after='3';
                $add=(new User())->become_agent($me,$role_after);
                if(!$add){
                    Db::rollback();
                    $this->error('操作失败');
                }

                Db::commit();


                $me=(new UserMemberLogic())->getInfo(['uid'=>$my_uid]);
                $my_upper_id=$me['info']['goods_uid'];
                $my_upper_wallet=(new WalletLogic())->getInfo(['uid'=>$my_upper_id]);
                if(!empty($my_upper_wallet['info']))$my_upper_wallet_stock=$my_upper_wallet['info']['stock_points'];
                //查看是否大于直接推荐人的等级
                $recommender=(new UserMemberLogic())->getInfo(['uid'=>$my_recommend]);
                $recommender_role=$recommender['info']['group_id'];
                //先判断推荐人是否是平台，若是平台则不用做奖励操作
                if($recommender['info']['type']!=1){
                    if($recommender_role>($type-2)&&!empty($my_upper_wallet['info'])){
                        //我的推荐人等级，大于我要升级的等级。推荐人库存减少，提现增加。
                        //先判断上级库存够不够
                        $upper_price=$this->get_stock_price('3', $skuinfo['info']);
                        $upper_total_money=$count*$upper_price;
                        $is_enough=(new Income())->is_enough($my_upper_id,$upper_total_money,$classify['info']['count'],$upper_price,$pid,$classify['info']['pid_type']);
                        if($is_enough) {
                        //if($my_upper_wallet_stock>=$upper_total_money) {
                            //上级的库存是足够的，则上级减库存，加提现
                            if(!empty($my_upper_id)){
                                $note           = '下级升级减库存积分';
//                                $money          = $upper_total_money;
//                                $type           = '0';
//                                $status         = '1';
//                                $wallet_project = $this->wallet_update($note, $money, $type, $status, $my_upper_id,config('datatree.after_become_elite_minus'));
//                                if (!$wallet_project) {
//                                    $this->error('操作失败');
//                                }

                                $mius_goods=(new Income())->mius_goods_stock($my_upper_id,$pid,$classify['info']['pid_type'],$upper_total_money,$classify['info']['count'],$upper_price,$note,config('datatree.after_become_elite_minus'));
                                if (!$mius_goods) {
                                    $this->error('操作失败');
                                }


                                $note           = '下级升级加提现积分';
                                $money          = $my_total_money;
                                $type           = '1';
                                $status         = '0';
                                $wallet_project = $this->wallet_update($note, $money, $type, $status, $my_upper_id,config('datatree.after_become_elite_plus'));
                                if (!$wallet_project) {

                                    $this->error('操作失败');
                                }}
                        }else{

                            $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($my_upper_id,$my_uid,'2',$my_total_money,$upper_total_money,'0',$pid);
                            if(!$add_task){

                                $this->error('操作失败');
                            }
                        }
                    }elseif($recommender_role<=($type-2)&&!empty($my_upper_wallet['info'])&&$recommender_role!=3){
                        //我直接升级到了我推荐人的上级或同级，提醒他升级，开启升级48小时任务。
                        //此笔收益不结算给我目前的上级，待48小时任务结束时结算
                        //my_uid,my_total_money,now_time,task_time,type
                        $upper_price=$this->get_stock_price('3', $skuinfo['info']);
                        $upper_total_money=$count*$upper_price;
                        if($type==4){
                            //升级成精英，推荐人是精英或天使
                            //天使就提醒他升级
                            if($recommender_role==1){
                                //若升级成精英,则成同级，结算同级收益,结算上级收益
                                $money_count=$classify['info']['count'];
                                $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($recommender,$my_uid,'3',$upper_total_money,'0','2',$pid);
                                if(!$add_task){
                                    $this->error('操作失败');
                                }
                                //若升级成领袖，结算上级收益，结算同级收益
                                $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($recommender,$my_uid,'4',$upper_total_money,'0','3',$pid);
                                if(!$add_task){
                                    $this->error('操作失败');
                                }
                            }
                            //精英就结算
                            if($recommender_role==2){
                                //结算同级库存
                                $ahead=(new Income())->ahead_income($my_uid,$classify['info']['count'],'6','4',$pid);
                                if($ahead['status']){
                                    //上级出货人需要付出的提成收益
                                    $upper_count=$ahead['count'];
                                }else{
                                    $upper_count=0;
                                }

                                //结算上级库存
                                $is_enough=(new Income())->is_enough($my_upper_id,$upper_total_money,$classify['info']['count'],$upper_price,$pid);
                                if($is_enough) {
                                //if($my_upper_wallet_stock>=$upper_total_money) {
                                    //上级的库存是足够的，则上级减库存，加提现
                                    if(!empty($my_upper_id)){
                                        $note           = '下级升级减库存积分';
//                                        $money          = $upper_total_money;
//                                        $type           = '0';
//                                        $status         = '1';
//                                        $wallet_project = $this->wallet_update($note, $money, $type, $status, $my_upper_id,config('datatree.after_become_elite_minus'));

                                        $mius_goods=(new Income())->mius_goods_stock($my_upper_id,$pid,$classify['info']['pid_type'],$upper_total_money,$classify['info']['count'],$upper_price,$note,config('datatree.after_become_elite_minus'));
                                        if (!$mius_goods) {
                                            $this->error('操作失败');
                                        }




                                        if (!$wallet_project) {
                                            $this->error('操作失败');
                                        }
                                        $note           = '下级升级加提现积分';
                                        $money          = $my_total_money-$upper_count;
                                        $type           = '1';
                                        $status         = '0';
                                        $wallet_project = $this->wallet_update($note, $money, $type, $status, $my_upper_id,config('datatree.after_become_elite_plus'));
                                        if (!$wallet_project) {

                                            $this->error('操作失败');
                                        }
                                    }
                                }else{

                                    $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($my_upper_id,$my_uid,'2',$my_total_money-$upper_count,$upper_total_money,'0',$pid);

                                    if(!$add_task){
                                        $this->error('操作失败');
                                    }
                                }

                            }

                        }else{
                            //升级成领袖，推荐人是精英或天使
                            //提醒精英和天使升级
                            //若升级成领袖，获得同级收益,同时吊起同级收益接口
                            $my_total_count=$classify['info']['count'];
                            $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($recommender,$my_uid,'5',$upper_total_money,'0','3',$pid);
                            if(!$add_task){
                                $this->error('操作失败');
                            }
                        }
                    }else{
                        //我升级到了领袖，且我的推荐人也是领袖
                        //结算
                        $ahead=(new Income())->ahead_income($my_uid,$classify['info']['count'],'6','4',$pid);
                    }
                }
            }else{
                //升级，天使-精英，精英-领袖，天使-领袖
                $or_upper=$my_upper;
                $upper_price=$this->get_stock_price('3', $skuinfo['info']);
                $upper_total_money=$count*$upper_price;

                //我的积分
                if($type==4){$note='升级到精英减库存积分';$datatree=config('datatree.become_elite_minus');$number=50;}
                if($type==5){$note='升级到领袖减库存积分';$datatree=config('datatree.become_leader_minus');$number=267;}
                $money=$my_total_money;$status='1';
                $wallet_project=$this->wallet_update($note,$money,'0',$status,$my_uid,$datatree);
                if(!$wallet_project){
                    Db::rollback();
                    $this->error('操作失败');
                }

                if($type==4){$note='升级到精英加库存';$datatree=config('datatree.become_elite_plus');}
                if($type==5){$note='升级到领袖加库存';$datatree=config('datatree.become_leader_plus');}
//                $money=$my_total_money;$status='0';
//                $wallet_project=$this->wallet_update($note,$money,'0',$status,$my_uid,$datatree);
                $wallet_project=(new Income())->add_goods_stock($my_uid,$classify['info']['pid_type'],$classify['info']['count']);

                if(!$wallet_project){
                    Db::rollback();
                    $this->error('操作失败');
                }
                //建立我的关系
                if($type==4) $after_role=2;
                if($type==5) $after_role=3;
                $add=(new User())->update_relation($my_uid,$after_role,$my_or_type);

                if(!$add){
                    Db::rollback();
                    $this->error('操作失败');
                }
                Db::commit();
                $me=(new UserMemberLogic())->getInfo(['uid'=>$my_uid]);
                $my_upper_id=$me['info']['goods_uid'];
                    if($my_upper_id==$or_upper['info']['uid']){
                        //如果我的上级并没有变化，则不用提醒上级升级。并减他的库存
                        //结算同级收益
                        $my_recommender=(new UserMemberLogic())->getInfo(['uid'=>$my_recommend]);
                        if($my_recommender['info']['group_id']<$type-2){
                            //若推荐人等级低，则推荐人开启收益任务
                            //上级无变化，说明推荐人只能拿同级收益
                            if($type==4){

                                $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($my_recommend,$my_uid,'3',$upper_total_money,0,'2',$pid);
                                if(!$add_task){
                                    $this->error('操作失败');
                                }
                                $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($my_recommend,$my_uid,'4',$upper_total_money,0,'2',$pid);
                                if(!$add_task){
                                    $this->error('操作失败');
                                }
                            }else{

                                $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($my_recommend,$my_uid,'5',$upper_total_money,'0','3',$pid);
                                if(!$add_task){
                                    $this->error('操作失败');
                                }
                            }
                        }else{
                            //若推荐人与我等级相同，或比我高，则进行同级收益和上下级收益
                            $ahead=(new Income())->ahead_income($my_uid,$classify['info']['count'],'6','4',$pid);
                            if($ahead['status']) {
                                $upper_count=$ahead['count'];
                            }else{
                                $upper_count=0;
                            }

                            if($my_upper_type!=1){
                                $is_enough=(new Income())->is_enough($my_upper_id,$upper_total_money,$classify['info']['count'],$upper_price,$pid,$classify['info']['pid_type']);

                                if($is_enough) {
                                    //上级的库存是足够的，则上级减库存，加提现
                                    if(!empty($my_upper_id)){
                                        $note           = '下级升级减库存积分';
                                        $money          = $upper_total_money;
//                                        $type           = '0';
//                                        $status         = '1';
//                                        $wallet_project = $this->wallet_update($note, $money, $type, $status, $my_upper_id,config('datatree.after_become_elite_minus'));
                                        $wallet_project = (new Income())->mius_goods_stock($my_upper_id,$pid,$classify['info']['pid_type'],$money,$classify['info']['count'],$upper_price,$note,config('datatree.after_become_elite_minus'));
                                        if (!$wallet_project) {
                                            $this->error('操作失败');
                                        }
                                        $note           = '下级升级加提现积分';
                                        $money          = $my_total_money-$upper_count;
                                        $type           = '1';
                                        $status         = '0';
                                        $wallet_project = $this->wallet_update($note, $money, $type, $status, $my_upper_id,config('datatree.after_become_elite_plus'));
                                        if (!$wallet_project) {
                                            $this->error('操作失败');
                                        }}
                                }else{
                                    //上级的库存是不足的
                                    //开启上级增加库存任务
                                    $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($my_upper_id,$my_uid,'2',$my_total_money-$upper_count,'846700','0',$pid);
                                    if(!$add_task){
                                        $this->error('操作失败');
                                    }
                                }
                            }
                        }


                    }elseif($type!=5){
                        //我的上级发生了变化,且我的等级不是领袖，则提醒前上级升级
                        //开启48小时任务

                        $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($or_upper['info']['uid'],$my_uid,'3',$upper_total_money,'0','2',$pid);
                        if(!$add_task){
                            $this->error('操作失败');
                        }
                        $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($or_upper['info']['uid'],$my_uid,'4',$upper_total_money,'0','3',$pid);
                        if(!$add_task){
                            $this->error('操作失败');
                        }
                    }else{
                        //我的上级发生了变化,且我的等级是领袖，提醒升级
                        //我的推荐人是我的上级，精英，或领袖，现在变成0
                        //我的推荐人不是我的上级，元领袖，现在是0
                        $my_recommender=(new UserMemberLogic())->getInfo(['uid'=>$my_recommend]);
                        if($my_recommender['info']['group_id']!=3){
                            //我的推荐人等级不足
                            $add_task=(new \app\src\task\logic\RoleTaskLogic())->add_task($my_recommend,$my_uid,'5',$upper_total_money,'0','3',$pid);
                            if(!$add_task){
                                $this->error('操作失败');
                            }
                        }else{
                            //我的上级是领袖，直接计算
                            $ahead=(new Income())->ahead_income($my_uid,$classify['info']['count'],'6','4',$pid);
                        }

                    }
                }

        }
        session('buy_se','');
        $this->success('操作成功','shop/product_list');
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

    /*
       * 是否有购买所有商品的次数及权限的判断
       * */
    public function buy_can($type,$buy_status='0') {
        //若是非手机注册用户，则什么也不能购买；
        $is_mobile_login = session('is_login');
        if ($is_mobile_login == 1) {
        } else {
            $this->error('您的手机号没有注册', 'user/mobile_register');
        }
        $role = $this->role;
        //若在48小时任务内，则任何商品不可购买

        $uid       = $this->memberinfo['id'];
        $role_task = (new RoletaskLogic())->getInfo(['from_uid' => $uid,'type'=>['in','1,3,4,5'],'status'=>'0']);
        if (!$role_task['status']) $this->error('查询出错');
        if (!empty($role_task['info'])) $this->error('您有升级定时任务，暂时不可购买');

        //当前身份与商品属性
        //商品类型1：不计入积分商品2：计入积分普通商品3：计入积分,天使级商品4：精英5：领袖
        if (empty($type)) {
            return true;
        }

        if ($type == 2) {//计入积分商品，所有身份可以购买
            if($buy_status==1){
                if($role>=1&&$role<=3){
                    return true;
                }else{
                    $this->error('当前身份不可存货');
                }
            }
        } elseif ($type == 3) {//天使商品，只有注册会员可以购买
            if ($role == 4) return true;
            $this->error('当前身份不可购买此商品，非注册会员');
        } elseif ($type == 4) {//精英商品，只有天使和注册会员可以购买
            if ($role == 4 || $role == 1) return true;
            $this->error('当前身份不可购买此商品，精英与领袖');
        } elseif ($type == 5) {//领袖商品，领袖不能购买
            if ($role == 3) $this->error('当前身份不可购买此商品，是天使');
            return true;
        }

    }





    /*
    * 天使订单生成
    *
    * */
    public function angel_order($uid,$ids,$numarr,$address_id,$note) {
        session('pid', null);
        session('count', null);

        //判断是否还有购买商品的权限

        //购买人的用户ID
//        $uid        = $this->memberinfo['id'];
//        $ids        = input('pid');
//        $numarr     = input('count');
//        $address_id = input('addressid'); //收货地址ID
//        $note       = input('note');
        //有购物车会出现多个物品一起提交

        $pay_type = 2; //支付类型 1:支付宝

        $qulity = (new ProductLogic())->getInfo(['id' => $ids]);
        if (!$qulity['status'] || empty($qulity['info'])) $this->error('读取不到商品信息');
        $type=$qulity['info']['type'];



        //1. 收货地址
        $entity = [];
        $entity = $this->need_address($address_id, $qulity);
        //联系人等信息



        //1. 本商城生成的支付编号
        $result = (new ProductSkuLogic())->getInfo(['product_id' => $ids]);
        if (!$result['status']) $this->error('读取不到商品信息');

        $price       = $this->get_real_price($qulity['info']['type'], $this->role, $result['info']);
        $total_price = $price * $numarr;

        $items = array();


        $name = $qulity['info']['name'];
        //$weight = $qulity['info']['weight'];
        $imgmap['pid']  = $result['info']['product_id'];
        $imgmap['type'] = '6015';
        $imgid          = (new ProductImageLogic())->getInfo($imgmap);

        $tmp = array(array(
            'p_id'              => $result['info']['product_id'],
            'name'              => $name,
            'ori_price'         => $result['info']['ori_price'],
            'price'             => $price,
            'count'             => $numarr,
            'sku_id'            => $result['info']['sku_id'],
            'psku_id'           => $result['info']['id'],
            'sku_desc'          => $result['info']['sku_desc'],
            'createtime'        => time(),
            'dt_origin_country' => $qulity['info']['dt_origin_country'],
            'weight' =>'',
            'taxrate'           => '0',
            'has_sku'           =>'0'
        ));

        if ($imgid['status']) {
            $img             = $imgid['info']['img_id'];
            $tmp['0']['icon_url'] = $img;
        }
        $items = $tmp;


        Db::startTrans();

        $flag = false;
        //为普通商品时

        $is_enough=$this->enough_money($total_price);
        if(!$is_enough) $this->error('积分不足');



        //增加积分日志和储值日志
        if($type==1){
            $note='购买非积分商品'.$note;
        }elseif($type==2){
            $note='购买积分商品'.$note;
        }elseif($type==3){
            $note='购买天使入门商品,angel处理'.$note;
        }elseif($type==4){
            $note='购买精英入门商品'.$note;
        }elseif($type==5){
            $note='购买领袖入门商品'.$note;
        }



        //积分储值减少
//        if($type!==3){
//            $this->wallet_update($note,$total_price,'1','1',$uid);
//        }



//        $addlogstoremoney = array(
//            'op_type'  => '98',
//            'number'   => -$total_price,
//            'time'     => time(),
//            'uid'      => $this->memberinfo['id'],
//            'note'     => $note,
//            'num_type' => '1'
//        );
//        $addlogmoneyres   = (new UserLogLogic())->add($addlogstoremoney);
//        if (!$addlogmoneyres['status']) {
//            $flag = true;
//        }





        $order = array(
            'uid'            => $this->memberinfo['id'],
            'order_code'     => getOrderid($uid),
            'price'          => $total_price,
            'post_price'     => '0',
            'note'           => $note,
            'pay_status'     => 1,
            'comment_status' => 0,
            'storeid'        => '0',
            'idcode'         => '0',
            'discount_money' => '0',
            'from'           => '1',
            'tax_amount'     => '0',
            'goods_amount'   => $total_price,
            'pay_type'       => 4,
            'pay_balance'    => '',
            'items'          => $items
        );
        $entity = array_merge($entity, $order);
        $result =(new OrdersLogic())->add_Order($entity);

        //扣除积分
//        $wallet=(new WalletLogic())->getInfo(['uid'=>$uid]);
//        $last_points=$wallet['info']['cash_points']-$total_price;
//        $wallet_enity=['cash_points'=>$last_points];
//        $miu=(new WalletLogic())->save(['uid'=>$uid],$wallet_enity);
//
//        $miu_log_info=[
//            'uid'=>$uid,
//            'before_points'=>$wallet['info']['cash_points'],
//            'points_type'=>'1',
//            'plus'=>'0',
//            'minus'=>$total_price,
//            'after_money'=>$last_points,
//            'create_time'=>time(),
//            'reason'=>$note,
//            'dtree_type'=>0,
//            'wallet_type'=>'0'
//        ];
//        $miu_log=(new WalletHisLogicV2())->add($miu_log_info);
//
//        if(!$miu['status']) $flag=false;
//        if(!$miu_log)$flag=false;


        //检查收益


        if ($flag) {
            Db::rollback();
            $this->error('对不起 订单生成出现问题');
        } else {
            Db::commit();
            return true;
        }

//        //若订单生成成功，那么收益分配，人物升级
//        if($flag){
//            $this->income($type,$total_price);
//        }else{
//            $this->error('出现错误');
//        }

    }





    public function buy_order($uid,$pid,$count,$money){
        //购买记录
        $me=(new UserMemberLogic())->getInfo(['uid'=>$uid]);
        if($me['status']&&!empty($me['info'])){
        $me=$me['info'];
        $entity=[
            'uid'=>$uid,
            'group_id'=>$me['group_id'],
            'father_uid'=>$me['father_uid'],
            'goods_uid'=>$me['goods_uid'],
            'pid'=>$pid,
            'count'=>$count,
            'status'=>'0',
            'create_time'=>time(),
            'money'=>$money
            ];

        $add=(new BuyOrderLogic())->add($entity,'id');
        return($add);
        }else{
         $this->error('存储购买，用户信息查询错误');
        }
    }




    public function clear(){

        $order=(new OrdersLogic())->queryNoPaging(['note'=>'补充订单']);
        foreach($order['info'] as $v){
            $createtime=1491443491+$v['id'];
            (new OrdersLogic())->save(['id'=>$v['id']],['createtime'=>$createtime]);
        }
    }



    /*
   * 存货方法
   *
   * */
    public function product_stock() {
        session('pid', null);
        session('count', null);
        if($this->memberinfo['id']!=196)$this->error('本功能暂不开放');
        //判断是否还有购买商品的权限
        //购买人的用户ID
        $uid        = $this->memberinfo['id'];
        $ids        = input('pid');
        $numarr     = input('count');
        $address_id = input('addressid'); //收货地址ID
        $note       = input('note');
        //有购物车会出现多个物品一起提交

        $pay_type = 2; //支付类型 1:支付宝

        $qulity = (new ProductLogic())->getInfo(['id' => $ids]);
        if (!$qulity['status'] || empty($qulity['info'])) $this->error('读取不到商品信息');
        $type=$qulity['info']['type'];

        //1. 收货地址
        $entity = [];
        $entity = $this->need_address($address_id, $qulity);
        //联系人等信息

        //1. 本商城生成的支付编号
        $result = (new ProductSkuLogic())->getInfo(['product_id' => $ids]);
        if (!$result['status']) $this->error('读取不到商品信息');

        $price       = $this->get_real_price($qulity['info']['type'], $this->role, $result['info']);
        $total_price = $price * $numarr;

        $items = array();


        $name = $qulity['info']['name'];
        //$weight = $qulity['info']['weight'];
        $imgmap['pid']  = $result['info']['product_id'];
        $imgmap['type'] = '6015';
        $imgid          = (new ProductImageLogic())->getInfo($imgmap);

        $tmp = array(array(
            'p_id'              => $result['info']['product_id'],
            'name'              => $name,
            'ori_price'         => $result['info']['ori_price'],
            'price'             => $price,
            'count'             => $numarr,
            'sku_id'            => $result['info']['sku_id'],
            'psku_id'           => $result['info']['id'],
            'sku_desc'          => $result['info']['sku_desc'],
            'createtime'        => time(),
            'dt_origin_country' => $qulity['info']['dt_origin_country'],
            'weight' =>'',
            'taxrate'           => '0',
            'has_sku'           =>'0'
        ));

        if ($imgid['status']) {
            $img             = $imgid['info']['img_id'];
            $tmp['0']['icon_url'] = $img;
        }
        $items = $tmp;


        Db::startTrans();

//        $order=$this->buy_order($uid,$ids,$numarr,$total_price);
//        if(!empty($order)){
//            session('order',$order['info']);
//        }
        session('order','-2');
        $flag = false;
        //为普通商品时

        $people_stock=(new WalletLogic())->getInfo(['uid'=>$uid]);
        $people_stock=$people_stock['info']['stock_points'];
        if($people_stock<$total_price) $this->error('积分不足');



        //增加积分日志和储值日志
        if($type==1){
            $note='购买非积分商品'.$note;
        }elseif($type==2){
            $note='提货积分商品'.$note;
        }elseif($type==3){
            $note='购买天使入门商品'.$note;
        }elseif($type==4){
            $note='购买精英入门商品'.$note;
        }elseif($type==5){
            $note='购买领袖入门商品'.$note;
        }

        $order = array(
            'uid'            => $this->memberinfo['id'],
            'order_code'     => getOrderid($uid),
            'price'          => $total_price,
            'post_price'     => '0',
            'note'           => $note,
            'pay_status'     => 1,
            'comment_status' => 0,
            'storeid'        => '0',
            'idcode'         => '0',
            'discount_money' => '0',
            'from'           => '1',
            'tax_amount'     => '0',
            'goods_amount'   => $total_price,
            'pay_type'       => 4,
            'pay_balance'    => '',
            'items'          => $items
        );
        $entity = array_merge($entity, $order);
        $result =(new OrdersLogic())->add_Order($entity);

        //扣除积分
        $note           = '提取货物减库存积分';
        $money          = $total_price;
        $type           = '0';
        $status         = '1';
        $wallet_project = $this->wallet_update($note, $money, $type, $status, $uid, '6234');

        //检查收益
        if ($flag) {
            Db::rollback();
            $this->error('对不起 订单生成出现问题');
        } else {
            //若订单生成成功，那么收益分配，人物升级
            Db::commit();
            $this->success('提货成功', url('shop/product_list'));
        }


    }


}



