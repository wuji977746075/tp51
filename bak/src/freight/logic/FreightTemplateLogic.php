<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-10
 * Time: 14:22
 */

namespace app\src\freight\logic;

use app\src\base\logic\BaseLogic;
use app\src\extend\Page;
use app\src\freight\model\FreightAddress;
use app\src\freight\model\FreightTemplate;

class FreightTemplateLogic extends BaseLogic
{
    public function _init() {
        $this->setModel(new FreightTemplate());
    }
    /**
     * 根据用户ID查询运费模版
     * 使用left join,外面分组 : 2017-07-28 10:20:01
     */
    public function queryWidthUID($uid = -1, $page = ['curpage'=>0,'size'=>10], $order = false, $params = false , $field = false){
        $model = $this->getModel();

        $field = $field ? $field : 'f.*,a.addressids,a.addresses,a.firstpiece,a.firstmoney,a.replenishpiece,a.replenishmoney';
        $map = ['f.uid' => $uid];
        $query = $model->alias('f')->where($map)->join(['itboye_freight_address'=>'a'],'f.id = a.template_id','left');
        $query = $query->where($map);
        // $query = $model->relation(true)->where($map);
        if(!($order === false)){
            $query = $query->order($order);
        }
        $list = $query->field($field)->select();
        if($list === false){
            $error = $model->getDbError();
            return $this->apiReturnErr($error);

        }

        $count = $model->alias('f')->where($map)->count();
        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);
        //分页跳转的时候保证查询条件
        if ($params !== false) {
            foreach ($params as $key => $val) {
                $Page->parameter[$key] = urlencode($val);
            }
        }
        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();

        return $this->apiReturnSuc(["show" => $show, "list" => Obj2Arr($list), 'count'=>$count]);

    }
    /**
     * 添加运费模版
     */
    public function addTemplate($entity){

        $data = [
            ["name"] => $entity["name"],
            ["company"] => $entity["company"],
            ["uid"] => $entity["uid"],
            ["type"] => $entity["type"]
        ];
        dump($data);
        exit;
        $this->getModel()->save($data);

        $freightAddress = $entity["freightAddress"];

        $result = (new FreightAddress())->saveAll($freightAddress);

        if ($result === false) {
            return $this -> apiReturnErr($this -> getModel() -> getDbError());
        }
        return $this -> apiReturnSuc($result);
    }
    /**
     * 更新运费模版
     */
    public function updateTemplate($map,$entity){

        //删除旧模版地址数据

        $m = new FreightAddressModel();
        $m->startTrans();
        if(isset($map['id'])){

            $result = $m->where(array('template_id'=>$map['id']))->delete();
            if ($result === false) {
                $m->rollback();
                return $this -> apiReturnErr($this -> getModel() -> getDbError());
            }

            $m->commit();

        }

        $model = $this -> getModel();

        $result = $model->relation(true)->where($map)->save($entity);

        if ($result === false) {
            return $this -> apiReturnErr($this -> getModel()-> getDbError());
        }

        return $this -> apiReturnSuc($result);

    }
    /**
     * 查询指定运费模版
     */
    public function findTemplate($map){

        $model = $this ->getModel();

        $result = $model->where($map)->relation(true)->find();

        if ($result === false) {
            return $this -> apiReturnErr($this -> getModel() -> getDbError());
        }

        return $this -> apiReturnSuc($result);
    }
}