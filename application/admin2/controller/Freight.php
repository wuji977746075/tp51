<?php
/**
 * Created by PhpStorm.
 * User: Zhoujinda
 * Date: 2016/3/14
 * Time: 15:11
 */

namespace app\admin\controller;

use app\src\freight\logic\FreightTemplateLogic;
use app\src\goods\logic\ProductLogic;
use app\src\store\logic\StoreLogic;
use think\Log;

class Freight extends Admin {
    private $store_id;
    public function _initialize(){
        parent::_initialize();
        $this->store_id = session("current_store_id");
        if(empty($this->store_id)){
            $this->store_id = $this->_param('store_id',0);
            session("current_store_id",$this->store_id);
        }
        //dump($_SESSION);
        if(empty($this->store_id)){
            $uid = UID;
            $map = array(
                "uid"=> $uid
            );

            $result = (new StoreLogic())->getInfo($map);
            if($result['status']){
                $this->store_id = $result['info']['id'];
            }
        }
        if (empty($this->store_id)) {
            $this -> error("缺少店铺ID参数！");
        }

    }

    public function index(){

        $onshelf = $this->_param('onshelf', 0);
        $name = urldecode($this->_param('name', ''));

        //检测store_id 是否合法
        $result = (new StoreLogic())->getInfo(['id'=>$this->store_id,'uid'=>UID]);

        if(!$result['status']){
            $this -> error($result['info']);
        }

        if(is_null($result['info'])){
            $this -> error("店铺ID不合法!");
        }

        $params = array('onshelf' => $onshelf,'store_id'=>$this->store_id);

        $map = array();
        if (!empty($name)) {
            $map['name'] = array('like', '%'.$name.'%');
            $params['name'] = $name;
        }
        $map['onshelf'] = $onshelf;
        $map['status'] = 1;
        $map['store_id'] = $this->store_id;
        $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
        $order = " create_time desc ";

        $result = (new ProductLogic())->queryWithPagingHtml($map, $page, $order, $params);

        //
        if ($result['status']) {

            $this -> assign('name', $name);
            $this -> assign('onshelf', $onshelf);
            $this -> assign('store_id', $this->store_id);
            $this -> assign('show', $result['info']['show']);
            $this -> assign('list', $result['info']['list']);

            $store = (new StoreLogic())->getInfo(['id'=>$this->store_id]);
            if(!$store['status']){
                $this->error($store['info']);
            }
            $this->assign("store",$store['info']);

            //查询运费模版
            $result = (new FreightTemplateLogic())->queryNoPaging(['uid'=>UID]);
            if($result['status']){
                $this->assign('freight_template',$result['info']);
            }else{
                $this -> error($result['info']);
            }

            return $this -> boye_display();
        } else {
            Log::Record('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
            $this -> error($result['info']);
        }

    }


    /**
     * 应用运费模版/批量应用
     * id,ids
     */
    public function save2() {
        $template_id = $this->_param('template_id',0);
        $id = $this->_param('p_id', -1);
        $ids = $this->_param('ids', -1);

        if ($ids === -1 && $id === -1) {
            $this -> error("缺少参数！");
        }
        if($template_id == 0){
            $this -> error("必须选择一个运费模版");
        }
        if ($ids === -1) {
            $map = array('id' => $id);
        } else {
            $map = array();

            $ids = implode(',', $ids);
            $map = array('id' => array('in', $ids));
        }
        $result = (new ProductLogic())->save([$map,['template_id'=>$template_id]]);

        if ($result['status']) {
            if (IS_AJAX) {
                $this -> success("应用成功！");
            } else {
                $this -> success("应用成功！", U('Admin/' . CONTROLLER_NAME . '/index'));
            }
        } else {
            $this -> error($result['info']);
        }
    }

}