<?php
namespace src\base;

use think\Db;
use think\exception\DbException;
use think\Model;
use Exception;

/**
 * Logic 基类 , 出错请抛出错误
 */
abstract class BaseLogic {
    /**
     * API调用模型实例
     * @access  protected
     * @var object
     */
    private $model;

    /**
     * 构造方法，检测相关配置
     */
    public function __construct() {
        $clsName   = str_replace("Logic","",get_class($this));
        // dump($clsName);
        $this->model = new $clsName;

        $this -> init();
    }
    /**
     * 抽象方法，用于设置模型实例
     */
    protected function init(){

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
            $r = $this -> model -> where($map) -> count();
        } else {
            $r = $this -> model -> where($map) -> count($field);
        }
        return $r;

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
     * @param $field   字段合法性检测
     * @return string 错误信息或更新条数
     */
    public function save($map, $entity, $field=false) {
        $query = $this->model;
        $field && $query = $model->field($field);
        return $query -> save($entity,$map);
    }

    public function getField($map = [],$field=''){
        $r = $this->model->where($map)->field($field)->find();
        $r = is_object($r) ? $r->toArray() : $r;
        return ($r && isset($r[$field])) ? $r[$field] : '';
    }

    public function get($id=0){
        return $this->model->get($id);
    }
    /**
     * 获取数据find
     * @param $map
     * @param bool $order
     * @param bool/str/arr $field
     * @param bool $noNull
     * @param bool $lock 锁表
     * @return array
     */
    public function getInfo($map,$order=false,$field=false,$noNull=false,$lock=false) {
        $query = $this->model;

        $order && $query = $query->order($order);
        $field && $query = $query->field($field);
        $lock  && $query = $query->lock(true);
        $r = $query->where($map)->find();
        if (is_object($r) && method_exists($r,"toArray")) {
            return $r->toArray();
        }
        return $r;
    }

    /**
     * 删除
     * @map 条件
     * @result array('status'=>'false|true',$info=>'错误信息|删除数据数')
     * @param $map
     * @return array
     */
    public function delete($map) {
        return $this->model->where($map)->delete();
    }


    /**
     * 批量删除
     * @param $data
     * @return int
     */
    public function bulkDelete($data){
        return $this->model->destroy($data);
    }


    public function getInsertId($pk='id'){
        return $this->model->$pk;
    }

    /**
     * add 添加
     * @param $entity
     * @param $pk string 主键
     * @param $field string 安全字段
     * @return bool
     */
    public function add($entity,$pk='id',$field=false) {
        $r = $this -> model -> data($entity) ->isUpdate(false) -> save();
        return $pk ? $this->getInsertId($pk) : $r;
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
     * @param $fields boolean|string|arr 获取指定字段或排除指定字段
     * @return array
     */
    public function query($map = null, $order = false, $fields = false) {

        $query = $this->model;
        if(!empty($map))      $query = $query->where($map);
        if(false !== $order)  $query = $query->order($order);
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
    public function queryList($map = null, $page = false, $order = false, $params = false, $fields = false ) {

       empty($page) && $page = ['page'=>1,'size'=>10];
        $model = $this->model;
        if(!is_null($map))    $query = $model->where($map);
        if(false !== $order)  $query = $query->order($order);
        if(false !== $fields) $query = $query->field($fields);
        $start = max(intval($page['page'])-1,0)*intval($page['size']);
        $list = $query -> limit($start,$page['size']) -> select();

        // $count = $model -> where($map) -> count();
        return $list->toArray();

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
    public function queryCount($map = null, $page = false, $order = false, $params = false, $fields = false) {
        empty($page) && $page = ['page'=>1,'size'=>10];
        $query = $this->model;
        if(!empty($map)) $query = $query->where($map);
        if(false !== $order) $query = $query->order($order);
        if(false !== $fields) $query = $query->field($fields);

        $start = isset($page['start']) ? $page['start'] : max(0,(intval($page['page'])-1)*intval($page['size']));
        $list = $query -> limit($start,$page['size']) -> select();
        $count = $this -> model -> where($map) -> count();
        return ["count" => $count, "list" => $list->toArray()];

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
    public function queryPage($map = null, $page = false, $order = false, $params = false, $fields = false) {
        empty($page) && $page = ['page'=>1,'size'=>10];
        $query = $this->model;
        if(!is_null($map)) $query = $query->where($map);
        if(false !== $order) $query = $query->order($order);
        if(false !== $fields) $query = $query->field($fields);
        $start = max(intval($page['page'])-1,0)*intval($page['size']);
        $list = $query -> limit($start,$page['size']) -> select();

        $count = $this -> model -> where($map) -> count();
        // 查询满足要求的总记录数
        $Page = new \Page($count, $page['size']);
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

        return ["count"=>$count,"list" => $data ,"show" => $show];
    }
}