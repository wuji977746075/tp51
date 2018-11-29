<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/11/11
 * Time: 14:50
 */

namespace app\admin\controller;

use app\src\freight\logic\FreightTemplateLogic;
use app\src\system\logic\CityLogic;
use app\src\system\logic\DatatreeLogicV2;
use app\src\system\logic\ProvinceLogic;

class FreightTemplate extends Admin{

    public function index(){

        //获取运费模版

        $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));

//        $result = apiCall(FreightTemplateApi::QUERY_WITH_UID,array(UID,$page));
        $result = (new FreightTemplateLogic())->queryWidthUID(UID,$page);

        if($result['status']){

            foreach($result['info']['list'] as &$val){

//                $data = apiCall(DatatreeApi::GET_INFO,array(array('id'=>$val['company'])));
                $data = (new DatatreeLogicV2())->getInfo(array('id'=>$val['company']));

                if($data['status']){

                    if(is_null($data['info'])){
                        $val['freight_type'] = '未知';
                    }else{
                        $val['freight_type'] = $data['info']['name'];
                    }
                }else{
                    $this->error($data['info']);
                }

            }

            $this->assign('show',$result['info']['show']);
            $this->assign('list',$result['info']['list']);
        }

        return $this->boye_display();
    }

    public function edit(){

        if(IS_AJAX){

            $freightTemplate = $this->_post('freightTemplate','','模版设置缺失');


            if(!empty($freightTemplate)){

                $freightTemplate = json_decode(htmlspecialchars_decode($freightTemplate),true);

            }


            //检查运费模版(name,type,company,uid),name不能与现有模版重复
            $key = ['name','company','template_id'];
            foreach($key as $val){
                if(!isset($freightTemplate[$val])){
                    //不是有效的运费模版
                    $this->error('不是有效的运费模版');
                }
            }

            if(empty(trim($freightTemplate['name']))){
                $this->error('运费模版名称不能为空');
            }


            $templates = $freightTemplate['templates'];


            $data = array();
            $data['name'] = $freightTemplate['name'];
            $data['company'] = $freightTemplate['company'];
            $data['uid'] = UID;
            $data['freightAddress'] = array();

            foreach($templates as $val){

                $addressids = $this->checkTemplate($val);

                $arr = array();
                $arr['firstpiece'] = intval($val['firstpiece']);
                $arr['firstmoney'] = intval($val['firstmoney']);
                $arr['replenishpiece'] = intval($val['replenishpiece']);
                $arr['replenishmoney'] = intval($val['replenishmoney']);

                if($addressids!==false){

                    if(empty($addressids)){
                        $arr['addressids'] = 0;
                        $arr['addresses'] = "全国 [默认运费]";
                        $data['type'] = intval($val['type']);
                    }else{
                        $arr['addressids'] = $addressids;
                        $arr['addresses'] = $val['addresses'];
                    }

                    array_push($data['freightAddress'],$arr);

                }

            }

            //如果运费模版ID为0就是添加运费模版
            if($freightTemplate['template_id'] == 0){


                $map = array(
                    'name' =>$freightTemplate['name'],
                    'uid' =>UID

                );
//                $result = apiCall(FreightTemplateApi::GET_INFO,array(array($map)));
                $result = (new FreightTemplateLogic())->getInfo($map);
                if($result['status']){
                    if(is_array($result['info'])){

                        $this->error('该运费模版名称已存在');
                    }

                }else{
                    $this->error($result['info']);
                }

               //添加保存运费模版
//                $result = apiCall(FreightTemplateApi::ADD_TEMPLATE,array($data));、
                $result = (new FreightTemplateLogic())->addTemplate($data);

                if($result['status']){
                    $this->success('操作成功');
                }
            }
            //如果运费模版ID大于0就是修改运费模版
            elseif($freightTemplate['template_id'] > 0){

                //编辑运费模版
                $map = array();
                $map['id'] = $freightTemplate['template_id'];
//                $result = apiCall(FreightTemplateApi::UPDATE_TEMPLATE,array($map,$data));
                $result = (new FreightTemplateLogic())->updateTemplate($map,$data);
                if($result['status']){

                    $this->success('操作成功');
                }else{
                    $this->error($result['info']);
                }
            }
        }else{
            $template_id = $this->_param('id',0);
            if($template_id !=0){
                //查询是否有此模版
                $map = array();
                $map['uid'] = UID;
                $map['id'] = $template_id;
//                $result = apiCall(FreightTemplateApi::FIND_TEMPLATE,array($map));
                $result = (new FreightTemplateLogic())->findTemplate($map);
                if($result['status'] && is_array($result['info'])){
                    //有对应ID模版,生成适合页面编辑的json数据
                    $template = $result['info'];
                    $template = $this->bulidTemplatePageData($template);

                    $this->assign('Template_data',json_encode($template));

                }

            }

            //获取配送方式
//            $result = apiCall(DatatreeApi::GET_INFO,array(array('name'=>'配送方式')));
            $result = (new DatatreeLogicV2())->getInfo(array('name'=>'配送方式'));

            $pid = 0;
            if(!empty($result)){
                $pid = $result['id'];
            }

//            $result = apiCall(DatatreeApi::QUERY_NO_PAGING,array(array('parentid'=>$pid)));
            $result = (new DatatreeLogicV2())->queryNoPaging(array('parentid'=>$pid));


            if(!empty($result)){

                $this->assign('freight_type',$result);
            }


            //获取省市json

//            $result = apiCall(ProvinceApi::QUERY_NO_PAGING);
            $result = (new ProvinceLogic())->queryNoPaging();

            $address = array();

            if($result['status']){

                if(is_array($result['info'])){

                    $province = $result['info'];

//                    $result = apiCall(CityApi::QUERY_NO_PAGING);
                    $result = (new CityLogic())->queryNoPaging();


                    if($result['status']){

                        $city = $result['info'];
//                        dump($city);
//                        dump($province);
                        foreach($province as $val_pro){

                            $nodes = array();

                            foreach($city as $val_city){

                                if($val_city['father'] == $val_pro['provinceID']){

                                    if($val_city['city'] == "市辖区" || $val_city['city'] == "县"){
                                        continue;
                                    }

                                    array_push($nodes,array('text'=>$val_city['city']));

                                }

                            }

                            if(empty($nodes)){
                                array_push($nodes,array('text'=>$val_pro['province']));
                            }

                            array_push($address,array('text'=>$val_pro['province'],'selectable'=>false,'nodes'=>$nodes));

                        }
                    }
                }
                $this->assign('template_id',$template_id);
                $this->assign('address_json',json_encode($address));
            }
            return $this->boye_display();
        }

    }

    private function checkTemplate($template){

        $key = ['addresses','type','firstpiece','firstmoney','replenishpiece','replenishmoney'];

        foreach($key as $val){

            if(!isset($template[$val])){
                return false;
            }
        }

        if(empty($template['addresses'])){
            return 0;//全国默认运费地址集为0
        }

        $addresses = $template['addresses'];

        $addresses_arr = explode(',',$addresses);

        $addressids = array();

        foreach($addresses_arr as $val){

            //查询地址ID,先查询省表,不存在查询市表
            $result = apiCall(ProvinceApi::GET_INFO,array(array('province'=>$val)));
            if($result['status'] && is_array($result['info'])){
                array_push($addressids,$result['info']['provinceid']);
                continue;
            }
            $result = apiCall(CityApi::GET_INFO,array(array('city'=>$val)));
            if($result['status'] && is_array($result['info'])){
                array_push($addressids,$result['info']['cityid']);
                continue;
            }

        }

        $addressids = implode(',',$addressids);

        return $addressids;

    }

    /**
     * 生成适合编辑模版页面的json数据
     */
    private function bulidTemplatePageData($template){

        /**数据中取出结果
         * array(6) {
        ["id"] => string(2) "26"
        ["name"] => string(19) "我的运费模版1"
        ["type"] => string(1) "1"
        ["company"] => string(4) "6018"
        ["uid"] => string(1) "1"
        ["freightAddress"] => array(4) {
        [0] => array(8) {
        ["id"] => string(2) "56"
        ["addressids"] => string(1) "0"
        ["addresses"] => string(21) "全国 [默认运费]"
        ["firstpiece"] => string(1) "1"
        ["firstmoney"] => string(1) "2"
        ["replenishpiece"] => string(1) "3"
        ["replenishmoney"] => string(1) "4"
        ["template_id"] => string(2) "26"
        }
        [1] => array(8) {
        ["id"] => string(2) "57"
        ["addressids"] => string(13) "130300,130100"
        ["addresses"] => string(25) "秦皇岛市,石家庄市"
        ["firstpiece"] => string(1) "4"
        ["firstmoney"] => string(1) "3"
        ["replenishpiece"] => string(1) "2"
        ["replenishmoney"] => string(1) "1"
        ["template_id"] => string(2) "26"
        }
        [2] => array(8) {
        ["id"] => string(2) "58"
        ["addressids"] => string(6) "120000"
        ["addresses"] => string(9) "天津市"
        ["firstpiece"] => string(1) "2"
        ["firstmoney"] => string(1) "2"
        ["replenishpiece"] => string(1) "2"
        ["replenishmoney"] => string(1) "2"
        ["template_id"] => string(2) "26"
        }
        [3] => array(8) {
        ["id"] => string(2) "59"
        ["addressids"] => string(6) "510100"
        ["addresses"] => string(9) "成都市"
        ["firstpiece"] => string(1) "3"
        ["firstmoney"] => string(1) "3"
        ["replenishpiece"] => string(1) "3"
        ["replenishmoney"] => string(1) "3"
        ["template_id"] => string(2) "26"
        }
        }
        }
         */


        /**json
         * {
         * "name":"我的运费模版1",
         * "company_name":"顺丰快递",
         * "company":"6018","template_id":0,
         * "templates":[
         * {"addresses":"","type":"1","firstpiece":"1","firstmoney":"2","replenishpiece":"3","replenishmoney":"4"},
         * {"addresses":"秦皇岛市,石家庄市","type":"1","firstpiece":"4","firstmoney":"3","replenishpiece":"2","replenishmoney":"1"},
         * {"addresses":"天津市","type":"1","firstpiece":"2","firstmoney":"2","replenishpiece":"2","replenishmoney":"2"},
         * {"addresses":"成都市","type":"1","firstpiece":"3","firstmoney":"3","replenishpiece":"3","replenishmoney":"3"}
         * ]} ;**/

        //查询配送方式(快递)名称

//        $result = apiCall(DatatreeApi::GET_INFO,array(array('id'=>$template['company'])));
        $result = (new DatatreeLogicV2())->getInfo(array('id'=>$template['company']));

        if($result['status'] && is_array($result['info'])){
            $company_name = $result['info']['name'];
        }else{
            $company_name = "未知";
        }

        $data = array(
            'name'=>$template['name'],
            'company_name'=>$company_name,
            'company'=>$template['company'],
            'templates'=>array()
        );

        foreach($template['freightAddress'] as $val){
            $arr = array(
                'addresses'=>$val['addresses'],
                'type'=>$template['type'],
                'firstpiece'=>$val['firstpiece'],
                'firstmoney'=>$val['firstmoney'],
                'replenishpiece'=>$val['replenishpiece'],
                'replenishmoney'=>$val['replenishmoney']
            );
            array_push($data['templates'],$arr);
        }
        return $data;
    }

    /**
     * 删除运费模版
     */
    public function delete()
    {

        $id = $this->_get('id', 0, '运费模版id缺失');
        $map = array();
        $map['id'] = $id;
        $map['uid'] = UID;//只能删除自己创建的
        $result = apiCall(FreightTemplateApi::DELETE_TEMPLATE, array($map));

        if ($result['status']) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

}