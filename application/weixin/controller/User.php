<?php
/**
 * Created by PhpStorm.
 * User: boye
 * Date: 2017/3/1
 * Time: 9:23
 */

namespace app\weixin\controller;

use app\src\goodsstock\logic\GoodsStockLogic;
use app\src\order\logic\OrdersCommentLogic;
use app\src\order\logic\OrdersInfoViewLogic;
use app\src\order\logic\OrdersItemLogic;
use app\src\order\logic\OrdersLogic;
use app\src\task\logic\RoleTaskLogic;
use app\src\user\action\RelationAction;
use app\src\user\logic\UcenterMemberLogic;
use app\src\user\logic\UserGroupLogic;
use app\src\user\logic\MemberLogic;
use app\src\user\logic\UserMemberLogic;
use app\src\user\logic\MemberConfigLogic;

use app\src\wallet\logic\WalletApplyLogic;
use app\src\wallet\logic\WalletHisLogicV2;
use app\src\wallet\logic\WalletLogic;
use app\src\wallet\logic\WalletOrderLogicV2;
use app\src\wallet\logic\WithdrawAccountLogicV2;
use app\src\wxpay\logic\WxaccountLogic;
use app\pc\helper\PcFunctionHelper;
use app\src\product\logic\ProductimageLogic;
use app\src\shoppingCart\action\ShoppingCartQueryAction;
use app\src\product\logic\ProductLogic;
use app\src\user\logic\UserRelationLogic;
use app\weixin\Logic\WeixinLogic;
use think\Db;

class User extends Home
{

    public function iindex(){
        return $this->fetch('shop/coding');
    }

    /*
     * 二维码扫进来的入口
     */
    public function recommend(){

        $recommend_uid=input('uid');
        $recommender=(new UserMemberLogic())->getInfo(['uid'=>$recommend_uid]);

        if(!$recommender['status']||empty($recommender['info'])) $this->error('这个推荐人不存在');

        if($recommender['info']['group_id']==4) $this->error('这个推荐人没有推荐权限','index/index');
        //$recommend_sum=$recommender['info']['recommend_sum'];
        //$group_id=$recommender['info']['group_id'];
        //if($group_id==1&&$recommend_sum>200) $this->error('这个推荐人的推荐权限已用完');
        //if($group_id==2&&$recommend_sum>500) $this->error('这个推荐人的推荐权限已用完');
        //把推荐人信息预存进个人表

        if (!is_null($recommend_uid)) {
            $memberinfo=session('user_Info');
            $uid=$memberinfo['id'];
            if(empty($uid)) $this->error('读取不到您的个人信息，请重新扫描');

            $is_recommend = (new UserMemberLogic())->getInfo(['uid' => $uid]);
            if(!$is_recommend['status'] )$this->error('查询出错');
            if (!empty($is_recommend['info']['recommender'])) {
                $this->success('您已有推荐人，不可更改推荐人','index/index');
            } else {
                $user_member=(new UserMemberLogic())->getInfo(['uid'=>$recommend_uid]);
                if($user_member['info']['type']==1){
                    $recommend['platform']=$recommend_uid;
                }else{
                    $platform=(new UserMemberLogic())->queryNoPaging(['type'=>1]);
                    $array=array_column($platform['info'],'uid');
                    $array_platform=implode(',',$array);

                    $map_r['upper_uid']=['in',$array_platform];
                    $map_r['lower_uid']=$recommend_uid;
                    $recommend_relation=(new UserRelationLogic())->getInfo($map_r);
                    $recommend['platform']=$recommend_relation['info']['upper_uid'];
                }



                $recommend['uid']         = $uid;
                $recommend['recommender'] = $recommend_uid;
                $recommend['group_id'] = 4;
                $recommend['status']=0;
                $recommend['create_time']=time();
                $add_recommend            = (new UserMemberLogic())->add($recommend,'');
                if (!$add_recommend['status']) $this->error('用户添加信息出错');
            }
        }else{
            $this->error('读取不到推荐人信息，请确认二维码正确');
        }


        //增加用户的推荐数
        $or_recommend_sum=$recommender['info']['recommend_sum'];
        $add_recommend_sum=(new UserMemberLogic())->save(['uid'=>$recommend_uid],['recommend_sum'=>($or_recommend_sum+1)]);






        $this->success('预存推荐人成功了','Binding/changebinding');
    }



