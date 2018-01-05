<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/6
 * Time: 18:42
 */
namespace app\admin\controller;

use app\src\oauth2\logic\OauthClientsLogic;
class Clients extends Admin{

    public function index(){
        $map = [];
        $param = [];
        $page = array('curpage'=>$this->_param('p',0),'size'=>10);
        $order= " client_id desc ";
//        $result = apiCall(ClientsApi::QUERY,array($map,$page,$order,$fields,$param));
        $result = (new OauthClientsLogic())->queryWithPagingHtml($map,$page,$order,$param);
        ifFailedLogRecord($result,__FILE__.__LINE__);
        if(!$result['status']){
            $this->error($result['info']);
        }

        $this->assign("list",$result['info']['list']);
        $this->assign("show",$result['info']['show']);
        return $this->boye_display();
    }

    public function add(){
        if(IS_GET){
            return $this->boye_display();
        }else{
            $form = $this->_param('form/a',array());
            $grant_types = $this->_param('post.grant_types','');
            $entity = array_merge([],$form);

            import("Org.String");

//            $client_id = \String::randString(9,0);
            if(!empty($grant_types)){
                $entity['grant_types'] = implode(",",$grant_types);
            }else{
                $entity['grant_types'] = "";
            }

            $entity['user_id'] = UID;
            $entity['client_id'] = "by".uniqid().UID;
            $entity['client_secret'] =  md5(uniqid());
            $entity['api_alg'] =  $this->_param('api_alg',"");
//            dump($entity);
//            $result = apiCall(ClientsApi::ADD,array($entity));
            $result = (new OauthClientsLogic())->add($entity);
            if(!$result['status']){
                $this->error($result['info']);
            }
            $this->success("添加成功!",url('Admin/Clients/index'));
        }
    }


    public function delete(){

        $client_id = $this->_param('client_id',0);

//        $result = apiCall(ClientsApi::DELETE,array(array('client_id'=>$client_id)));
        $result = (new OauthClientsLogic())->delete(['client_id'=>$client_id]);

        if(!$result['status']){
            $this->error($result['info']);
        }
        $this->success("删除成功!",url('Admin/Clients/index'));
    }

    /**
     * 查看
     */
    public function view() {

        $map = array('id' => $this->_get('id'));
        $result = (new OauthClientsLogic())->getInfo($map);

        if ($result['status'] === false) {
            $this -> error(L('C_GET_NULLDATA'));
        } else {
            $this -> assign("vo", $result['info']);
            return $this->boye_display();
        }
    }
}
