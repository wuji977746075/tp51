<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-08
 * Time: 18:52
 */

namespace app\domain;
use app\src\base\helper\ValidateHelper;
use app\src\order\action\OrderConfirmAction;
use app\src\order\action\OrderCreateAction;
use app\src\order\action\OrderDetailAction;
use app\src\order\action\OrderQueryAction;
use app\src\order\action\OrderReceiveGoodsAction;
use app\src\order\action\OrderShippedAction;
use app\src\order\enum\PayCurrency;
use app\src\order\helper\OrdersBusinessStatusHelper;
use app\src\order\logic\OrdersLogic;
use app\src\order\logic\OrdersPaycodeLogic;
use app\src\order\model\Orders;
use app\src\order\model\OrdersExpress;
use app\src\shoppingCart\logic\ShoppingCartLogic;


/**
 * 订单业务操作
 * Class OrderDomain
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\domain
 */
class OrderDomain extends BaseDomain
{

    /**
     * 订单确认操作
     * @author hebidu <email:346551990@qq.com>
     */
    public function confirm(){

        $this->checkVersion("101", "请增加s_id参数");
        $order_code = $this->_post("order_code","",lang("lack_parameter",['param'=>'order_code']));
        $uid        = $this->_post('uid',"",lang("uid_need"));

        //获取订单信息
        $logic = new OrdersLogic();
        $result = $logic->getInfo(['uid'=>$uid,'order_code'=>$order_code]);
        $this->exitWhenError($result);
        $orders = new Orders($result['info']);

        $result = (new OrderConfirmAction())->confirm($orders);

        $this->returnResult($result);
    }

    /**
     * 订单发货操作
     * @author hebidu <email:346551990@qq.com>
     */
    public function shipped(){
        $this->checkVersion("101", "请增加s_id参数");
        $order_code = $this->_post("order_code","",lang("lack_parameter",['param'=>'order_code']));
        $uid        = $this->_post('uid',"",lang("uid_need"));
        $express_no = $this->_post("express_no","",lang("lack_parameter",['param'=>'express_no']));
        $express_code = $this->_post("express_code","",lang("lack_parameter",['param'=>'express_code']));
        $express_name = $this->_post("express_name","",lang("lack_parameter",['param'=>'express_name']));

        //获取订单信息
        $logic = new OrdersLogic();
        $result = $logic->getInfo(['uid'=>$uid,'order_code'=>$order_code]);
        $this->exitWhenError($result);
        $orders = new Orders($result['info']);

        $ordersExpress = new OrdersExpress();
        $ordersExpress->setUid($uid);
        $ordersExpress->setOrderCode($order_code);
        $ordersExpress->setExpresscode($express_code);
        $ordersExpress->setExpressno($express_no);
        $ordersExpress->setExpressname($express_name);
        $ordersExpress->setNote('');

        $result = (new OrderShippedAction())->shipped($orders,$ordersExpress);

        $this->exitWhenError($result,true);
    }

    /**
     * 确认收货
     * @author hebidu <email:346551990@qq.com>
     */
    public function receiveGoods(){
        $this->checkVersion("101", "请增加s_id参数");
        $order_code = $this->_post("order_code","",lang("lack_parameter",['param'=>'order_code']));
        $uid        = $this->_post('uid',"",lang("uid_need"));

        //获取订单信息
        $logic = new OrdersLogic();
        $result = $logic->getInfo(['uid'=>$uid,'order_code'=>$order_code]);
        $this->exitWhenError($result);
        $orders = new Orders($result['info']);

        $result = (new OrderReceiveGoodsAction())->receiveGoods($orders);

        $this->exitWhenError($result,true);
    }

    private function getBillInfo($type=0,$title='',$code=''){
        $type = intval($type);
        if(in_array($type, [1,2]) && empty($title)){
            $this->apiReturnErr(Llack('bill_title'));
        }
        if($type==2 && empty($code)){
            $this->apiReturnErr(Llack('bill_code'));
        }
        return [
            'type' =>$type,
            'title'=>$title,
            'code' =>$code,
        ];
    }

    /**
     * 立即购买 - 废弃
     * 101 : req + s_id  登陆检查(入口出)
     * 102 : req + score 可使用积分抵扣
     * 添加发票信息 2017-08-30 14:16:27
     * @author hebidu <email:346551990@qq.com>
     */
    public function createNow(){
        $this->checkVersion(["101","102"],"可以使用积分抵扣");

        $uid        = $this->_post('uid/d',0,lang("uid_need"));
        $count      = $this->_post('count/s',"",lang("lack_parameter",["param"=>"count"]));
        $sku_pkid   = $this->_post('sku_pkid/s',"",lang("lack_parameter",["param"=>"sku_pkid"]));
        $address_id = $this->_post('address_id/d',0,lang('address_id_need'));
        $note       = $this->_post("note/s",'');
        $score      = $this->_post("score/d",0);
        $bill_type  = $this->_post("bill_type/d",0);
        $bill_title = $this->_post("bill_title/s",'');
        $bill_code  = $this->_post("bill_code/s",'');
        $bill_info  = $this->getBillInfo($bill_type,$bill_title,$bill_code);
        // check param
        $count_arr    = explode(",",$count);
        $sku_pkid_arr = explode(",",$sku_pkid);
        if(empty($count_arr) || empty($sku_pkid_arr) || count($sku_pkid_arr) != count($count_arr)){
            $this->apiReturnErr(lang("err_param"));
        }

        //批量立即购买
        $action = new OrderCreateAction();
        $r = $action->createNow($uid,$count_arr,$sku_pkid_arr,$address_id,$note,$score,$bill_info);
        $this->exitWhenError($r,true);
    }