    /*
     * 用户个人中心
     */
    //用户个人信息，用户积分信息，用户地址信息
    public function index()
    {
        $memberinfo=$this->memberinfo;
        $this->assignTitle('我的中心');
        $this->assign('user_Info',$memberinfo);

        switch ($memberinfo['roles_info']['group_info']['group_id'])
        {
            case 1:
                $level = '健康天使';
                break;
            case 2:
                $level = '健康精英';
                break;
            case 3:
                $level = '健康领袖';
                break;
            case 4:
                $level = '注册会员';
                break;
            default:
                $level = '非注册会员';
        }
        $this->assign('level',$level);
        $uid=$memberinfo['uid'];
        $head=$memberinfo['head'];
        $this->assign('head',$head);

        $code='http://dehong.8raw.com/weixin.php/weixin/user/recommend/uid/'.$uid;
        $this->assign('code',$code);
        $uid=$memberinfo['id'];


        //底部
        $uid=$memberinfo['id'];
        $me=(new UserMemberLogic())->getInfo(['uid'=>$uid]);
        $my_upper_id=$me['info']['goods_uid'];
        if(!empty($my_upper_id)){
            $my_upper=(new UserMemberLogic())->getInfo(['uid'=>$my_upper_id]);
            if($my_upper['info']['type']!==1){
                $my_upper=(new MemberLogic())->getInfo(['uid'=>$uid]);
                $this->assign('Default','0');
            }else{
                $my_upper=(new MemberLogic())->getInfo(['uid'=>$uid]);
                $this->assign('Default','1');
            }
        }else{
            $my_upper=(new MemberLogic())->getInfo(['uid'=>$uid]);
            $this->assign('Default','1');
        }
        $my_info=(new MemberLogic())->getInfo(['uid'=>$uid]);
        if(empty($my_upper['info']['head'])){
            $my_upper['info']['head']=$my_info['info']['head'];
        }
        $this->assign('my_upper',$my_upper['info']);


        return $this->fetch('user/index');
    }

