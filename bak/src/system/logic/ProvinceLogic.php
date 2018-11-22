<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-16
 * Time: 16:47
 */

namespace app\src\system\logic;


use app\src\base\logic\BaseLogic;
use app\src\system\model\Province;
use think\Db;

class ProvinceLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new Province());
    }

    public function deleteByID($id=0){
        $provinceLogic = new ProvinceLogic();
        $id = intval($id) | 0;
        $r2 = $provinceLogic->getInfo(['id'=>$id]);

        if(!$r2['status']) $this->apiReturnErr($r2['info']);
        if($r2['info']){
            $r  = $provinceLogic->delete(array('id'=>$id));
            if(!$r['status'])   $this->apiReturnErr($r['info']);
            $fid = $r2['info']['provinceid'];
            unset($r2);
            // echo '删除'.$fid;
            if(!$fid)    $this->apiReturnErr('null provinceid ');
            $cityLogic = new CityLogic();
            $areaLogic = new AreaLogic();

            Db::startTrans();
            //查询是否有下级市
            $r = $cityLogic->queryNoPaging(['father'=>$fid]);
            if(!$r['status']){
                Db::rollback();
                $this->apiReturnErr($r['info']);
            }else{
                if($r['info']){
                    //有下级市
                    foreach ($r['info'] as $k => $v) {

                        $fid = $v['cityid'];
                        if(!$fid){
                            Db::rollback();
                            $this->apiReturnErr('cityid is null');
                        }
                        $r  = (new CityLogic())->delete(['cityID'=>$fid]);
                        if(!$r['status']){
                            Db::rollback();
                            $this->apiReturnErr($r['info']);
                        }

                        //删除下级区
                        $r  =  $areaLogic->delete(['father'=>$fid]);
                        if(!$r['status']){
                            Db::rollback();
                            $this->apiReturnErr($r['info']);
                        }
                    }
                }

                $this->apiReturnSuc('删除成功');
            }
        }else{
            $this->apiReturnErr('null province');
        }
    }
}