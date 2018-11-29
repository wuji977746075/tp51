<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-29 13:46:22
 * Description : [P2p模块 首页 ]
 * src :FinancialStatistics.php
 */

// namespace app\
// use

class P2p {



    private function  downExcel($title='',array $cell,array $data){
        $table = '<table><thead><tr> ';
        foreach ($cell as $v) {
            $table .= "<th class='name'>{$v}</th>";
        }
        $table .= "</tr></thead><tbody>";
        foreach ($data as $v) {
            $table .= "<tr>";
            foreach ($v as $vv) {
                $table .= "<td class='name'>{$vv}</td>";
            }
            $table .="</tr>";
        }
        $table .= "</tbody></table>";
        //通过header头控制输出excel表格
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename="'.$title.'.xls"');
        header("Content-Transfer-Encoding:binary");
        echo $table;exit;
    }

    public function index(){
        $down = intval($this->_param('down', 0));

        $start = $this->_param('start', '');
        $end   = $this->_param('end', '');
        // $arr = DateHelper::getDataRange(3);
        $this->assign('start',$start);
        $this->assign('end',$end);
        $map = ' 1=1 ';
        $params = ['start' => $start, 'end' => $end];
        $start_time = is_numeric($start) ? $start : strtotime($start);
        $end_time   = is_numeric($end) ? $end : strtotime($end);
        if($start){
            if($end){
                // $map['create_time'] = [['egt',$start_time],['elt',$end_time],'and'];
                $map .= " and create_time>=$start_time and create_time<=$end_time ";
            }else{
                // $map['create_time'] = ['egt',$start_time];
                $map .= " and create_time>=$start_time ";
            }
        }elseif($end){
            // $map['create_time'] = ['elt',$end_time];
            $map .= " and create_time<=$end_time";
        }

        $pay_type   = $this->_param('pay_type', '');
        $order_type = $this->_param('order_type','');
        $this->assign('pay_type', $pay_type);
        $this->assign('order_type', $order_type);
        if($pay_type){
            $map .= " and pay_type=$pay_type";
            $params['pay_type'] = $pay_type;
        }
        if($order_type){
            $map .= " and order_type='$order_type'";
            $params['order_type'] = $order_type;
        }

        $uid = $this->_param('uid', '');
        $this->assign('uid',$uid);
        $nickname = '';
        if($uid){
            $map .= " and uid=$uid";
            $params['uid'] = $uid;
            $nickname = (new MemberLogic)->getOneInfo($uid);
        }
        $this->assign('nickname',$nickname);

        $page  = $down ? false : ['curpage'=>$this->_param('p',1),'size'=>10];
        $order = 'create_time desc';
        $r = (new FinancialStatisticsLogic)->queryOrder($map,$page,$order,$params);
        !$r['status'] && $this->error($r['info'],'');
        $list = $r['info']['list'];
// ["order_type"] => string(12) "商城订单"
// ["order_code"] => string(21) "T1716607210998465824B"
// ["create_time"] => int(1497568869)
// ["uid"] => int(192)
// ["pay_type"] => int(3)
// ["id"] => int(299)
// ["money"] => int(3000)
// ["pay_code"] => string(22) "PA1716607210943905084B"

        $show = $r['info']['show'];
        $sum  = $r['info']['sum'];
        $sum['money'] = intval($sum['money']) / 100;
        foreach ($list as &$val){
            $val['money']  = intval($val['money']) / 100;
            switch ($val['pay_type']){
                case PayType::ALIPAY:
                    $val['pay_type'] = '支付宝';
                    break;
                case PayType::WXPAY:
                    $val['pay_type'] = '微信';
                    break;
                case PayType::WALLET:
                    $val['pay_type'] = '余额';
                    break;
                case PayType::UPACP:
                    $val['pay_type'] = '银联支付';
                    break;
                default:
                    $val['pay_type'] = '未知支付方式['.$val['pay_type'].']';
            }
            $r = (new VUserInfoLogic)->getInfo(['id'=>$val['uid']]);
            $info = $r['info'];
            !$r['status'] && $this->error($info,'');
            $val['user_info'] = $info ? [
                'nickname' => $info['nickname'],
                'mobile' => $info['mobile']
            ] : [];
        } unset($val);
        if($down){
            $xlsTitle = $nickname.'-'.$pay_type.'-'.$order_type.'-'.$start.'-'.$end.'-财务统计';
            $xlsCell = [
                '','订单类型','订单号','支付方式','创建时间','订单金额','用户','手机','uid'
            ];
            $xlsData = [];
            foreach ($list as $v) {
                $xlsData[] = [
                    '',
                    $v['order_type'],
                    $v['order_code'],
                    $v['pay_type'],
                    date('Y-m-d H:i:s',$v['create_time']),
                    $v['money'],
                    $v['user_info'] ? $v['user_info']['nickname'] : '',
                    $v['user_info'] ? $v['user_info']['mobile'] : '',
                    $v['uid']
                ];
            }
            $xlsData[] = [
              '总计','','','','','=sum(F2:F'.(count($list)+1).')','','',''
            ];
            $this->downExcel($xlsTitle,$xlsCell,$xlsData);
        }else{
            //统计用户余额总额
            $map = ['wallet_type' => 0];
            $r = (new VUserWalletInfoLogic)->sum($map, 'balance');
            $total_balance = $r['info'] / 100;
            $this->assign('total_balance', $total_balance);

            $this->assign('sum',$sum);
            $this->assign('list',$list);
            $this->assign('show',$show);
            return $this->boye_display();
        }
    }

