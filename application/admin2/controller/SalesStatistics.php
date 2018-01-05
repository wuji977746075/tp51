<?php
/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/3/2
 * Time: 下午1:59
 */

namespace app\admin\controller;


use app\src\admin\helper\DateHelper;
use app\src\base\helper\ExceptionHelper;
use app\src\goods\logic\ProductLogic;
use app\src\order\enum\PayType;
use app\src\statistics\logic\FinancialStatisticsLogic;
use app\src\user\logic\MemberLogic;
use app\src\order\logic\OrdersLogic;
use app\src\user\logic\VUserInfoLogic;
use app\src\wallet\logic\VUserWalletInfoLogic;
use app\src\wallet\logic\WalletHisLogicV2;
use app\src\wallet\logic\WalletLogic;
use app\src\wallet\model\WalletHis;
use app\web\controller\Wallet;
use think\exception\DbException;

use app\src\system\logic\ProvinceLogic;
use app\src\powersystem\logic\AuthGroupAccessLogic;
use app\src\city\logic\CityLogicV2;
use app\src\area\logic\AreaLogicV2;

class SalesStatistics extends Admin{

    private function  downExcel($title='',array $cell,array $data){
        $cols = count($cell);
        $table = '<table><tr> ';
        $table .= '<th colspan="'.$cols.'" align="center">'.$title.'</th></tr><tr>';
        foreach ($cell as $v) {
            $table .= "<th  align='right'>{$v}</th>";
        }
        $table .= "</tr>";
        foreach ($data as $v) {
            $table .= "<tr>";
            foreach ($v as $vv) {
                $table .= "<td align='right'>{$vv}</td>";
            }
            $table .="</tr>";
        }
        $table .= "</table>";
        //通过header头控制输出excel表格
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl;charset=utf-8"); //
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="'.$title.'.xls"');
        header("Content-Transfer-Encoding:binary");
        echo $table;exit;
    }

