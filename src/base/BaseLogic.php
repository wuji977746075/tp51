<?php
namespace src\base;

use think\Db;
use think\exception\DbException;
use think\Model;
use Exception;
use src\base\traits\Jump;

/**
 * Logic 基类 , 出错请抛出错误
 */
abstract class BaseLogic {
    use Jump;
    const CACHE_TIME = 600;
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

    protected function isValidInfo($id,$field='id',$err=true){
        $info = $this->getInfo([$field=>$id]);
        if($err === true) {
            $err = Linvalid($field.'=>'.$id);
        }else{
        }
        if($err && empty($info)){
            $this->err($err);
        }
        return $info;
    }

    public function trans(){
        Db::startTrans();
    }
    public function back(){
        Db::rollback();
    }
    public function commit(){
        Db::commit();
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
    public function setDecUnsigned($map, $field, $cnt = 1) {
        $r = $this -> model ->where($map) ->find();
        if(!empty($r) && isset($r[$field])){
            $fieldValue = $r[$field];
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
    // 多条件查单字段
    public function getField($map = [],$field='',$nullErr=false){
        $r = $this->model->where($map)->field($field)->find();
        if($nullErr && (empty($r) || !isset($r[$field])) ){
            throws(is_string($nullErr) ? $nullErr : $field.'或条件非法');
        }
        return ($r && isset($r[$field])) ? $r[$field] : '';
    }
    // 单条件查单字段
    // ModelNotFoundException
    public function get($id=0,$f=false){
        if($f) $r = $this->model->getOrFail($id);
        else $r = $this->model->get($id);
        return $r;
    }
    // @throws ModelNotFoundException
    function __call($method,$args){
        if(substr($method, 0,5) == 'getBy'){
            // 单条件查单数据 Model::getBy
            $f = false;
            if(count($args)>1){
                $f = $args[1];unset($args[1]);
            }
            $r = call_user_func_array([$this->model,$method], $args);
            $this->checkDbNull($r,$f);
            return $r;
        }else if(substr($method, 0,10) == 'getFieldBy'){
            // 单条件查单字段 Model::getFieldBy
            $f = false;
            if(count($args)>2){
                $f = $args[2];
            }
            $r = $this->model->$method($args[1],$args[0]);
            $this->checkDbNull($r,$f);
            return $r;
        }else if(substr($method, 0,5) == 'where'){
            // where 及where系方法 可连携
            return call_user_func_array([$this->model,$method], $args);
        }
    }
    // 检查数据返回空
    // @throws ModelNotFoundException
    public function checkDbNull($r,$f=false){
        if($f && empty($r)){
            $class = get_class($this->model);
            throw new \think\db\exception\ModelNotFoundException(is_string($f) ? $f : 'model data Not Found:' . $class, $class);
        }
    }
    /**
     * 获取数据find
     * 填写max()等field 返回 bug : ['level'=>null,..] 2018-10-29 14:55:42
     * @param $map
     * @param bool $order
     * @param bool/str/arr $field
     * @param bool $noNull
     * @return array
     */
    public function getInfo($map,$order=false,$field=false,$noNull=false) {
        $query = $this->model;
        $order && $query = $query->order($order);
        $field && $query = $query->field($field);
        // $lock  && $query = $query->lock(true);
        $r = $query->where($map)->find(); // object
        // $r = $r ? $r->toArray() : [];
        $noNull && $this->checkDbNull($r,$noNull);
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
        // $r = $this -> model -> data($entity,true) -> save();
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
         $list = $query -> select(); //think\model\Collection
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
        $count = $this -> model -> where($map) -> count();
        $list  = [];
        if($count){
            $query = $this->model;
            if(!empty($map)) $query = $query->where($map);
            if(false !== $order) $query = $query->order($order);
            if(false !== $fields) $query = $query->field($fields);

            $start = isset($page['start']) ? $page['start'] : max(0,(intval($page['page'])-1)*intval($page['size']));
            $list = $query -> limit($start,$page['size']) -> select();
            $list = $list->toArray();
        }
        return ["count" => $count, "list" => $list];

    }
    function queryCountWithUser($map = null, $page = false, $order = false, $params = false, $fields = "p.*,ifnull(u.name,'') as uname,ifnull(u.nick,'') as unick",$jo="uid") {
        $count = $this -> model ->alias('p')
        ->join([PRE.'user'=>'u'],'p.'.$jo.' = u.id','left')
        -> where($map)
        -> count();
        $list = [];
        if($count){
            empty($page) && $page = ['page'=>1,'size'=>10];
            $query = $this-> model ->alias('p')->join([PRE.'user'=>'u'],'p.'.$jo.' = u.id','left');
            if(!empty($map)) $query = $query->where($map);
            if(false !== $order) $query = $query->order($order);
            if(false !== $fields) $query = $query->field($fields);

            $start = isset($page['start']) ? $page['start'] : max(0,(intval($page['page'])-1)*intval($page['size']));
            $list = $query -> limit($start,$page['size']) -> select();
        }
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
        $count = $this -> model -> where($map) -> count();
        $list  = [];$show = L('no-query-data');
        $data = [];
        if($count){
            empty($page) && $page = ['page'=>1,'size'=>10];

            $query = $this->model;
            if(!is_null($map)) $query = $query->where($map);
            if(false !== $order) $query = $query->order($order);
            if(false !== $fields) $query = $query->field($fields);
            $start = max(intval($page['page'])-1,0)*intval($page['size']);
            $list = $query -> limit($start,$page['size']) -> select();
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
            foreach ($list as $vo){
                if(method_exists($vo,"toArray")){
                    array_push($data,$vo->toArray());
                }
            }
        }
        return ["count"=>$count,"list" => $data ,"show" => $show];
    }
}