    /**
     * 用户钱包变动历史
     */
    public function walletHis()
    {
        $uid = $this->_param('id');
        $p = $this->_param('p', 1);
        if(empty($uid)){
            $this->error('用户id缺失');
        }

        $arr = DateHelper::getDataRange(3);
        $startdatetime = is_numeric($arr[0]) ? $arr[0] : strtotime($arr[0]);
        $enddatetime   = is_numeric($arr[1]) ? $arr[1] : strtotime($arr[1]);

        $params = ['uid' => $uid, 'startdatetime' => $startdatetime, 'enddatetime' => $enddatetime];

        //查询钱包历史
        $map = [
            'uid' => $uid,
            'create_time' => [['>=', $startdatetime], ['<', $enddatetime]]
        ];
        $page = ['curpage' => $p, 'size' => 10];
        $order = 'create_time desc';

        try{
            $result = (new WalletHisLogicV2)->queryWithPagingHtml($map, $page, $order, $params);
            $list = $result['list'];

            foreach ($list as &$val){
                switch ($val['dtree_type']){
                    case WalletHis::WALLET_HIS_BALANCE:
                        $val['from'] = '余额';
                        break;
                    case WalletHis::WALLET_HIS_WEIXIN_MP:
                        $val['from'] = '微信公众号';
                        break;
                    case WalletHis::WALLET_HIS_ALIPAY:
                        $val['from'] = '支付宝';
                        break;
                    case WalletHis::WALLET_HIS_WEIXIN_WORKER:
                        $val['from'] = '微信技工端';
                        break;
                    case WalletHis::WALLET_HIS_ADMIN:
                        $val['from'] = '后台';
                        break;
                    case WalletHis::WALLET_HIS_WEIXIN_DRIVER:
                        $val['from'] = '微信司机端';
                        break;
                    default:
                        $val['from'] = '=未知来源=';
                }
            }
            $show = $result['show'];
            $this->assign('list', $list);
            $this->assign('show', $show);
            $this->assign('uid', $uid);
            $this->assign('startdatetime',$startdatetime);
            $this->assign('enddatetime',$enddatetime);
            return $this->boye_display();

        }catch (DbException $e){
            $this->error(ExceptionHelper::getErrorString($e));
        }


    }

    /**
     * 用户钱包列表
     */
    public function walletList()
    {
        $mobile = $this->_param('mobile');
        $order_type = $this->_param('order_type',0);

        $map = [
            'wallet_type' => 0,
        ];

        $params = [];
        $uid = $this->_param('uid', '');
        $this->assign('uid',$uid);
        $this->assign('mobile', $mobile);
        $this->assign('order_type', $order_type);
        $nickname = '';
        if($uid){
            $map['uid'] = $uid;
            $params['uid'] = $uid;
            $nickname = (new MemberLogic)->getOneInfo($uid);
        }
        $order = false;
        if(!empty($mobile)){
            $map['mobile'] = ['like', "%$mobile%"];
            $params['mobile'] = $mobile;
        }
        if(in_array($order_type, [1,2])){
            if($order_type == 1){
                $order = 'balance desc';
            }
            if($order_type == 2){
                $order = 'balance asc';
            }
            $params['order_type'] = $order_type;
        }
        $this->assign('nickname',$nickname);

        $p = $this->_param('p', 1);

        $page = ['curpage' => $p, 'size' => 10];
        $fields = 'uid, balance, mobile, nickname';
        $result = (new VUserWalletInfoLogic)->queryWithPagingHtml($map, $page, $order, $params, $fields);

        if(!$result['status']) $this->error($result['info']);

        $list = [];
        $show = $result['info']['show'];
        foreach ($result['info']['list'] as $val){
            $val['balance'] /= 100;
            $list[] = $val;
        }

        //统计用户余额总额
        $map = [
            'wallet_type' => 0
        ];
        $result = (new VUserWalletInfoLogic)->sum($map, 'balance');
        $total_balance = $result['info'] / 100;

        $this->assign('list', $list);
        $this->assign('show', $show);
        $this->assign('total_balance', $total_balance);

        return $this->boye_display();
    }

    /**
     * 钱包操作
     */
    public function walletOper()
    {
        $uid = $this->_param('id');
        if(empty($uid)) $this->error('用户id缺失');

        $oper = $this->_param('oper');

        if(!is_numeric($oper) || $oper<0){
            $this->error('余额必须为不小于0的数字');
        }
        $oper *= 100;

        $logic = new VUserWalletInfoLogic();
        $map = [
            'uid' => $uid,
            'wallet_type' => 0
        ];
        $result = $logic->getInfo($map);
        if(!$result['status']) $this->error('操作失败','');

        if(empty($result['info'])) $this->error('该用户不存在');

        $frozen_funds = $result['info']['frozen_funds'];

        //修改后余额
        $account_balance = $frozen_funds + $oper;

        if($oper == $result['info']['account_balance'] - $result['info']['frozen_funds']){
            $this->success('没有改变','');
        }

        //记录钱包日志
        $result = (new WalletLogic)->addHisByAfterMoney($uid, $oper, WalletHis::WALLET_HIS_ADMIN, '后台修改余额');

        if(!$result['status']) $this->error('操作失败','');

        $result = (new WalletLogic)->save(['uid' => $uid], ['account_balance' => $account_balance]);

        if(!$result['status']) $this->error('操作失败','');


        $this->success('操作成功', '');

        $this->display();
    }
}