    public function index(){
        $down = intval($this->_param('down', 0));
        $type = intval($this->_param('type', 0));

        $start = $this->_param('start', date('Y-m-01 00:00')); // 开始日期
        $end   = $this->_param('end', date('Y-m-01 00:00',strtotime(date('Y-m-01').' +1 month')));   // 结束日期
        // $arr = DateHelper::getDataRange(3);
        $this->assign('start',$start);
        $this->assign('end',$end);
        $params = ['start' => $start, 'end' => $end];
        $start_time  = is_numeric($start) ? $start : strtotime($start);
        $end_time    = is_numeric($end) ? $end : strtotime($end);

        $introducer_uid = $this->_param('introducer_uid', '');
        $this->assign('introducer_uid',$introducer_uid);
        if($introducer_uid){
            $map = " introducer_uid=$introducer_uid";
            $params['introducer_uid'] = $introducer_uid;
            $nickname = (new MemberLogic)->getOneInfo($introducer_uid);
        }else{
            $map    = " introducer_uid>0 ";
            $nickname = '';
        }
        $this->assign('nickname',$nickname);
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
        $page = $down ? false : ['curpage'=>$this->_param('p',1),'size'=>10];
        $prov  = $this->_param('prov', '');
        $city  = $this->_param('city', '');
        $area  = $this->_param('area', '');
        $this->assign('prov',$prov);
        $this->assign('city',$city);
        $this->assign('area',$area);
        $params['prov'] = $prov;
        $params['city'] = $city;
        $params['area'] = $area;
        $loc_code = '';$loc = '';
        $prov_name = $prov ? (new ProvinceLogic)->isExistCode($prov,'province')['province'].'(省)' : '';
        $city_name = $city ? (new CityLogicV2)->isExistCode($city,'city')['city'].'(市)' : '';
        $area_name = $area ? (new AreaLogicV2)->isExistCode($area,'area')['area'].'(区县)' : '';
        $loc = $prov_name.$city_name.$area_name;
        if($area){            $loc_code = $area;
            $map .= " and `loc_code`=$loc_code ";
        }elseif($city){       $loc_code = substr($city, 0, 4);
            $map .= " and `loc_code` like '$loc_code%' ";
        }elseif($prov){       $loc_code = substr($prov, 0, 2);
            $map .= " and `loc_code` like '$loc_code%' ";
        }

        $extra = $this->_param('extra', '');
        $this->assign('extra',$extra);
        if($extra){
            $map .= " and `extra` like '%$extra%' ";
            $params['extra'] = $extra;
        }
        $order = 'create_time desc';

        if($down){
            $xlsData = [];
            if($type === 1){ // 销售代表统计报表
                if(!$introducer_uid) $this->error('需要销售代表','');
                $r = (new AuthGroupAccessLogic)->getInfo(['uid'=>$introducer_uid,'group_id'=>7]);
                !$r['status'] && $this->error($r['info']);
                $introducer_code = $r['info']['extra'];

                if(!$start_time || !$end_time) $this->error('需要时间区间','');
                $map = " introducer_uid=$introducer_uid and create_time>=$start_time and create_time<=$end_time ";

                $r = (new OrdersLogic)->statisticsOrder($map,$page,$order,$params);
                $list = $r['info']['list'];
                foreach ($list as &$val){
                    $val = $this->fixList($val);
                } unset($val);
                $xlsTitle = "销售代表 $introducer_code 销售统计报表 ".date('Ymd Hi',$start_time)."~".date('Ymd Hi',$end_time);
                $xlsCell = ['','用户ID','购买金额','购买书籍','支付','订单号','订单创建时间','报表开始时间','报表结束时间'];
                $uids = [];$oids = [];
                foreach ($list as $v) {
                    $uids[$v['uid']] = $v['uid'];
                    $oids[$v['order_code']] = $v['order_code'];
                    $xlsData[] = [
                        '',$v['uid'],$v['money'],$v['p_name'],$v['pay_type'],$v['order_code'],date('Y-m-d H:i:s',$v['create_time']),date('Y-m-d H:i:s',$start_time),date('Y-m-d H:i:s',$end_time)
                    ];
                }
                $sum = '0.00元';
                if(count($list)){
                    $sum = '=sum(C3:C'.(count($list)+2).')&"元"';
                }
                $xlsData[] = ['','','','','','','','',''];
                $xlsData[] = [
                  '总计',count($uids).'人',$sum,'','',count($oids).'单','','',''
                ];
            }elseif($type === 2){ // XXXX市XXXX区/县销售代表 销售统计报表
                if(!$city) $this->error('需要市','');
                if(!$area) $this->error('需要区/县','');

                if(!$start_time || !$end_time) $this->error('需要时间区间','');
                $map = " loc_code=$loc_code and create_time>=$start_time and create_time<=$end_time ";

                $r = (new OrdersLogic)->statisticsOrder($map,$page,$order,$params);
                $list = $r['info']['list'];
                foreach ($list as &$val){
                    $val = $this->fixList($val);
                } unset($val);
                // 按销售人员组装数据
                $uids = [];
                foreach ($list as $v) {
                    $temp_uid = $v['introducer_uid'];
                    if(!isset($uids[$temp_uid])){
                        $uids[$temp_uid]['extra'] = $v['extra'];
                        $uids[$temp_uid]['uid']   = $temp_uid;
                        $uids[$temp_uid]['order_items'] = [];
                    }
                    $uids[$temp_uid]['order_items'][] = $v;
                } unset($temp_uid);
                // 按用户统计数据
                $uids_all=[];
                foreach ($uids as &$v) {
                    $oids = [];$alipay =0;$wxpay=0;$all=0;$u_uids = [];
                    foreach ($v['order_items'] as $vv) {
                        $u_uids[$vv['uid']]   = $vv['uid'];
                        $uids_all[$vv['uid']] = $vv['uid'];

                        $oids[$vv['order_code']] = $vv['order_code'];
                        if($vv['pay_type'] == '支付宝'){
                            $alipay += $vv['money'];
                        }elseif($vv['pay_type'] == '微信'){
                            $wxpay += $vv['money'];
                        }
                        $all += $vv['money'];
                    }
                    $v['alipay'] = $alipay;
                    $v['wxpay']  = $wxpay;
                    $v['all']    = $all;
                    $v['oids']   = $oids;
                    $v['uids']   = $u_uids;
                } unset($v);
                // 组装xlsData
                $xlsTitle = $city_name.$area_name."销售代表 销售统计报表 ".date('Ymd Hi',$start_time)."~".date('Ymd Hi',$end_time);
                $xlsCell = ['','推荐码-ID','销售金额','支付宝','微信','购买人数','订单数量','报表开始时间','报表结束时间'];
                foreach ($uids as $v) {
                    $xlsData[] = [
                        '',$v['extra'].'-'.$v['uid'],$v['all'],$v['alipay'],$v['wxpay'],count($v['uids']),count($v['oids']),date('Y-m-d H:i:s',$start_time),date('Y-m-d H:i:s',$end_time)
                    ];
                }
                $temp = count($uids);
                $sum3 = $temp ? '=sum(C3:C'.($temp+2).')' : '0.00';
                $sum4 = $temp ? '=sum(D3:D'.($temp+2).')' : '0.00';
                $sum5 = $temp ? '=sum(E3:E'.($temp+2).')' : '0.00';
                $sum7 = $temp ? '=sum(G3:G'.($temp+2).')' : '0';
                $xlsData[] = ['','','','','','','','',''];
                $xlsData[] = [
                  '总计',$temp.'人',$sum3,$sum4,$sum5,count($uids_all),$sum7,'',''
                ];
            }elseif($type === 3){ // 杭州市 销售统计报表
                if(!$city) $this->error('需要市','');
                $loc_code = substr($city,0,4);
                if(!$start_time || !$end_time) $this->error('需要时间区间','');
                $map = " loc_code like '".$loc_code."%' and create_time>=$start_time and create_time<=$end_time ";

                $r = (new OrdersLogic)->statisticsOrder($map,$page,$order,$params);
                $list = $r['info']['list'];
                foreach ($list as &$val){
                    $val = $this->fixList($val);
                } unset($val);
                // 所有区名
                $r = (new AreaLogicV2)->queryNoPaging(['father'=>$loc_code.'00']);
                $areas = [] ;//getArrColumn($r,'area','areaID');
                // 订单按区分组
                foreach ($r as $v) {
                    $areas[$v['areaID']] = ['area_id'=>$v['areaID'],'area_name'=>$v['area'],'order_items'=>[]];
                }
                foreach ($list as $v) {
                    $areas[$v['loc_code']]['order_items'][] = $v;
                }
                // 数据统计
                foreach ($areas as &$v) {
                    $all = 0;$alipay = 0;$wxpay= 0 ;
                    $oids = []; // 订单数 order_code
                    $uids = []; // 推荐人数 introducer_uid
                    foreach ($v['order_items'] as $vv) {
                        $oids[$vv['order_code']] = $vv['order_code'];
                        $uids[$vv['introducer_uid']] = $vv['introducer_uid'];
                        if($vv['pay_type'] == '支付宝'){
                            $alipay += $vv['money'];
                        }elseif($vv['pay_type'] == '微信'){
                            $wxpay += $vv['money'];
                        }
                        $all += $vv['money'];
                    }
                    $v['alipay'] = $alipay;
                    $v['wxpay']  = $wxpay;
                    $v['all']    = $all;
                    $v['oids']   = $oids;
                    $v['uids']   = $uids;
                } unset($v);
                // 组装xlsData
                $xlsTitle = $city_name." 销售统计报表 ".date('Ymd Hi',$start_time)."~".date('Ymd Hi',$end_time);
                $xlsCell = ['','区县名称','销售金额','支付宝','微信','销售代表人数','订单数量','报表开始时间','报表结束时间'];
                foreach ($areas as $v) {
                    $xlsData[] = [
                        '',$v['area_name'],$v['all'],$v['alipay'],$v['wxpay'],count($v['uids']),count($v['oids']),date('Y-m-d H:i:s',$start_time),date('Y-m-d H:i:s',$end_time)
                    ];
                }
                $temp = count($areas);
                $sum3 = $temp ? '=sum(C3:C'.($temp+2).')' : '0.00';
                $sum4 = $temp ? '=sum(D3:D'.($temp+2).')' : '0.00';
                $sum5 = $temp ? '=sum(E3:E'.($temp+2).')' : '0.00';
                $sum6 = $temp ? '=sum(F3:F'.($temp+2).')' : '0';
                $sum7 = $temp ? '=sum(G3:G'.($temp+2).')' : '0';
                $xlsData[] = ['','','','','','','','',''];
                $xlsData[] = [
                  '总计','',$sum3,$sum4,$sum5,$sum6,$sum7,'',''
                ];
            }elseif($type === 4){ // 浙江省 销售统计报表
                if(!$prov) $this->error('需要省','');
                $loc_code = substr($prov,0,2);
                if(!$start_time || !$end_time) $this->error('需要时间区间','');
                $map = " loc_code like '".$loc_code."%' and create_time>=$start_time and create_time<=$end_time ";

                $r = (new OrdersLogic)->statisticsOrder($map,$page,$order,$params);
                $list = $r['info']['list'];
                foreach ($list as &$val){
                    $val = $this->fixList($val);
                } unset($val);
                // 所有市名
                $r = (new CityLogicV2)->queryNoPaging(['father'=>$loc_code.'0000']);
                $citys = [] ;//getArrColumn($r,'city','cityID');
                // 订单按区分组
                foreach ($r as $v) {
                    $citys[$v['cityID']] = ['city_id'=>$v['cityID'],'city_name'=>$v['city'],'order_items'=>[]];
                }
                foreach ($list as $v) {
                    $citys[substr($v['loc_code'],0,4).'00']['order_items'][] = $v;
                }
                // 数据统计
                foreach ($citys as &$v) {
                    $all = 0;$alipay = 0;$wxpay= 0 ;
                    $oids = []; // 订单数 order_code
                    $uids = []; // 推荐人数 introducer_uid
                    foreach ($v['order_items'] as $vv) {
                        $oids[$vv['order_code']] = $vv['order_code'];
                        $uids[$vv['introducer_uid']] = $vv['introducer_uid'];
                        if($vv['pay_type'] == '支付宝'){
                            $alipay += $vv['money'];
                        }elseif($vv['pay_type'] == '微信'){
                            $wxpay += $vv['money'];
                        }
                        $all += $vv['money'];
                    }
                    $v['alipay'] = $alipay;
                    $v['wxpay']  = $wxpay;
                    $v['all']    = $all;
                    $v['oids']   = $oids;
                    $v['uids']   = $uids;
                } unset($v);
                // 组装xlsData
                $xlsTitle = $prov_name." 销售统计报表 ".date('Ymd Hi',$start_time)."~".date('Ymd Hi',$end_time);
                $xlsCell = ['','市名称','销售金额','支付宝','微信','销售代表人数','订单数量','报表开始时间','报表结束时间'];
                foreach ($citys as $v) {
                    $xlsData[] = [
                        '',$v['city_name'],$v['all'],$v['alipay'],$v['wxpay'],count($v['uids']),count($v['oids']),date('Y-m-d H:i:s',$start_time),date('Y-m-d H:i:s',$end_time)
                    ];
                }
                $temp = count($citys);
                $sum3 = $temp ? '=sum(C3:C'.($temp+2).')' : '0.00';
                $sum4 = $temp ? '=sum(D3:D'.($temp+2).')' : '0.00';
                $sum5 = $temp ? '=sum(E3:E'.($temp+2).')' : '0.00';
                $sum6 = $temp ? '=sum(F3:F'.($temp+2).')' : '0';
                $sum7 = $temp ? '=sum(G3:G'.($temp+2).')' : '0';
                $xlsData[] = ['','','','','','','','',''];
                $xlsData[] = [
                  '总计','',$sum3,$sum4,$sum5,$sum6,$sum7,'',''
                ];
            }elseif($type === 5){ // 全国 销售统计报表
                if(!$start_time || !$end_time) $this->error('需要时间区间','');
                $map = ($loc_code ? " loc_code like '".$loc_code."%' and " : " ")." create_time>=$start_time and create_time<=$end_time ";
                $r = (new OrdersLogic)->statisticsOrder($map,$page,$order,$params);
                $list = $r['info']['list'];
                foreach ($list as &$val){
                    $val = $this->fixList($val);
                } unset($val);
                // 所有省名
                $r = (new ProvinceLogic)->queryNoPaging(['countryid'=>1]);
                if(!$r['status']) return $r;
                $provs = [] ; // getArrColumn($r,'province','provinceID');
                // 订单按区分组
                foreach ($r['info'] as $v) {
                    $provs[$v['provinceID']] = ['prov_id'=>$v['provinceID'],'prov_name'=>$v['province'],'order_items'=>[]];
                }
                foreach ($list as $v) {
                    $provs[substr($v['loc_code'],0,2).'0000']['order_items'][] = $v;
                }
                // 数据统计
                foreach ($provs as &$v) {
                    $all = 0;$alipay = 0;$wxpay= 0 ;
                    $oids = []; // 订单数 order_code
                    $uids = []; // 推荐人数 introducer_uid
                    foreach ($v['order_items'] as $vv) {
                        $oids[$vv['order_code']] = $vv['order_code'];
                        $uids[$vv['introducer_uid']] = $vv['introducer_uid'];
                        if($vv['pay_type'] == '支付宝'){
                            $alipay += $vv['money'];
                        }elseif($vv['pay_type'] == '微信'){
                            $wxpay += $vv['money'];
                        }
                        $all += $vv['money'];
                    }
                    $v['alipay'] = $alipay;
                    $v['wxpay']  = $wxpay;
                    $v['all']    = $all;
                    $v['oids']   = $oids;
                    $v['uids']   = $uids;
                } unset($v);
                // 组装xlsData
                $xlsTitle = "全国 销售统计报表 ".date('Ymd Hi',$start_time)."~".date('Ymd Hi',$end_time);
                $xlsCell = ['','省名称','销售金额','支付宝','微信','销售代表人数','订单数量','报表开始时间','报表结束时间'];
                foreach ($provs as $v) {
                    $xlsData[] = [
                        '',$v['prov_name'],$v['all'],$v['alipay'],$v['wxpay'],count($v['uids']),count($v['oids']),date('Y-m-d H:i:s',$start_time),date('Y-m-d H:i:s',$end_time)
                    ];
                }
                $temp = count($provs);
                $sum3 = $temp ? '=sum(C3:C'.($temp+2).')' : '0.00';
                $sum4 = $temp ? '=sum(D3:D'.($temp+2).')' : '0.00';
                $sum5 = $temp ? '=sum(E3:E'.($temp+2).')' : '0.00';
                $sum6 = $temp ? '=sum(F3:F'.($temp+2).')' : '0';
                $sum7 = $temp ? '=sum(G3:G'.($temp+2).')' : '0';
                $xlsData[] = ['','','','','','','','',''];
                $xlsData[] = [
                  '总计','',$sum3,$sum4,$sum5,$sum6,$sum7,'',''
                ];
            }elseif($type === 0){
                $r = (new OrdersLogic)->statisticsOrder($map,$page,$order,$params);
                if(!$r['status']) $this->error($r['info']);
                $list = $r['info']['list'];
                $show = $r['info']['show'];
                $sum  = $r['info']['sum'];

                $sum['money'] = intval($sum['money']) / 100;
                foreach ($list as &$val){
                    $val = $this->fixList($val);
                } unset($val);
                $xlsTitle = ($loc ? $loc.' ':'').($nickname ? $nickname.' ':'').'销售统计报表 '.date('Ymd Hi',$start_time).'~'.date('Ymd Hi',$end_time);
                $xlsCell = [
                    '','订单类型','订单号','支付方式','创建时间','订单金额','用户-手机-id','推荐人-id','推荐码'
                ];
                foreach ($list as $v) {
                    $xlsData[] = [
                        '',
                        $v['order_type'],
                        $v['order_code'],
                        $v['pay_type'],
                        date('Y-m-d H:i:s',$v['create_time']),
                        $v['money'],
                        ($v['user_info'] ? $v['user_info']['nickname'].'-'.$v['user_info']['mobile'] : ' - ').'-'.$v['uid'],
                        $v['introducer_uname'].'-'.$v['introducer_uid'],
                        $v['extra'],
                    ];
                }
                $sum = '0.00';
                if(count($list)){
                    $sum = '=sum(F3:F'.(count($list)+2).')';
                }
                $xlsData[] = [
                  '总计','','','','',$sum,'','',''
                ];
            }else{
                $this->error('暂未实现');
            }
            $this->downExcel($xlsTitle,$xlsCell,$xlsData);
        }else{
            // 查询全部省
            // $r = $this->getArea(1,'country',false);
            $r = (new ProvinceLogic)->queryNoPaging(['countryid'=>1]);
            $this->exitIfError($r);
            $this->assign('provs',$r['info']);
            $r = (new OrdersLogic)->statisticsOrder($map,$page,$order,$params);
            if(!$r['status']) $this->error($r['info']);
            $list = $r['info']['list'];
            $show = $r['info']['show'];
            $sum  = $r['info']['sum'];

            $sum['money'] = intval($sum['money']) / 100;
            foreach ($list as &$val){
                $val = $this->fixList($val);
            } unset($val);
            $this->assign('sum',$sum);
            $this->assign('list',$list);
            $this->assign('show',$show);
            //统计用户余额总额
            // $map = [
            //     'wallet_type' => 0
            // ];
            // $r = (new VUserWalletInfoLogic)->sum($map, 'balance');
            // $total_balance = $r['info'] / 100;
            // $this->assign('total_balance', $total_balance);
            return $this->boye_display();
        }
    }

