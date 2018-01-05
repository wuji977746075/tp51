<?php
/**
 * Created by PhpStorm.
 * User: boye
 * Date: 2017/3/1
 * Time: 9:23
 */

namespace app\weixin\controller;

use app\weixin\helper\WxApiHelper;
use app\weixin\wapi\WxAddressApi;
use app\src\tool\helper\GeoHashHelper;
use app\src\user\logic\MemberLogic;
use app\src\user\logic\UserMemberLogic;
class Address extends Home
{


    public function index()
    {
        $this->assignTitle('我的收货地址');
        $memberinfo = $this->memberinfo;
        $uid = $memberinfo['uid'];
        $address = WxAddressApi::query($uid);
     //   dump($address['info']);
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

        if(!$address['status']) $this->error($address['info']);
        $this->assign('user_Info', $memberinfo);
        if ($address['status']) {
            $this->assign('address', $address['info']);
            return $this->fetch();
        }
    }

    public function address_add()
    {
        $memberinfo = $this->memberinfo;
        $uid = $memberinfo['uid'];
        $this->assignTitle('添加地址');
        if(IS_POST){
            $lat=$this->_param('lat');
            $lng=$this->_param('lng');
            $geohash = (new GeoHashHelper())->encode($lat, $lng);
            $entity = [
                'country' => '中国',
                'country_id' => 1,
                'provinceid' => $this->_param('provinceid'),
                'cityid' => $this->_param('cityid',0),
                'areaid' => $this->_param('areaid',0),
                'detailinfo' => $this->_param('detailinfo'),
                'contactname' => $this->_param('contactname'),
                'mobile' => $this->_param('mobile'),
                'postal_code' => $this->_param('postal_code','0000'),
                'province' => $this->_param('province'),
                'city' => $this->_param('city',' '),
                'area' => $this->_param('area',' '),
                'default' => $this->_param('default'),
                'lat' => $this->_param('lat'),
                'lng' => $this->_param('lng'),
                'geohash'=> $geohash
            ];
            $result = WxAddressApi::add($uid, $entity);
            if($result['status']){
                $this->success('添加成功', url('address/index'));
            }else{
                $this->error($result['info'],'');
            }

        }else{
            return $this->fetch();
        }

    }

    public function address_edit()
    {
        $memberinfo = $this->memberinfo;
        $uid = $memberinfo['uid'];
        $this->assignTitle('我的收货地址');
        if (IS_POST) {
            $id = $this->_param('id');
            $lat=$this->_param('lat');
            $lng=$this->_param('lng');
            $geohash = (new GeoHashHelper())->encode($lat, $lng);
            $entity = [

                'country' => '中国',
                'country_id' => 1,
                'provinceid' => $this->_param('provinceid'),
                'cityid' => $this->_param('cityid', 0),
                'areaid' => $this->_param('areaid', 0),
                'detailinfo' => $this->_param('detailinfo'),
                'contactname' => $this->_param('contactname'),
                'mobile' => $this->_param('mobile'),
                'postal_code' => $this->_param('postal_code', '0000'),
                'province' => $this->_param('province'),
                'city' => $this->_param('city', ' '),
                'area' => $this->_param('area', ' '),
                'default' => $this->_param('default'),
                'lat' => $this->_param('lat'),
                'lng' => $this->_param('lng'),
                'geohash'=> $geohash
            ];

            $result = WxAddressApi::update($uid, $id, $entity);

            if ($result['status']) {
                $this->success('修改成功', url('address/index'));
            } else {
                $this->error($result['info'], '');
            }
        } else {
            $id = $this->_param('id');
            $result  = WxAddressApi::query($uid);
            if ($result['status']) {
                $list = $result['info'];
                $address = [];
                foreach ($list as $val) {
                    if ($val['id'] == $id) {
                        $address = $val;
                        break;
                    }
                }
                if (empty($address)) {
                    $this->error('错误的收货地址');
                }
                $this->assign('address', $address);
            } else {
                $this->error($result['info']);
            }
            return $this->fetch();
        }

    }


    public function address_delete(){
        $memberinfo = $this->memberinfo;
        $uid = $memberinfo['uid'];
        if(IS_POST){
            $id = $this->_param('id');
            $result = WxAddressApi::delete($uid, $id);
            if($result['status']){
                $this->success('删除成功',url('address/index'));
            }else{
                $this->error($result['info'],'');
            }
        }
    }

}