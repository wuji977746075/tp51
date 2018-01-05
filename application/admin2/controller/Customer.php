<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace app\admin\controller;


class Customer extends Admin {

	public function asker_list(){

	}

	public function index() {

	}

	/**
	 * 实名审核
	 */
	public function realname(){
		$page = array('curpage' => $this->_param('p'), 'size' => 15);
		$map=array('identity_validate'=>2);
		$result=apiCall(MemberConfigApi::QUERY,array($map,$page));
		$this->assign('member',$result['info']['list']);
		$resu=apiCall(MemberApi::QUERY_NO_PAGING,array());
		$this->assign('user',$resu['info']);
        return $this -> boye_display();
	}
	/**
	 * 审核通过
	 */
	public function pass(){
		$map=array('uid'=>$this->_param('id',0));
		$entity=array('identity_validate'=>1);
		$result=apiCall(MemberConfigApi::SAVE,array($map,$entity));
		if($result['status']){
			$this->success('操作成功');
		}else{
			$this->error('操作失败');
		}
	}
	/**
	 * 审核失败
	 */
	public function fail(){
		$map    = array('uid'=>$this->_param('id',0));
		$entity = array('identity_validate'=>0);
		$result = apiCall(MemberConfigApi::SAVE,array($map,$entity));
		if($result['status']){
			$this->success('操作成功');
		}else{
			$this->error('操作失败');
		}
	}

	/**
	 * pushMessage推送消息
	 */
	public function pushMessageAll(){
        return $this -> boye_display();
	}
	public function pushMessage($uid=0){
		if(IS_POST){
			$message_type = $this->_param('type',0,'int');
			$uid = $this->_param('uid',0,'int');
			$pushAll = $this->_param('pushAll',false,'boolean');

			$content = $this->_param('content','');
			$title = $this->_param('title','');
			$summary = $this->_param('summary','');

			//推送消息
			if($message_type==1) {
				//记录消息
				//$name = $pushAll ? MessageModel::MESSAGE_SYSTEM : MessageModel::MESSAGE_PUSH;
				$name = MessageModel::MESSAGE_SYSTEM;
				$result = apiCall(DatatreeApi::GET_INFO, array(array('name' => $name)));
				if ($result['status']) {
					$dtree_type = !empty($result['info']) ? $result['info']['id'] : 0;
				} else {
					$dtree_type = 0;
				}

				if ($pushAll) {
					$to_id = -2;
				} else {
					$to_id = $uid;
				}

				$entity = array(
					'title' => $title,
					'content' => $content,
					'to_id' => $to_id,
					'dtree_type' => $dtree_type,
					'from_id' => 0,//0:系统
					'summary' => $summary,
					'send_time' => time(),
					'status' => 1
				);
				$result = apiCall(MessageApi::RECORD_MESSAGE, array($entity));

				if ($result['status']){

					$msg_id = $result['info'];
					vendor('BoyePushApi', APP_PATH . 'Api/Vendor/UMeng/', '.class.php');

					$BoyePushApi = new BoyePushApi();

					$param = array(
						'alert' => $content,
						'ticker' => $title,
						'title' => $title,
						'text' => $content
					);

					//添加系统消息参数,after_open 跳转
					$after_open = array(
						'type' => 'go_activity',
						'param' => MessageModel::MESSAGE_SYSTEM_ACTIVITY,
						'extra' => array('id'=>$msg_id)
					);
					if ($pushAll) {
						$result = $BoyePushApi->sendAll($param,$after_open);
					} else {
						$result = $BoyePushApi->send($uid, $param,$after_open);
					}

					if ($result['status']) {
						$this->success('发送成功！', U('Admin/Message/index', array('uid' => $uid)));
					} else {
						$this->error($result['info']);
					}
				} else {
					$this->error($result['info']);
				}
			}
			//其他消息
			if($message_type==2){

				//记录消息
				//记录消息
				//$name = $pushAll?MessageModel::MESSAGE_SYSTEM:'其他消息';
				$name = MessageModel::MESSAGE_SYSTEM;
				$result = apiCall(DatatreeApi::GET_INFO,array(array('name'=>$name)));
				if($result['status']){
					$dtree_type = !empty($result['info'])?$result['info']['id']:0;
				}else{
					$dtree_type = 0;
				}

				if($pushAll){
					$to_id = -2;
				}else{
					$to_id = $uid;
				}

				$entity = array(
					'title'=>$title,
					'content'=>$content,
					'to_id'=>$to_id,
					'dtree_type'=>$dtree_type,
					'from_id'=>0,//0:系统
					'summary'=>$summary,
					'send_time'=>time(),
					'status'=>1
				);
				$result = apiCall(MessageApi::RECORD_MESSAGE,array($entity));

				if($result['status']){
					$this->success('发送成功！',U('Admin/Message/index',array('uid'=>$uid)));
				}else{
					$this->error($result['info']);
				}
			}



		}
        return $this -> boye_display();
	}

	/**
	 * 检测用户名是否已存在
	 */
	public function check_username($username){
		$result = apiCall(UserApi::CHECK_USER_NAME,array($username));
		if($result['status']){
			echo "true";
		}else{
			echo "false";
		}
	}

	/**
	 * 检测用户名是否已存在
	 */
	public function check_email(){
		$result = apiCall(UserApi::CHECK_EMAIL,array($email));
		if($result['status']){
			echo "true";
		}else{
			echo "false";
		}
	}

	/**
	 *
	 */
	public function select(){

		$map['nickname'] = array('like', "%" . $this->_param('q', '', 'trim') . "%");
		$map['uid'] = $this->_param('q',-1);
		$map['_logic'] = 'OR';
		$page = array('curpage'=>0,'size'=>20);
		$order = " last_login_time desc ";

		$result = apiCall(MemberApi::QUERY, array($map,$page, $order,false,'uid,nickname,head'));

		if($result['status']){
			$list = $result['info']['list'];

			foreach($list as $key=>$g){

				$list[$key]['id']=$list[$key]['uid'];
				$list[$key]['head'] = getImageUrl($list[$key]['head']);
			}

			$this->success($list);
		}

	}

	public function view(){
		$id = $this->_param('id',0);

		$result = apiCall(MemberApi::GET_INFO, array(array("uid"=>$id)));
		if(!$result['status']){
			$this->error($result['info']);
		}

		$this->assign("userinfo",$result['info']);

		$result = apiCall(UserApi::GET_INFO, array($id));

		if($result['status']){
			if(!is_array($result['info'])){
				$this->error($result['info']);
			}
		}else{
			$this->error('未知错误');
		}

		$this->assign("useraccount",$result['info']);

        return $this -> boye_display();
	}

}
