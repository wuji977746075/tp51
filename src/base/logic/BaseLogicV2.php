<?php
//rainbow
//basicLogic 不处理异常版本 仅抛出

namespace app\src\base\logic;

use app\src\extend\Page;
use think\Db;
use think\exception\DbException;
use think\Model;

/**
 * Logic 基类
 */
abstract class BaseLogicV2 {

    /**
     * API调用模型实例
     * @access  protected
     * @var object
     */
    private $model;

    public $error='';
    public function getError(){
        return $this->error;
    }
    /**
     * 构造方法，检测相关配置
     */
    public function __construct() {
        $clsName   = str_replace("LogicV2","",get_class($this));
        $clsName   = str_replace("logic","model",$clsName);
        $this->model = new  $clsName;
        $this -> _init();
    }

    /**
     * 抽象方法，用于设置模型实例
     */
    protected function _init(){

    }

    /**
     * get model
     * @return Model
     */
    public function getModel() {
        return $this -> model;
    }

    /**
     * set Model
     * @param Model $model
     */
    public function setModel(Model $model){
        $this->model = $model;
    }


    /**
     * 求和统计
     * @param $map
     * @param $field
     * @return array
     */
    public function sum($map,$field){
        return $this -> model -> where($map) -> sum($field);
    }

    /**
     * 数量统计
     * @param $map
     * @param bool $field
     * @return array
     */
    public function count($map, $field = false) {

        if ($field === false) {
            $result = $this -> model -> where($map) -> count();
        } else {
            $result = $this -> model -> where($map) -> count($field);
        }
        return $result;

    }


    /**
     * 禁用
     * 必须有status字段 ，0 为禁用状态
     * @param $map
     * @return status|bool
     */
    public function disable($map) {
        return $this -> save($map, array('status' => 0));
    }

    /**
     * 启用
     * 必须有status字段，1 为启用状态
     * @param $map
     * @return status|bool
     */
    public function enable($map) {
        return $this -> save($map, array('status' => 1));
    }

    /**
     * 假删除
     * 必须有status字段，且 －1 为删除状态
     * @param $map
     * @return status|bool
     */
    public function pretendDelete($map) {
        return $this -> save($map, array('status' => -1));
    }

    /**
     * 根据id保存数据
     * @param $id
     * @param $entity
     * @return array | bool
     */
    public function saveByID($id, $entity) {
        unset($entity['id']);

        return $this -> save(['id' => $id],$entity);
    }

    /**
     * 数字类型字段有效
     * @param $map array 条件
     * @param $field string 更改字段
     * @param float|int $cnt float 增加的值
     * @return integer 返回影响记录数 或 错误信息
     */
    public function setInc($map, $field, $cnt = 1) {

        return $this -> model -> where($map) -> setInc($field, $cnt);
    }

    /**
     * 数字类型字段有效
     * @param $map array 条件
     * @param $field string 更改字段
     * @param $cnt int 减少的值 减少的值
     * @return integer|string 返回影响记录数 或 错误信息
     */
    public function setDec($map, $field, $cnt = 1) {
        return $this -> model -> where($map) -> setDec($field, $cnt);
    }
    /**
     * 数字类型字段有效,不允许小于0,维护字段最小为0,金额等敏感类型不适用
     * @param $map array 条件
     * @param $field string 更改字段
     * @param $cnt int 减少的值
     * @return integer 返回影响记录数 或 错误信息
     */
    public function setDec2($map, $field, $cnt = 1) {
        $result = $this -> model ->where($map) ->find()->toArray();

        if(!empty($result) && isset($result[$field])){
            $fieldValue = $result[$field];
            if($fieldValue - $cnt < 0) $cnt = $fieldValue;
        }

        return $this->setDec($map,$field,$cnt);

    }

    /**
     * 批量更新，仅根据主键来
     * @param $entity
     * @return array
     */
    public function saveAll($entity){
        return $this->model->saveAll($entity);
    }

    /**
     * 保存
     * @param $map
     * @param $entity
     * @return string 错误信息或更新条数
     */
    public function save($map, $entity) {
        return $this->getModel() -> save($entity,$map);
    }

    public function getField($map = [],$field=''){
        $r = $this->model->where($map)->field($field)->find();
        $r = is_object($r) ? $r->toArray() : $r;
        return ($r && isset($r[$field])) ? $r[$field] : '';
    }
    /**
     * 获取数据find
     * @param $map
     * @param bool $order
     * @param bool $field
     * @param bool $noNull
     * @param bool $lock 锁表
     * @return array
     */
    public function getInfo($map,$order=false,$field=false,$noNull=false,$lock=false) {
        $query = $this->model;

        if (false !== $order) {
            $query = $query->order($order);
        }

        if (false !== $field) {
            $query = $query->field($field);
        }

        if (false !== $lock) {
            $query = $query->lock(true);
        }

        $result = $query->where($map)->find();

        if (is_object($result) && method_exists($result,"toArray")) {
            return $result->toArray();
        }

        return $result;
    }

