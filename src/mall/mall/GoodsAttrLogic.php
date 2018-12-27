<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-05
 * Time: 17:12
 */

namespace src\mall\mall;


use src\base\logic\BaseLogic;
use think\Db;
use think\Exception;

class ProductPropLogic extends BaseLogic {
    /**
     * 批量修改商品属性
     * @param $pid
     * @param $prop_value_ids
     * @return array
     */
    public function editProps($pid,$props){
        //首先删除所有商品属性
        Db::startTrans();
        try{
            $map = [
                'pid' => $pid
            ];
            $r = $this->delete($map);

            $dataList = [];
            foreach ($props as $val){
                $dataList[] = [
                    'pid' => $pid,
                    'prop_id' => $val['prop_id'],
                    'value_id' => $val['value_id'],
                    'value_name' => $val['value_name']
                ];
            }

            $y = $this->addAll($dataList);

            if($r['status']===true && $y!==false){
                Db::commit();
                return $this->apiReturnSuc('修改成功');
            }else{
                Db::rollback();
                return $this->apiReturnErr('修改失败');
            }
        }catch (Exception $ex){
            Db::rollback();
            return $this->apiReturnErr('修改失败');
        }
    }


    /**
     * 获取属性列表
     * @author hebidu <email:346551990@qq.com>
     * @param $id
     * @return array
     */
    public function queryPropList($id){

        //join-fix
        $result = Db::table("itboye_product_prop")->alias("prop")
            ->field("prop.*,dt1.propname as prop_name,dt2.valuename as value_name")
            ->join(["itboye_category_prop  dt1",""],"dt1.id = prop.prop_id","LEFT")
            ->join(["itboye_category_propvalue dt2",""],"dt2.id = prop.value_id","LEFT")
            ->where("prop.pid",$id)
            ->select();
        $tmp = [];

        foreach ($result as $vo){
            $key = md5($vo['prop_id']);
            if(!isset($tmp[$key])){
                $tmp[$key] = [
                    'prop_id'   => $vo['prop_id'],
                    'prop_name' => $vo['prop_name'],
                    'prop_value_list'=>[]
                ];
            }

            $val = [
                'value_id'=>$vo['value_id'],
                'value_name'=>$vo['value_name']
            ];

            array_push($tmp[$key]['prop_value_list'],$val);

        }

        $ret = [];

        foreach($tmp as &$item){
            array_push($ret,$item);
        }

        return $ret;
    }

}