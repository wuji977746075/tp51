<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-19
 * Time: 14:39
 */

namespace app\src\category\logic;


use app\src\base\logic\BaseLogic;
use app\src\category\model\Category;
use think\Db;

class CategoryLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new Category());
    }

    /**
     * @param $lang string 语言
     * @return array
     */
    public function queryMainCategory($lang){
        try{
            $map = ['parent'=>0];
            $result = Db::table('itboye_category')
                ->alias('c')
                ->join(['itboye_category_prop'=>'cp'],'c.id = cp.cate_id','left')
                ->field(['c.id' => 'id', 'img_id', 'name', 'parent', 'level', 'display_order', 'cp.propname' => "prop_name", 'cp.id' => 'prop_id'])
                ->order('level asc,display_order desc')
                ->where($map)
                ->where(['lang'=>$lang])
                ->select();

            return $this->apiReturnSuc($result);
        }catch (\Exception $e){
            return $this->apiReturnErr($e->getMessage());
        }
    }

    /**
     *
     * @param $cate_id int 类目id
     * @param $lang string 语言
     * @return array
     */
    public function querySubCategory($cate_id,$lang){

        $map = 'root_id = '.$cate_id;

        $result = Db::table('itboye_category')
        ->alias('c')
            ->field(['id', 'name', 'parent', 'level', 'display_order', 'root_id', 'img_id'])
        ->order('level asc,display_order desc')
        ->where($map)
            ->where(['lang'=>$lang])
        ->select();

        //转化成2级 树结构 ，暂定只处理level 2,3
        if(is_array($result)){
            $list = [];
            foreach ($result as $vo){
                if($vo['level'] == 2){
                    $list[$vo['id']] = $vo;
                    $list[$vo['id']]['children'] = [];
                }
            }

            foreach ($result as $vo){
                if($vo['level'] == 3){
                    if(isset($list[$vo['parent']])){
                        array_push($list[$vo['parent']]['children'],$vo);
                    }
                }
            }
            $result = [];
            
            foreach ($list as $vo){
                array_push($result,$vo);
            }
        }


        return $this->apiReturnSuc($result);
    }

    public function queryAllCategory(){

        $result = Db::table('itboye_category')
            ->alias('c')
            ->field(['id', 'name', 'parent', 'level', 'display_order', 'root_id', 'img_id'])
            ->order('level asc,display_order desc')
            ->select();
        $first  = [];
        $second = [];
        $third  = [];
        $format = [];
        foreach ($result as &$item){
            if($item['level'] == 1){
                $item['name'] = ''.$item['name'];
                $first[] = $item;
            }
            elseif($item['level'] == 2){
                $item['name'] = '--'.$item['name'];
                $second[]= $item;
            }elseif($item['level'] == 3){
                $item['name'] = '----'.$item['name'];
                $third[]= $item;
            }
        }
        foreach ($first as $vo){
            $format[] = $vo;
            foreach ($second as $val) {
                if($val['parent'] == $vo['id']) {
                    $format[] = $val;
                }
                foreach ($third as $val2){
                    if($val2['parent'] == $val['id']) {
                        $format[] = $val2;
                    }
                }
            }

        }


        return $this->apiReturnSuc($format);
    }

}