    /**
     * 删除
     * @map 条件
     * @result array('status'=>'false|true',$info=>'错误信息|删除数据数')
     * @param $map
     * @return array
     */
    public function delete($map)
    {

        return $this->model->where($map)->delete();

    }


    /**
     * 批量删除
     * @param $data
     * @return int
     */
    public function bulkDelete($data){
        return $this->getModel()->destroy($data);
    }


    public function getInsertId($pk='id'){
        return $this->model->$pk;
    }

    /**
     * add 添加
     * @param $entity
     * @param $pk string 主键
     * @return bool
     */
    public function add($entity,$pk='id') {

        $result = $this -> model -> data($entity) ->isUpdate(false) -> save();
        if(!empty($pk)){
            $result = $this->getInsertId($pk);
        }
        return $result;

    }

    /**
     * 批量插入
     * @param $list 数组
     * @return array
     */
    public function addAll($list){
        return $this->model->saveAll($list);
    }

    /**
     * query 不分页
     * @param $map array 查询条件
     * @param $order boolean|string 排序条件
     * @param $fields boolean|string 只获取指定字段
     * @return array
     */
    public function queryNoPaging($map = null, $order = false, $fields = false) {

        $query = $this->model;
        if(!empty($map)) $query = $query->where($map);
        if(false !== $order) $query = $query->order($order);
        if(false !== $fields) $query = $query->field($fields);
        $list = $query -> select();
        return $list ? obj2Arr($list) : [];

    }


    /**
     * query
     * @param 查询条件|null $map
     * @param array|分页参数 $page
     * @param bool|排序参数 $order
     * @param bool|点击分页时带参数 $params
     * @param bool $fields
     * @return array
     * @internal param 查询条件 $map
     * @internal param 分页参数 $page
     * @internal param 排序参数 $order
     * @internal param 点击分页时带参数 $params
     */
    public function query($map = null, $page = ['curpage'=>1,'size'=>10], $order = false, $params = false, $fields = false ) {

        $model = $this->getModel();
        if(!is_null($map))    $query = $model->where($map);
        if(false !== $order)  $query = $query->order($order);
        if(false !== $fields) $query = $query->field($fields);
        $start = max(intval($page['curpage'])-1,0)*intval($page['size']);
        $list = $query -> limit($start,$page['size']) -> select();

        $count = $model -> where($map) -> count();
        return ["count" => $count, "list" => $list];

    }

    /**
     * query
     * @param 查询条件|null $map
     * @param array|分页参数 $page
     * @param bool|排序参数 $order
     * @param bool|点击分页时带参数 $params
     * @param bool $fields
     * @return array
     * @internal param 查询条件 $map
     * @internal param 分页参数 $page
     * @internal param 排序参数 $order
     * @internal param 点击分页时带参数 $params
     */
    public function queryWithCount($map = null, $page = array('curpage'=>1,'size'=>10), $order = false, $params = false, $fields = false) {

        $query = $this->model;
        if(!empty($map)) $query = $query->where($map);
        if(false !== $order) $query = $query->order($order);
        if(false !== $fields) $query = $query->field($fields);

        $start = max(0,(intval($page['curpage'])-1)*intval($page['size']));
        $list = $query -> limit($start,$page['size']) -> select();

        $count = $this -> model -> where($map) -> count();
        return ["count" => $count, "list" => $list];

    }

    /**
     * query
     * @param 查询条件|null $map
     * @param array|分页参数 $page
     * @param bool|排序参数 $order
     * @param bool|点击分页时带参数 $params
     * @param bool $fields
     * @return array
     * @internal param 查询条件 $map
     * @internal param 分页参数 $page
     * @internal param 排序参数 $order
     * @internal param 点击分页时带参数 $params
     */
    public function queryWithPagingHtml($map = null, $page = ['curpage'=>1,'size'=>10], $order = false, $params = false, $fields = false) {
        $query = $this->model;
        if(!is_null($map)) $query = $query->where($map);
        if(false !== $order) $query = $query->order($order);
        if(false !== $fields) $query = $query->field($fields);
        $start = max(intval($page['curpage'])-1,0)*intval($page['size']);
        $list = $query -> limit($start,$page['size']) -> select();

        $count = $this -> model -> where($map) -> count();

        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);

        //分页跳转的时候保证查询条件
        if ($params !== false) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();
        $data = [];
        foreach ($list as $vo){
            if(method_exists($vo,"toArray")){
                array_push($data,$vo->toArray());
            }
        }

        return (["count"=>$count, "list" => $data ,"show" => $show]);
    }
}