    private function fixList($val){
        $val['money'] = number_format(intval($val['money']) / 100,2);
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
        if($r['status']){
            $info = $r['info'];
            $val['user_info'] = [
             'nickname' => $info ? $info['nickname'] : '',
             'mobile'   => $info ? $info['mobile'] : ''
            ];
        }
        return $val;
    }

    /*
     * 按商品统计销售
     */
    public function product(){
        $down = intval($this->_param('down', 0));

        $start = $this->_param('start', '');
        $end   = $this->_param('end', '');
        // $arr = DateHelper::getDataRange(3);
        $this->assign('start',$start);
        $this->assign('end',$end);
        $params = ['start' => $start, 'end' => $end];
        $start_time  = is_numeric($start) ? $start : strtotime($start);
        $end_time    = is_numeric($end) ? $end : strtotime($end);

        $introducer_uid = $this->_param('introducer_uid', '');
        $this->assign('introducer_uid',$introducer_uid);
        if($introducer_uid){
            $map = " introducer_uid=$introducer_uid";
            $params['introducer_uid'] = $introducer_uid;
            $nickname = (new MemberLogic)->getOneInfo($introducer_uid);
        }else{
            $map    = " introducer_uid>0 ";
            $nickname = '';
        }
        $this->assign('nickname',$nickname);
        if($start){
            if($end){
                $map .= " and create_time>=$start_time and create_time<=$end_time ";
            }else{
                $map .= " and create_time>=$start_time ";
            }
        }elseif($end){
            $map .= " and create_time<=$end_time";
        }

        $page = $down ? false : ['curpage'=>$this->_param('p',1),'size'=>10];
        $prov  = $this->_param('prov', '');
        $city  = $this->_param('city', '');
        $area  = $this->_param('area', '');
        $this->assign('prov',$prov);
        $this->assign('city',$city);
        $this->assign('area',$area);
        $params['prov'] = $prov;
        $params['city'] = $city;
        $params['area'] = $area;
        $loc_code = '';
        if($area){            $loc_code = $area;
            $map .= " and `loc_code`=$loc_code ";
        }elseif($city){       $loc_code = substr($city, 0, 4);
            $map .= " and `loc_code` like '$loc_code%' ";
        }elseif($prov){       $loc_code = substr($prov, 0, 2);
            $map .= " and `loc_code` like '$loc_code%' ";
        }
        // 查询全部省
        // $r = $this->getArea(1,'country',false);
        $r = (new ProvinceLogic)->queryNoPaging(['countryid'=>1]);
        $this->exitIfError($r);
        $this->assign('provs',$r['info']);

        $extra = $this->_param('extra', '');
        $this->assign('extra',$extra);
        if($extra){
            // $map['extra'] = ['like', "%".$extra."%"];
            $map .= " and `extra` like '%$extra%' ";
            $params['extra'] = $extra;
        }

        $product = $this->_param('product','');
        $this->assign('product',$product);
        if($product){
            $map .= " and `p_id` = $product";
            $params['product'] = $product;
        }

        $order = 'create_time desc';
        $r = (new OrdersLogic)->statisticsProductOrder($map,$page,$order,$params);
        $list = $r['info']['list'];
        $show = $r['info']['show'];
        $sum  = $r['info']['sum'];

        $sum['money'] = intval($sum['money']);
        foreach ($list as &$val){
            $val = $this->fixList($val);
        } unset($val);
        if(!$r['status']) $this->error($r['info']);
        $this->assign('sum',$sum);
        $this->assign('list',$list);
        $this->assign('show',$show);

        $product_list = (new ProductLogic())->queryNoPaging('status=1');

        $this->assign('product_list',$product_list['info']);

        if($down){
            $xlsTitle = ($loc_code ? '地区'.$loc_code.'-' : '').($nickname ? '昵称'.$nickname.'-' : '').'销售统计报表';
            $xlsTitle.= ($start_time ? ' '.date('Ymd Hi',$start_time) : ' ∞').'~'.($end_time ? date('Ymd Hi',$end_time) : '∞');
            $xlsCell = [
                '','订单类型','订单号','支付方式','创建时间','订单金额','用户-手机-id','推荐人-id','推荐码','商品名称','商品数量'
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
                    ($v['user_info'] ? $v['user_info']['nickname'].'-'.$v['user_info']['mobile'] : ' - ').'-'.$v['uid'],
                    $v['introducer_uname'].'-'.$v['introducer_uid'],
                    $v['extra'],
                    $v['item_name'],
                    $v['item_count']
                ];
            }
            $sum = '0.00';
            if(count($list)){
                $sum = '=sum(F3:F'.(count($list)+2).')';
            }
            $xlsData[] = [
                '总计','','','','',$sum,'','',''
            ];
            $this->downExcel($xlsTitle,$xlsCell,$xlsData);
        }else{
            //统计用户余额总额
            $map = [
                'wallet_type' => 0
            ];
            $r = (new VUserWalletInfoLogic)->sum($map, 'balance');
            $total_balance = $r['info'] / 100;
            $this->assign('total_balance', $total_balance);

            return $this->boye_display();
        }
    }

}