    /**
     * 订单详情页
     * @author hebidu <email:346551990@qq.com>
     */
    public function detail(){

        $this->checkVersion("101", "请增加s_id参数");
        $uid        = $this->_post('uid',"",lang("uid_need"));
        $order_code = $this->_post("order_code","",lang("lack_parameter",['param'=>'order_code']));

        $result = (new OrderDetailAction())->detail($uid,$order_code);

        $this->exitWhenError($result,true);
    }

    /**
     *
     * 查询订单
     * @author hebidu <email:346551990@qq.com>
     */
    public function query(){
        $this->checkVersion("101", "请增加s_id参数");
        $uid          = $this->_post('uid',"",lang("uid_need"));
        $query_status = $this->_post('query_status',"");
        $keyword = $this->_post('keyword',"");

        //对查询状态进行分析
        //[0=>全部,1=>待付款,2=>待发货,3=>待收货,4=>已收货,5=>退款/售后,6=>待评价,7=>已完成]

        //TODO：临时处理成已完成
        // if($query_status == 4) $query_status == 7;
        // 下面处理 了
        $status = OrdersBusinessStatusHelper::analysisQueryStatus($query_status);

        $orders = new Orders();
        $orders->setOrderStatus($status['order_status']);
        $orders->setPayStatus($status['pay_status']);
        $orders->setUid($uid);

        $result = (new OrderQueryAction)->query($keyword,$orders,$this->getPageParams());

        if(ValidateHelper::legalArrayResult($result)){
            foreach ($result['info']['list'] as &$item){
                $item['query_status'] = OrdersBusinessStatusHelper::convertQueryStatus($item);
            }
        }
        $this->exitWhenError($result,true);
    }

    /**
     * 取消订单
     * @author hebidu <email:346551990@qq.com>
     */
    public function cancel(){
        $this->checkVersion("101", "请增加s_id参数");
        $order_code = $this->_post("order_code","",lang("lack_parameter",['param'=>'order_code']));
        $uid        = $this->_post('uid',"",lang("uid_need"));

        //获取订单信息
        $logic = new OrdersLogic();
        $result = $logic->getInfo(['uid'=>$uid,'order_code'=>$order_code]);
        $this->exitWhenError($result);
        $orders = new Orders($result['info']);

        $result = (new OrdersLogic())->cancel($orders);

        $this->exitWhenError($result,true);
    }

    /**
     * 创建订单
     * 添加发票信息 2017-08-30 14:06:08
     * @author hebidu <email:346551990@qq.com>
     */
    public function create(){
        $this->checkVersion("101", "请增加s_id参数");
        $ids        = $this->_post("ids/s",'',lang('id_need'));
        $uid        = $this->_post("uid/d",0,lang('uid_need'));
        $address_id = $this->_post('address_id/d',0,lang('address_id_need'));
        $note       = $this->_post("note/s",'');
        $score      = $this->_post("score/d",0);
        $bill_type  = $this->_post("bill_type/d",0);
        $bill_title = $this->_post("bill_title/s",'');
        $bill_code  = $this->_post("bill_code/s",'');
        $bill_info  = $this->getBillInfo($bill_type,$bill_title,$bill_code);
        // $del = $this->_post("del",0);
        $del = 1;
        $action = new OrderCreateAction();

        $result = $action->create($uid,$ids,$address_id,$note,$score,$bill_info);

        //调用成功后，删除购物车项
        if($result['status'] && $del == 1){
            $this->deleteShoppingCartItem($uid,$ids);
        }

        $this->exitWhenError($result,true);

    }

    /**
     * 删除购物车中的项目
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @param $ids
     */
    private function deleteShoppingCartItem($uid,$ids){
        $map = [
            'uid'=>$uid,
            'id'=>['in',$ids]
        ];

        (new ShoppingCartLogic())->delete($map);
    }

    /**
     * 订单重新支付
     * @author hebidu <email:346551990@qq.com>
     */
    public function repay(){

        $this->checkVersion("101", "请增加s_id参数");
        $uid = $this->_post('uid','',lang('uid_need'));
        $order_code = $this->_post('order_code','',lang('order_code_need'));

        $r = (new OrdersLogic)->getInfo(['uid'=>$uid,'order_code'=>$order_code]);
        if(!ValidateHelper::legalArrayResult($r)){
            $this->apiReturnErr(lang('err_repay_param'));
        }

        $payMoney = intval($r['info']['price']) - intval($r['info']['score_pay']);
        $r = (new OrdersPaycodeLogic())->getPayInfo($uid,[$order_code],$payMoney,PayCurrency::RMB);

        $this->exitWhenError($r,true);
    }

}