    public function account(){
        $info= (new WxaccountLogic())->getInfo(['id'=>1]);
        $this->wxapi=(new WeixinLogic($info['info']['appid'], $info['info']['appsecret']));
        $signPackage = $this->wxapi -> getSignPackage();
        $jsapilist = "'onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo'";
        $config = "wx.config({
			    debug: false,
			    appId: '" . $signPackage["appId"] . "',
			    timestamp: '" . $signPackage["timestamp"] . "',
			    nonceStr: '" . $signPackage["nonceStr"] . "',
			    signature: '" . $signPackage["signature"] . "',
			    jsApiList: [" . $jsapilist . "]
			});";
        $this->assign('config',$config);
        var_dump($config);exit;

        var_dump($info);exit;
    }



    public function two_code(){
        $memberinfo=$this->memberinfo;
        $this->assignTitle('我的中心');
        $this->assign('user_Info',$memberinfo);
        switch ($memberinfo['roles_info']['group_info']['group_id'])
        {
            case 1:
                $level = '健康天使';
                break;
            case 2:
                $level = '健康精英';
                break;
            case 3:
                $level = '健康领袖';
                break;
            case 4:
                $level = '注册会员';
                break;
            default:
                $level = '非注册会员';
        }
        $this->assign('level',$level);
        $uid=$memberinfo['uid'];
        $code='http://dehong.8raw.com/weixin.php/weixin/user/recommend/uid/'.$uid;
        $this->assign('code',$code);

        $info= (new WxaccountLogic())->getInfo(['id'=>1]);
        $this->wxapi=(new WeixinLogic($info['info']['appid'], $info['info']['appsecret']));
        $signPackage = $this->wxapi -> getSignPackage();
        $jsapilist = "'onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo'";
        $config = "wx.config({
			    debug: false,
			    appId: '" . $signPackage["appId"] . "',
			    timestamp: '" . $signPackage["timestamp"] . "',
			    nonceStr: '" . $signPackage["nonceStr"] . "',
			    signature: '" . $signPackage["signature"] . "',
			    jsApiList: [" . $jsapilist . "]
			});";
        $this->assign('config',$config);

        $spread_code='http://dehong.8raw.com/weixin.php/weixin/webview/spread_code/uid/'.$uid;
        $this->assign('spread_code',$spread_code);

        return $this->fetch();
    }


    public function sign_test() {
        $info= (new WxaccountLogic())->getInfo(['id'=>1]);
        $this->wxapi=(new WeixinLogic($info['info']['appid'], $info['info']['appsecret']));
        $signPackage = $this->wxapi -> getSignPackage();
        $jsapilist = "'onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo'";
        $config = "wx.config({
			    debug: false,
			    appId: '" . $signPackage["appId"] . "',
			    timestamp: '" . $signPackage["timestamp"] . "',
			    nonceStr: '" . $signPackage["nonceStr"] . "',
			    signature: '" . $signPackage["signature"] . "',
			    jsApiList: [" . $jsapilist . "]
			});";
        $this->assign('config',$config);

        var_dump($config);
    }


    //注册用户购买代理商品，进入代理关系网。
    /*
     * my_uid
     * type购买商品成为几级会员
     * 推荐人应该已经预存，判断一下
     */
    //public function can_become_agent($my_uid,$type){
    public function can_become_agent(){
        $my_uid=input('uid');$type=input('type');

        $me=(new UserMemberLogic())->getInfo(['uid'=>$my_uid]);
        if(!$me['info']||empty($me['status'])) $this->error('读取不到您的信息，请重新登录');
        $my_recommend=$me['info']['recommender'];
        if(empty($my_recommend)) $this->error('读取不到您的推荐人信息，请重新扫码');

        //判断是否为天使，若为天使查看推荐人是否有推荐天使权限
        if($type==1){
            $recommend=(new UserMemberLogic())->getInfo(['uid'=>$my_recommend]);
            if(!$recommend['info']||empty($recommend['status'])) $this->error('读取不到您推荐人的信息，请重新登录');
            if($recommend['info']['group_id']==4) $this->error('您的推荐人没有推荐权限');
            if($recommend['info']['group_id']==1){
                if($recommend['info']['recommend_sum']>=200) $this->error('您的推荐人没有推荐权限');
            }
            if($recommend['info']['group_id']==2){
                if($recommend['info']['recommend_sum']>=500) $this->error('您的推荐人没有推荐权限');
            }
        }

        $this->become_agent($me,$type);

        return($me);


    }
    /*
     * 进入关系的时候改变什么
     * 生成我的前级或上级
     * 不改变其他人的前级或上级
     */
    public function become_agent($me,$type){


        if($type>3||$type<1) $this->error('数据有误');
        if(!$me['info']||empty($me['status'])) $this->error('读取不到您的信息，请重新登录');
        $my_recommend=$me['info']['recommender'];

        if(is_null($my_recommend)) $this->error('读取不到您的推荐人信息，请重新扫码');
        $recommend=(new UserMemberLogic())->getInfo(['uid'=>$my_recommend]);

        if(!$recommend['info']||empty($recommend['status'])) $this->error('读取不到您推荐人的信息，请重新登录');

        $entity['group_id']=$type;



        if($recommend['info']['group_id']==$type){
            //我与推荐人同级，那么推荐人的上级就是我的上级
            $entity['goods_uid']=$recommend['info']['goods_uid'];
            $entity['father_uid']=$my_recommend;
        }elseif($recommend['info']['group_id']>$type){
            //我等级比推荐人低，那么推荐人是我的上级，我没有同级
            $entity['goods_uid']=$my_recommend;
            $entity['father_uid']=null;
        }elseif($recommend['info']['group_id']<$type){
            //如果我升级比我的推荐人高，那么我我一定到了精英或领袖
            $my_recommend_upper=(new UserMemberLogic())->getInfo(['uid'=>$recommend['info']['goods_uid']]);
            if($type==3){
                //如果我升级到领袖
                $entity['goods_uid']=null;
                if($my_recommend_upper['info']['group_id']==3){
                    $entity['father_uid']=$my_recommend_upper['info']['uid'];
                }else{
                    //如果我比推荐人上级等级高，那么我就去上级的上级为我的前级
                    $entity['father_uid']=$my_recommend_upper['info']['goods_uid'];
                }
            }else{
                //如果我没有升级到领袖，那么我一定升级成了精英

                if($my_recommend_upper['info']['group_id']==2){
                    //推荐人上级是精英，那么他就是我的前级，他的上级就是我的上级
                    $entity['father_uid']=$my_recommend_upper['info']['uid'];
                    $entity['goods_uid']=$my_recommend_upper['info']['goods_uid'];
                }
                if($my_recommend_upper['info']['group_id']==3){

                    //如果我推荐人上级是领袖，那么我没有前级，只有他是我的上级
                    $entity['father_uid']=null;
                    $entity['goods_uid']=$my_recommend_upper['info']['uid'];
                }


            }
        }
        $entity['status']=1;

        $save=(new UserMemberLogic())->save(['uid'=>$me['info']['uid']],$entity);

        if(!$save['status']) $this->error('建立您的关系出错');

        $recommend_uid=$me['info']['recommender'];
        $lower_uid=$me['info']['uid'];
        $build_relation=(new RelationAction())->become_agent($recommend_uid,$lower_uid,$type,$same_level_id='');
        if($build_relation) return true;

    }


    public function update_test(){
        $my_uid=input('uid');
        $my_type=input('type');
        $or_type=input('or_type');
        $this->update_relation($my_uid,$my_type,$or_type);
    }


    //我进行升级，处理后续反应
    /*
     * 前置要查看自己是否要进入48小时的任务中
         * 1，查询自己新上级--在前置中做掉
         * 2，查询自己新前级--在前置中做掉
         * 3，查询以自己为前级，并且元与自己有相同上级的人，变为他们的上级--本方法只做这一步
         * 4，查询自己的被推荐人，响应改变他们的关系--在前置中做掉
         */
    public function update_relation($my_uid,$my_type,$or_type){

        //查询自己的新上级和新前级
        $me=(new UserMemberLogic())->getInfo(['uid'=>$my_uid]);
        if(!$me['status']||empty($me['info'])) $this->error('信息查询出错，代码4403');
        $my_recommend_id=$me['info']['recommender'];
        //我的新上级，是我原有上级或者是我原有上级的上级，或者是系统
        //升级到我上级的同级，上级变前级
        //升级到我上级的下级，没有前级
        //上级到我上级的上级，上级的上级变前级，没有上级
        $or_upper_id=$me['info']['goods_uid'];
        $or_upper=(new UserMemberLogic())->getInfo(['uid'=>$or_upper_id]);
        if(!$or_upper['status']) $this->error('信息查询出错，代码4404');
        if($my_type==$or_upper['info']['group_id']){
            //升级到我上级的同级
            $entity=[];
            $entity['father_uid']=$or_upper_id;
            $entity['goods_uid']=$or_upper['info']['goods_uid'];
        }elseif($my_type<$or_upper['info']['group_id']){
            //升级为我元上级的下级，则上级不变，前级变空
            $entity=[];
            $entity['father_uid']=null;
            //$entity['goods_uid']=$or_upper['info']['goods_uid'];
        }elseif($my_type>$or_upper['info']['group_id']){
            //升级到我元上级的上级，则我为领袖，元上级为精英。我前级变为我精英上级的上级，我上级变为我前级的上级
            $entity=[];
            $entity['father_uid']=$or_upper['info']['goods_uid'];
            $or_upper_upper=(new UserMemberLogic())->getInfo(['uid'=>$or_upper['info']['goods_uid']]);
            $entity['goods_uid']=$or_upper_upper['info']['goods_uid'];
        }
        //上级与前级的关系理清

        //这里把我的级别从usermember中修改掉
        $entity['group_id']=$my_type;
        $entity['status']=1;
        $save=(new UserMemberLogic())->save(['uid'=>$my_uid],$entity);
        if(!$save['status'])$this->error('更新个人信息出错');
        //3，查询以自己为前,且元与自己同级，并且元与自己有相同上级的人，变为他们的上级
        $relation_update=(new RelationAction())->update($my_uid,$my_type,$or_type,$or_upper_id);
        if(!$relation_update) $this->error('更新用户的原有同级关系出错');

        //* 4，查询自己的被推荐人，响应改变他们的关系--在前置中做掉
        $recommended_update=$this->recommended_update($my_uid,$my_type);
        if(!$recommended_update) $this->error('更新您的下级出现错误');

        //*5，查询以自己为前，且后比自己同级，且现在有相同的上级，变为他们的前级
//        $recommended_update=$this->recommended_update($my_uid,$my_type);
//        if(!$recommended_update) $this->error('更新您的下级出现错误');
        return true;

    }


    public function recommended_update($my_uid,$my_type){
        $recommended_lists=(new UserMemberLogic())->queryNoPaging(['recommender'=>$my_uid]);
        if(!$recommended_lists['status']) $this->error('查询信息出错');
        if(!empty($recommended_lists['info'])){
            //若我有推荐的人
            foreach($recommended_lists['info'] as $k=>$v){
                //若我的等级小于被我推荐人的等级，则被我推荐的人的前级上级不变
                if($v['group_id']==$my_type){
                    //升级之后，我与被我推荐的人同级，则把被我推荐的人的前级改成我
                    $entity['father_uid']=$my_uid;
                    $update=(new UserMemberLogic())->save(['uid'=>$v['uid']],$entity);
                    if(!$update['status']) $this->error('错误代码111');
                }
                //若升级之后，被我推荐的人等级比我低，则已经在前面处理过了
            }
        }
        return true;
    }




    //进行手机注册，
    public function mobile_register(){
        echo 1;exit;
    }


    public function my_income(){
        $this->assignTitle('积分变动');
        $my_uid=$this->memberinfo['id'];
        $wallet_his=(new WalletHisLogicV2())->queryNoPaging(['uid'=>$my_uid]);

        $this->assign('info',$wallet_his);
        return $this->fetch();

    }



    public function my_next()
    {
        $this->assignTitle('健康团队');
        $memberinfo=$this->memberinfo;
        $uid=empty(input('uid'))?$memberinfo['id']:input('uid');
        $this->assign('user_Info',$memberinfo);

        $sign=0;
        if($uid==$memberinfo['id']) $sign=1;
        $this->assign('sign',$sign);

        $is_sign=input('sign');
        $is_me=0;
        if($uid==$memberinfo['id']) $is_me=1;
        $this->assign('is_me',$is_me);
        if($is_sign!=1&&$uid!=$memberinfo['id']) $this->error('您不能查看此人下级');


        //底部
        $me=(new UserMemberLogic())->getInfo(['uid'=>$memberinfo['id']]);
        $my_upper_id=$me['info']['goods_uid'];
        if(!empty($my_upper_id)){
            $my_upper=(new UserMemberLogic())->getInfo(['uid'=>$my_upper_id]);
            $this->assign('Default','0');
        }else{
            $my_upper=(new MemberLogic())->getInfo(['uid'=>$memberinfo['id']]);
            $this->assign('Default','1');
        }
        $my_info=(new MemberLogic())->getInfo(['uid'=>$memberinfo['id']]);
        $my_upper['info']['head']=$my_info['info']['head'];
        $this->assign('my_upper',$my_upper['info']);


        $my_lowers=(new UserMemberLogic())->queryNoPaging(['recommender'=>$uid]);
        if(!$my_lowers['status']) $this->error('查询出错');
        if(!empty($my_lowers['info'])) {
            foreach ($my_lowers['info'] as $k=>$v) {
                $tem[$k]=$v;
                $people=(new MemberLogic())->getInfo(['uid'=>$v['uid']]);
                $tem[$k]['name']=$people['info']['nickname'];
                $tem[$k]['head']=$people['info']['head'];
                $mobile=(new UcenterMemberLogic())->getInfo(['id'=>$v['uid']]);
                $tem[$k]['mobile']=is_numeric($mobile['info']['mobile'])?$mobile['info']['mobile']:'未注册电话';
            }
            $this->assign('lower',$tem);
        }else{
            $this->assign('lower','');
        }

        return $this->fetch();
    }


    public function address()
    {
        $this->assignTitle('我的收货地址');
        $memberinfo=$this->memberinfo;
        $this->assign('user_Info',$memberinfo);
        $address[0] = [
            'id'=>'1',
            'is_default'=>'1',
            'contactname'=>'yh',
            'mobile'=>'18969020145',
            'province'=>'浙江省',
            'city'=>'杭州市',
            'area'=>'江干区',
            'detailinfo'=>'智慧谷503'
        ];
        $this->assign('address',$address);

        $uid=$memberinfo['id'];
        $me=(new UserMemberLogic())->getInfo(['uid'=>$uid]);
        $my_upper_id=$me['info']['goods_uid'];
        if(!empty($my_upper_id)){
            $my_upper=(new UserMemberLogic())->getInfo(['uid'=>$my_upper_id]);
            $this->assign('my_upper',$my_upper['info']);
            $this->assign('Default','0');
        }else{
            $my_upper=['head'=>'https://mp.weixin.qq.com/misc/getheadimg?token=1152569593&fakeid=3242064522&r=262464','nickname'=>'德弘东方'];
            $this->assign('my_upper',$my_upper['info']);
            $this->assign('Default','1');
        }

        return $this->fetch();
    }
    public function address_add()
    {
        $this->assignTitle('添加地址');
        return $this->fetch();
    }
    public function address_edit()
    {
        $this->assignTitle('我的收货地址');
        $address = [
            'id' => '1',
            'is_default' => '1',
            'contactname' => 'yh',
            'mobile' => '18969020145',
            'province' => '浙江省',
            'city' => '杭州市',
            'area' => '江干区',
            'provinceid' => '11',
            'cityid' => '11',
            'areaid' => '11',
            'detailinfo' => '智慧谷503'
        ];
        $this->assign('address', $address);
        return $this->fetch();

    }
    public function commission_detail()
    {
        $this->assignTitle('提成明细');
        return $this->fetch();

    }
    public function my_score()
    {
        $this->assignTitle('我的积分');
        $memberinfo=$this->memberinfo;
        $this->assign('user_Info',$memberinfo);

        $goods=(new GoodsStockLogic())->queryNoPaging(['uid'=>$this->memberinfo['id']]);
        if(!empty($goods['info'])){
            foreach($goods['info'] as $k=>$v){
                $tem[$k]=$v;

                $product=(new ProductLogic())->getInfo(['id'=>$v['pid']]);

                $tem[$k]['product_name']=$product['info']['name'];
            }
            $this->assign('stock',$tem);
        }else{
            $this->assign('stock','');
        }


        return $this->fetch();
    }

    //提现积分转库存积分
    public function cash_to_stock(){
        $points=input('points');
        $points=$points*100;
        $uid=$this->memberinfo['id'];
        $wallet=(new WalletLogic())->getInfo(['uid'=>$uid]);
        if($wallet['info']['cash_points']<$points) $this->error('提现积分不足');

        Db::starttrans();
        $cash=(new Income())->wallet_update('提现积分转库存减提现积分',$points,'1','1',$uid,'-2');
        $stock=(new Income())->wallet_update('提现积分转库存加库存积分',$points,'0','0',$uid,'-2');
        if($cash&&$stock){
            Db::commit();
            $this->success('转换成功','user/index');
        }else{
            Db::backroll();
            $this->error('转换失败','user/wallet_in');
        }
    }

    //提现积分转库存积分
    public function cash_to_stock_direct($points,$url_type){
        $uid=$this->memberinfo['id'];
        $wallet=(new WalletLogic())->getInfo(['uid'=>$uid]);
        if($wallet['info']['cash_points']<$points) $this->error('提现积分不足');
        Db::starttrans();
        $cash=(new Income())->wallet_update('提现积分转库存减提现积分',$points,'1','1',$uid,'-2');
        $stock=(new Income())->wallet_update('提现积分转库存加库存积分',$points,'0','0',$uid,'-2');
        if($cash&&$stock){
            Db::commit();

            if($url_type==2)$this->success('转换成功','order/up_level');
            $this->success('转换成功','order/product_order');
        }else{
            Db::backroll();
            $this->error('转换失败','user/wallet_in');
        }
    }


    //充值
    public function recharge(){
        $money=input('points');
        if(empty($money)) $this->error('充值金额错误');
        $type=input('type');
        if(empty($type)) $this->error('充值方式错误');
        $uid=$this->memberinfo['id'];
        $log_list=[
            'uid'=>$uid,
            'order_code'=>time(),
            'money'=>$money*100,
            'note'=>'充值现金',
            'status'=>'1',
            'create_time'=>time(),
            'update_time'=>time(),
            'from'=>'1',
            'pay_type'=>0,
            'pay_status'=>0,
            'pay_money'=>'0',
            'pay_code'=>'',
            'trade_no'=>''
        ];
        $log=(new WalletOrderLogicV2())->add($log_list);
        if(empty($log))$this->error('申请出错');
        $entity['order_code']='WX'.$uid.'I'.$log;
        $log_order=(new WalletOrderLogicV2())->saveByID($log,$entity);

        if($type==1){
            $this->success('订单生成成功',url('pay/jsapi',array('order_code'=>$entity['order_code'])));
        }else{
            $this->success('订单生成成功',url('Alipaytest/pay',array('out_trade_no'=>$entity['order_code'])));
        }
    }




    //充值
    public function direct_recharge($points='0',$type='',$url_type=''){
        $money=$points/100;
        if(empty($money)) $this->error('充值金额错误');
        if(empty($type)) $this->error('充值方式错误');
        $this->redirect('user/select_recharge',array('money'=>$money,'url_type'=>$url_type));
    }

    public function select_recharge(){
        $this->assign('money',input('money'));
        $this->assign('url_type',input('url_type'));

        return $this->fetch();
    }

    public function recharge_product(){
        $type=input('type');
        $money=input('points');
        $url_type=input('url_type');

        if($type==3){
            $this->cash_to_stock_direct($money,$url_type);
        }else{
            $uid=$this->memberinfo['id'];
            $log_list=[
                'uid'=>$uid,
                'order_code'=>time(),
                'money'=>$money,
                'note'=>'充值现金',
                'status'=>'1',
                'create_time'=>time(),
                'update_time'=>time(),
                'from'=>'1',
                'pay_type'=>0,
                'pay_status'=>0,
                'pay_money'=>'0',
                'pay_code'=>'',
                'trade_no'=>'',
                'buy_type'=>$url_type
            ];
            $log=(new WalletOrderLogicV2())->add($log_list);
            if(empty($log))$this->error('申请出错');
            $entity['order_code']='WX'.$uid.'I'.$log;
            $log_order=(new WalletOrderLogicV2())->saveByID($log,$entity);
            if($type==1){
                $this->success('订单生成成功'.$entity['order_code'],url('pay/direct_buy',array('order_code'=>$entity['order_code'])));
            }else{
                $this->success('订单生成成功',url('Alipaytest/pay',array('out_trade_no'=>$entity['order_code'])));
            }
        }
    }


    public function wallet_in()
    {
        $this->assignTitle('充值');
        return $this->fetch();
    }

    /*
     * 升级任务
     */
    public function my_task(){
        $uid=$this->memberinfo['id'];
        $this->assignTitle('我的任务');
        $this->assign('people',$this->memberinfo);
        $task=(new RoleTaskLogic())->queryNoPaging(['uid'=>$uid,'status'=>'0']);
        if(!empty($task['info'])){
            $tem=[];
            foreach($task['info'] as $k=>$v){
                $tem[$k]=$v;
                $tem[$k]['create_time']=strtotime($v['create_time'])+172800;
            }
            $task['info']=$tem;
        }

        $this->assign('info',$task['info']);


        return $this->fetch();


    }

    /*
     * 提现主页
     */
    public function withdraw_list(){
        $this->assignTitle('提现申请');
        $this->assign('people',$this->memberinfo);
        $apply=(new WalletApplyLogic())->queryNoPaging(['uid'=>$this->memberinfo['id']]);

        $this->assign('apply',$apply['info']);

        return $this->fetch();
    }

    /*
     * 提现页面
     */
    public function withdraw_cash(){
        $this->assignTitle('提现');
        $this->assign('people',$this->memberinfo);
        return $this->fetch();
    }

    /*
     * 提现申请
     */
    public function withdraw_apply(){
        //发起提现申请
        //$partner_trade_no,$openid,$amount,$name,$desc

        //判断是否有提现权限
        $my_type=$this->memberinfo['roles_info']['group_info']['group_id'];
        if($my_type==4)$this->error('当前身份不可提现');

        $uid=$this->memberinfo['id'];
        $amount=input('amount','');
        //转化为分，将提现金额做验证
        if(empty($amount)) $this->error('提现金额错误-1','user/withdraw_cash');
        $amount=(int)$amount;

        if(!is_int($amount)) $this->error('提现金额错误-2','user/withdraw_cash');
        $amount=$amount*100;


        $real_name=input('real_name','');
        if(empty($real_name)) $this->error('错误，您没有输入提现人');


        $openid=$this->memberinfo['wxopenid'];
        //查看钱包金额是否足够
        $my_wallet=(new WalletLogic())->getInfo(['uid'=>$uid]);
        if(!$my_wallet['status']||empty($my_wallet['info'])) $this->error('错误：读取不到您的钱包信息');
        if($my_wallet['info']['cash_points']<$amount) $this->error('错误：您的提现库存不足');
        //提交提现申请

        $withdraw_entity=[
            'uid'=>$uid,
            'account'=>$openid,
            'account_type'=>6231,
            'create_time'=>time(),
            'valid_status'=>'0',
            'extra'=>''
        ];
        $add=(new WithdrawAccountLogicV2())->add($withdraw_entity,'id');


        $entity=[
            'create_time'=>time(),
            'reason'=>'',
            'valid_status'=>'0',
            'uid'=>$uid,
            'amount'=>$amount,
            'real_name'=>$real_name,
            'account_id'=>$add,
        ];
        $apply=(new WalletApplyLogic())->add($entity,'id');




        if($apply['status']) $this->success('申请成功，请等待审核','user/index');
        $this->error('申请失败，请重新申请');
    }


