<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\src\base\logic;

use app\src\base\helper\ExceptionHelper;
use app\src\extend\Page;
use think\exception\DbException;
use think\Model;

/**
 * Logic 基类
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

        // $clsName   = str_replace("Logic","",get_class($this));
        // $clsName   = str_replace("logic","model",$clsName);
        // $this->model = new  $clsName;
        // $this -> _init();
    }

    /**
     * 抽象方法，用于设置模型实例
     */
    protected function _init(){

    }

    /**
     * 返回错误结构
     * @param $info
     * @param $trans 错误是否回滚
     * @return array
     */
    protected function apiReturnErr($info,$trans=false) {
        if($trans) \think\Db::rollback();
        if($info instanceof  DbException){
            $info = ExceptionHelper::getErrorString($info);
        }
        if(is_string($info)) $info = str_replace(",", "，", $info);
        return array('status' => false, 'info' => $info);
    }

    /**
     * 返回成功结构
     * @param $info
     * @return array
     */
    protected function apiReturnSuc($info) {
        return array('status' => true, 'info' => $info);
    }

    /**
     * 返回结构
     * @param $status
     * @param $info
     * @return array
     */
    protected function apiReturn($status, $info) {
        return array('status' => $status, 'info' => $info);
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
        try{

            $result = $this -> model -> where($map) -> sum($field);

            return $this -> apiReturnSuc($result);
        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }

    /**
     * 数量统计
     * @param $map
     * @param bool $field
     * @return array
     */
    public function count($map, $field = false) {
        try{

            if ($field === false) {
                $result = $this -> model -> where($map) -> count();
            } else {
                $result = $this -> model -> where($map) -> count($field);
            }

            return $this -> apiReturnSuc($result);

        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
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

        return $this -> save(array('id' => $id),$entity);
    }

    /**
     * 数字类型字段有效
     * @param $map array 条件
     * @param $field string 更改字段
     * @param float|int $cnt float 增加的值
     * @return integer 返回影响记录数 或 错误信息
     */
    public function setInc($map, $field, $cnt = 1) {
        try{
            $result = $this -> model -> where($map) -> setInc($field, $cnt);
            return $this -> apiReturnSuc($result);
        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }

    /**
     * 数字类型字段有效
     * @param $map array 条件
     * @param $field string 更改字段
     * @param $cnt int 减少的值 减少的值
     * @return integer|string 返回影响记录数 或 错误信息
     */
    public function setDec($map, $field, $cnt = 1) {
        try{
            $result = $this -> model -> where($map) -> setDec($field, $cnt);
            return $this -> apiReturnSuc($result);
        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }
    /**
     * 数字类型字段有效,不允许小于0,维护字段最小为0,金额等敏感类型不适用
     * @param $map array 条件
     * @param $field string 更改字段
     * @param $cnt int 减少的值
     * @return integer 返回影响记录数 或 错误信息
     */
    public function setDec2($map, $field, $cnt = 1) {
        try{
            $result = $this -> model ->where($map) ->find()->toArray();

            if(!empty($result) && isset($result[$field])){
                $fieldValue = $result[$field];
                if($fieldValue - $cnt < 0) $cnt = $fieldValue;
            }

            return $this->setDec($map,$field,$cnt);

        }catch (DbException $ex) {
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }

    }

    /**
     * 批量更新，仅根据主键来
     * @param $entity
     * @return array
     */
    public function saveAll($entity){
        try{
            $result = $this->model->saveAll($entity);
            return $this -> apiReturnSuc($result);
        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }

    /**
     * 保存
     * @param $map
     * @param $entity
     * @return string 错误信息或更新条数
     */
    public function save($map, $entity) {
        try{

            $result = $this->getModel() -> save($entity,$map);

            return $this -> apiReturnSuc($result);
        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
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
        try {
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
                return $this->apiReturnSuc($result->toArray());
            }

            return $this->apiReturnSuc($result);

        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
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
        try {

            $result = $this->model->where($map)->delete();

            return $this->apiReturnSuc($result);

        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }


    /**
     * 批量删除
     * @param $data
     * @return array
     */
    public function bulkDelete($data)
    {
        try {
            $result = $this->getModel()->destroy($data);
            return $this->apiReturnSuc($result);
        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }


    public function getInsertId($pk='id'){
        return $this->model->$pk;
    }

    /**
     * add 添加
     * @param $entity
     * @param $pk string 主键
     * @return array
     */
    public function add($entity,$pk='id') {

        try{
            $result = $this -> model -> data($entity) ->isUpdate(false) -> save();
            if(!empty($pk)){
                $result = $this->getInsertId($pk);
            }

            return $this -> apiReturnSuc($result);

        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }

    /**
     * 批量插入
     * @param array $list 数组
     * @return array
     */
    public function addAll($list){
        try{
            $result = $this-> model->saveAll($list);
            return $this->apiReturnSuc($result);
        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }

    /**
     * query 不分页
     * @param $map array 查询条件
     * @param $order boolean|string 排序条件
     * @param $fields boolean|string 只获取指定字段
     * @return array
     */
    public function queryNoPaging($map = null, $order = false, $fields = false) {

        try{
            $query = $this->model;
            if(!empty($map)) $query = $query->where($map);
            if(false !== $order) $query = $query->order($order);
            if(false !== $fields) $query = $query->field($fields);
            $list = $query -> select();
            $data = [];
            foreach ($list as $vo){
                if(method_exists($vo,"toArray")){
                    array_push($data,$vo->toArray());
                }
            }

            return $this -> apiReturnSuc($data);
        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }

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

        try{
            $query = $this->model;
            if(!is_null($map)) $query = $query->where($map);
            if(false !== $order) $query = $query->order($order);
            if(false !== $fields) $query = $query->field($fields);
            $start = max(intval($page['curpage'])-1,0)*intval($page['size']);
            $list = $query -> limit($start,$page['size']) -> select();

            if (false === $list) return $this -> apiReturnErr($this -> model -> getError());
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

            return $this -> apiReturnSuc(["show" => $show, "list" => $data]);
        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }

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
    public function query($map = null, $page = ['curpage'=>1,'size'=>10], $order = false, $params = false, $fields = false) {

        try{
            $query = $this->model;
            if(!is_null($map)) $query = $query->where($map);
            if(false !== $order) $query = $query->order($order);
            if(false !== $fields) $query = $query->field($fields);
            $start = max(intval($page['curpage'])-1,0)*intval($page['size']);
            $list = $query -> limit($start,$page['size']) -> select();

            if (false === $list) return $this -> apiReturnErr($this -> model -> getError());
            $count = $this -> model -> where($map) -> count();

            $data = [];
            foreach ($list as $vo){
                if(method_exists($vo,"toArray")){
                    array_push($data,$vo->toArray());
                }
            }
            return $this -> apiReturnSuc(["count" => $count, "list" => $data]);
        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }

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
        try{
            $query = $this->model;
            if(!empty($map)) $query = $query->where($map);
            if(false !== $order) $query = $query->order($order);
            if(false !== $fields) $query = $query->field($fields);

            $start = max(0,(intval($page['curpage'])-1)*intval($page['size']));
            $list = $query -> limit($start,$page['size']) -> select();

            $count = $this -> model -> where($map) -> count();
            $data = [];
            foreach ($list as $vo){
                if(method_exists($vo,"toArray")){
                    array_push($data,$vo->toArray());
                }
            }
            return $this -> apiReturnSuc(["count" => $count, "list" => $data]);
        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }

    public function getField($map = [],$field=''){
        $r = $this->model->where($map)->field($field)->find();
        $r = is_object($r) ? $r->toArray() : $r;
        return ($r && isset($r[$field])) ? $r[$field] : '';
    }
}
