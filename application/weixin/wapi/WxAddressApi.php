<?php
/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/1/14
 * Time: 上午11:52
 */

namespace app\weixin\wapi;

use app\weixin\helper\WxApiHelper;

class WxAddressApi
{
    /**
     * 收货地址查询
     * @param $uid
     * @return array
     * @param string $sid
     */
    public static function query($uid, $s_id = 'itboye'){
        $data = [
            'api_ver' => 101,
            'uid' => $uid,
            's_id' => $s_id,
        ];

        return WxApiHelper::callRemote('By_Address_query', $data);
    }

    /**
     * 收货地址新增
     * @param $uid
     * @param $entity
     * @param string $sid
     * @return array
     */
    public static function add($uid, $entity, $s_id = 'itboye'){
        $data = [
            'api_ver' => 102,
            'uid' => $uid,
            's_id' => $s_id,
            'default' => $entity['default'],
            'country' => $entity['country'],
            'country_id' => $entity['country_id'],
            'province' => isset($entity['province']) ? $entity['province'] : '',
            'city' => isset($entity['city']) ? $entity['city'] : '',
            'area' => isset($entity['area']) ? $entity['area'] : '',
            'provinceid' => $entity['provinceid'],
            'cityid' => $entity['cityid'],
            'areaid' => $entity['areaid'],
            'detailinfo' => $entity['detailinfo'],
            'contactname' => $entity['contactname'],
            'mobile' => $entity['mobile'],
            'postal_code' => $entity['postal_code'],
            'wxno' => isset($entity['wxno']) ? $entity['wxno'] : '',
            'id_card' => isset($entity['id_card']) ? $entity['id_card'] : '',
            'lat' => $entity['lat'],
            'lng' => $entity['lng'],
            'geohash' => $entity['geohash']
        ];

        return WxApiHelper::callRemote('By_Address_add', $data);
    }

    /**
     * 收货地址删除
     * @param $uid
     * @param $id
     * @param string $sid
     * @return array
     */
    public static function delete($uid, $id, $s_id = 'itboye'){
        $data = [
            'api_ver' => 101,
            'uid' => $uid,
            's_id' => $s_id,
            'id' => $id
        ];

        return WxApiHelper::callRemote('By_Address_delete', $data);
    }

    /**
     * 收货地址更新
     * @param $uid
     * @param $entity
     * @param string $sid
     * @return array
     */
    public static function update($uid, $id, $entity, $s_id = 'itboye'){
        $data = [
            'api_ver' => 101,
            'uid' => $uid,
            'id' => $id,
            's_id' => $s_id,
            'default' => $entity['default'],
            'country' => $entity['country'],
            'country_id' => $entity['country_id'],
            'province' => isset($entity['province']) ? $entity['province'] : '',
            'city' => isset($entity['city']) ? $entity['city'] : '',
            'area' => isset($entity['area']) ? $entity['area'] : '',
            'provinceid' => $entity['provinceid'],
            'cityid' => $entity['cityid'],
            'areaid' => $entity['areaid'],
            'detailinfo' => $entity['detailinfo'],
            'contactname' => $entity['contactname'],
            'mobile' => $entity['mobile'],
            'postal_code' => $entity['postal_code'],
            'wxno' => isset($entity['wxno']) ? $entity['wxno'] : '',
            'id_card' => isset($entity['id_card']) ? $entity['id_card'] : '',
            'lat' => $entity['lat'],
            'lng' => $entity['lng'],
            'geohash' => $entity['geohash']

        ];

        return WxApiHelper::callRemote('By_Address_update', $data);
    }

}