//
//    public function platform(){
//        $member=(new UserMemberLogic())->queryNoPaging(['type'=>0]);
//
//        foreach($member['info'] as $k=>$v){
//            $map['upper_uid']=['in','1,2,3,4,5,6,7,8,9,10,11'];
//            $map['lower_uid']=$v['uid'];
//            $relation=(new UserRelationLogic())->getInfo($map);
//
//            if(!in_array($v['recommender'],[1,2,3,4,5,6,7,8,9,10,11])){
//                if(!empty($relation['info'])){
//                    $entity['platform']=$relation['info']['upper_uid'];
//                }else{
//                    $map_r['upper_uid']=['in','1,2,3,4,5,6,7,8,9,10,11'];
//                    $map_r['lower_uid']=$v['recommender'];
//                    $recommend_relation=(new UserRelationLogic())->getInfo($map_r);
//                    $entity['platform']=$recommend_relation['info']['upper_uid'];
//                }
//            }else{
//                $entity['platform']=$v['recommender'];
//            }
//            $save=(new UserMemberLogic())->save(['uid'=>$v['uid']],$entity);
//        }
//    }





    public function my_order(){
        $uid=$this->memberinfo['id'];
        $this->assignTitle('我的订单');
        $map=['uid'=>$uid];


        $payStatus     = $this->_param('paystatus', '');
        $orderStatus   = $this->_param('orderstatus', '');
        $this->assign('pay_status',$payStatus);
        $this->assign('order_status',$orderStatus);
        if ($payStatus != '') {
            $map['pay_status']   = $payStatus;
            $params['paystatus'] = $payStatus;
        }
        if ($orderStatus != '') {
            $map['order_status']   = $orderStatus;
            $params['orderstatus'] = $orderStatus;
        }

        $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
        $order='createtime desc';
        $params['uid']=$uid;
        //$my_orders=(new OrdersInfoViewLogic())->queryWithPagingHtml($map, $page, $order,$params);
        $my_orders=(new OrdersInfoViewLogic())->queryNoPaging($map,$order);

        if(!empty($my_orders['info'])){
            $tem=[];
            foreach($my_orders['info'] as $k=>$v){
                $tem[$k]=$v;
                $order_info=(new OrdersItemLogic())->getInfo(['order_code'=>$v['order_code']]);
                $tem[$k]['items']=$order_info['info'];
            }
        }else{
            $tem=[];
        }



        if ($my_orders['status']) {
            $this->assign('order',$tem);
            return $this->fetch();
        }else{
            $this->error('查询出错');
        }

    }



    public function order_sure(){
        $id=input('id');
        $uid=$this->memberinfo['id'];
        if(empty($id)||empty($uid)) $this->error('缺少参数');

        $orders=(new OrdersLogic())->getInfo(['id'=>$id,'uid'=>$uid]);
        if($orders['status']){
            if($orders['info']['order_status']==4){
                $save=(new OrdersLogic())->save(['id'=>$id,'uid'=>$uid],['order_status'=>5]);
                if($save)$this->success('确认收货成功','user/my_order');
                $this->error('确认收货失败，请重试','user/my_order');
            }else{
                $this->error('订单状态无法确认收货','user/my_order');
            }
        }else{
            $this->error('查无此订单','user/my_order');
        }


    }






}