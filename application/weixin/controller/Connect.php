<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\weixin\controller;
use think\Controller;
//use app\weixin\Logic\WxaccountLogic;
use app\weixin\Logic\WeixinLogic;
use app\src\wxpay\logic\WxaccountLogic;
//use weixin\Api\WxreplyNewsApi;
//use Weixin\Api\WxreplyTextApi;
//use Weixin\Api\WxuserApi;
//use Admin\Api\NewmemberApi;
//use Admin\Api\RoleApi;
//use app\weixin\Api\testApi;
/*
 * 微信通信控制器
 */
class Connect extends Weixin {

	const MSG_TYPE_TEXT = 'text';
	const MSG_TYPE_IMAGE = 'image';
	const MSG_TYPE_VOICE = 'voice';
	const MSG_TYPE_VIDEO = 'video';
	const MSG_TYPE_MUSIC = 'music';
	const MSG_TYPE_NEWS = 'news';
	const MSG_TYPE_LOCATION = 'location';
	const MSG_TYPE_LINK = 'link';
	const MSG_TYPE_EVENT = 'event';
    //微信默认返回
    const DEFAULT_REPLY = "";

	//TOKEN ，通信地址参数，非微信接口配置中的token
	private $token;
	//通信消息主体
	public $data = array();
	//通信的粉丝的可获取的信息
	public $fans;
	//当前通信的公众号信息
	public $wxaccount;
	
	private $wxapi;
	
	private function getPluginParams(){
		return array("fans"=>$this->fans,"data"=>$this->data,"wxaccount"=>$this->wxaccount);
	}
	
	protected function _initialize() {
		parent::_initialize();
		
	}	
	
	public function index() {

		if (!class_exists('SimpleXMLElement')) {
			exit('SimpleXMLElement class not exist');
		}
		if (!function_exists('dom_import_simplexml')) {
			exit('dom_import_simplexml function not exist');
		}
		$this -> token = input('get.token', "htmlspecialchars");
		if (!preg_match("/^[0-9a-zA-Z]{3,42}$/", $this -> token)) {
			exit('error id');
		}


//        addWeixinLog("准备获取公众号信息","wxuser getInfo");
		//获取当前通信的公众号信息
		//$this -> wxaccount = cache('weixin_' . $this -> token);
		if (!$this -> wxaccount) {
			//$result = apiCall(WxaccountApi::GET_INFO, array( array('token' => $this -> token)));
			$result=(new WxaccountLogic())->getInfo(['id'=>1]);
			$account=['id'=>1,
					  'appid'=>' wx58fe237b1746d7b0',
					  'appsecret'=>'5da0ee40096800c6dab7339fa300ff64',
					  'encodingAESKey'=>'bYFZhIu2MzlEoqPdjk93O3FLM27XkwXR71Wy9Nf2pWr',
					  'encodingaeskey'=>'bYFZhIu2MzlEoqPdjk93O3FLM27XkwXR71Wy9Nf2pWr',
					  'uid'=>4,
					  'token'=>'pvifkmrw1476152475'];
			$result=['status'=>true,'info'=>$account];
			if ($result['status']) {
				$this -> wxaccount = $result['info'];
			}
			cache('weixin_' . $this -> token, $this -> wxaccount, 600);
			//缓存10分钟
		}
		$this->wxapi=new WeixinLogic($this -> wxaccount['appid'], $this -> wxaccount['appsecret']);
		//$this->wxapi=new WeixinApi($this -> wxaccount['appid'], $this -> wxaccount['appsecret']);
		if (input('test','0') == 1) {
			$this -> data['Event'] = (input('post.event', ''));
			$this -> data['MsgType'] = (input('post.msgtype', ''));
			$this -> data['Content'] = (input('post.keyword', ''));
			echo json_encode($this -> reply(),JSON_UNESCAPED_UNICODE);
			return;
		}

//        dump("测试Wechat是否引入");
		import("@.Common.Wechat");
		$weixin = new \Wechat($this -> token, $this -> wxaccount['encodingaeskey'], $this -> wxaccount['appid']);
//        dump($weixin);
		$this -> data = $weixin -> request();
		if ($this -> data && is_array($this -> data)) {
			$fanskey = "appid_".$this -> wxaccount['appid']."_" . $this->getOpenID();
			//读取缓存的粉丝信息
			$this -> fans = cache($fanskey);
			if (is_null($this->fans) || $this -> fans === false) {
				$result = apiCall(WxuserApi::GET_INFO, array( array('wxaccount_id'=>$this -> wxaccount['id'], 'openid' => $this->getOpenID())));
				addWeixinLog($result,"wxuser getInfo");
				if ($result['status'] && is_array($result['info'])) {
					S($fanskey,  $result['info'],600);//10分钟
					$this -> fans = $result['info'];
				} else {
                    //认证服务号，才能使用
					$this->addWxuser();
					$this -> fans = null;
					cache($fanskey,  null);//清除
				}

			}
            addWeixinLog($this -> data['Event'],'事件类型');
			$reply = $this -> reply();
			if(empty($reply)){
				exit("success");
			}
			list($content, $type) = $reply;
//			$weixin -> response(serialize($content), self::MSG_TYPE_TEXT);
			$weixin -> response($content, $type);
		} else {
			$weixin -> response("我不知道你讲的是什么意思 : )", self::MSG_TYPE_TEXT);
		}
	}
	
	//响应
	private function reply() {

		import("@.Common.Wechat");
		//转化为小写
		$this -> data['Event'] = strtolower($this -> data['Event']);
		$this -> data['MsgType'] = strtolower($this -> data['MsgType']);
        addWeixinLog('zjq',"接受消息");
		if($this->data['Event']  != \Wechat::MSG_EVENT_LOCATION){
			addWeixinLog($this->data,"【来自微信服务器消息】");
		}
		$return = self::DEFAULT_REPLY;
		
		//=====================微信事件转化为系统内部可处理
		if ($this -> data['MsgType'] == self::MSG_TYPE_EVENT) {
			//接收事件推送
			switch ($this->data['Event']) {
				
				case \Wechat::MSG_EVENT_CLICK :
					$return = $this -> menuClick();
					break;
				case \Wechat::MSG_EVENT_VIEW :
					$return = $this -> menuView();
					break;
				case \Wechat::MSG_EVENT_SCAN :
					$return = $this -> qrsceneScan();
					break;
				case \Wechat::MSG_EVENT_MASSSENDJOBFINISH :
					//群发任务结束
					break;
				case \Wechat::MSG_EVENT_SUBSCRIBE :
					$return = $this -> subscribe();
					break;
				case \Wechat::MSG_EVENT_UNSUBSCRIBE :
					$return = $this -> unsubscribe();
					break;
				case \Wechat::MSG_EVENT_LOCATION :
					//用户自动上报地理位置
					$return = $this -> locationProcess();
					break;
				default :
					break;
			}
		} else {
			//接受普通消息
			switch ($this->data['MsgType']) {
				case self::MSG_TYPE_TEXT :
					$return = $this -> textProcess();
					break;
				case self::MSG_TYPE_IMAGE :
					$return = $this -> imageProcess();
					break;
				case self::MSG_TYPE_VIDEO :
					$return = $this -> videoProcess();
					break;
				case self::MSG_TYPE_LOCATION :
					//用户手动发送地理位置
					$return = $this -> locationProcess();
					//群发任务结束
					break;
				case self::MSG_TYPE_LINK :
					break;
				case self::MSG_TYPE_VOICE :
					$return = $this -> voiceProcess();
					break;
				default :
					break;
			}
		}
		
		//=====================系统内置其它方法响应微信处理

		if(empty($return)){
			//只在上面的处理方法，无法处理时才进行下面处理
			$return = $this->innerProcess();
		}

        addWeixinLog($return,"返回内容");
		return $return;
	}

	//END reply
	
	private $Plugins = array(
		'_promotioncode_'=>"Promotioncode",
	);
	
	private function innerProcess(){

        addWeixinLog($this->data,"innerProcess");
//        return array($this -> getOpenID(), self::MSG_TYPE_TEXT);
		//系统内置关键词处理方式
		//统一以包括上_
		switch (strtolower($this->data['Content'])) {
			case 'id' :
				// 当前粉丝的openid
				$return = array($this -> getOpenID(), self::MSG_TYPE_TEXT);
				break;
//			case '_promotioncode_':
//				//TODO: 考虑从数据库中取得 关键词对应的插件标识名
//				addWeixinLog($this->getPluginParams(),"[Promotioncode]");
//				$return = pluginCall($this->Plugins['_promotioncode_'],array($this->getPluginParams()));
//				
//				$return = pluginCall("Promotioncode",array($this->getPluginParams()));
//				break;
			default :
                $return = pluginCall("Unknown",array($this->getPluginParams()));
				//TODO: 可以检测用户请求数
				break;
		}
		
		return $return;
	}
	
	//=======================用户发送给公众号的消息类型
	/**
	 * 处理用户发送的视频消息
	 */
	private function videoProcess() {
		return self::DEFAULT_REPLY;
	}
	
	/**
	 * 处理用户发送的文本消息
	 */
	private function textProcess($keyword='') {
		if(empty($keyword)){
			$keyword = $this->data['Content'];
		}
		
		$map = array('keyword'=>$keyword);
//        addWeixinLog("进入文字关键词处理","textProcess");
		//文本响应
		$result = apiCall(WxreplyTextApi::GET_INFO,array($map));

//        addWeixinLog($result,"textProcess");
		if($result['status'] && is_array($result['info'])){
			return array((($result['info']['content'])) , self::MSG_TYPE_TEXT);
		}

//        addWeixinLog($result,"WxreplyNewsApi");
		//图文响应
		$result = apiCall(WxreplyNewsApi::QUERY_WITH_PICTURE,array($map,'sort desc'));

//        addWeixinLog($result,"WxreplyNewsApi");
		if($result['status'] && !is_null($result['info'])){
			$siteurl = C("SITE_URL");
			//多图文
			$newslist = array();
			foreach($result['info'] as $key=>$news){				
					array_push($newslist,array($news['title'],$news['description'],$siteurl.getPictureURL($news['piclocal'],$news['picremote']),$news['url']));
			}	
			return array($newslist , self::MSG_TYPE_NEWS);
		}

		return self::DEFAULT_REPLY;
	}

	/**
	 * 处理用户发送的图片消息
	 */
	private function imageProcess() {
		$keyword = $this->data['Content'];

		return self::DEFAULT_REPLY;
	}

	/**
	 * 处理用户发送的语音消息
	 */
	private function voiceProcess() {
		$this -> data['Content'] = $this -> data['Recognition'];
		return self::DEFAULT_REPLY;
	}

	/**
	 * 地理位置上报处理
	 */
	private function locationProcess() {
		//ToUserName	开发者微信号
		//FromUserName	发送方帐号（一个OpenID）
		//CreateTime	消息创建时间 （整型）
		//MsgType	消息类型，event
		//Event	事件类型，LOCATION
		//Latitude	地理位置纬度
		//Longitude	地理位置经度
		//Precision	地理位置精度
		
		//TODO: 地理位置上报处理
        //Latitude:30.313025
        //Longitude:120.366547
        //Precision:65.000000
        $lat = $this->data['Latitude'];
        $lng = $this->data['Longitude'];

        $uid = $this->fans['id'];
        $entity = array('lat'=>$lat,'lng'=>$lng,'loc_update_time'=>time());
        $result = apiCall(WxuserApi::SAVE_BY_ID,array($uid,$entity));


        return self::DEFAULT_REPLY;

	}

	//========================微信事件处理方法

	/**
	 * 自定义菜单事件
	 *  ToUserName	开发者微信号
	 FromUserName	发送方帐号（一个OpenID）
	 CreateTime	消息创建时间 （整型）
	 MsgType	消息类型，event
	 Event	事件类型，CLICK
	 EventKey	事件KEY值，与自定义菜单接口中KEY值对应
	 */
	private function menuClick() {
		//点击菜单拉取消息时的事件推送
		$this->data['Content'] = $this->data['EventKey'];
		
		addWeixinLog($this->data['Content'],"menuClick");
		if(empty($return)){
			
		}
		return $return;

	}

	/**
	 * 自定义菜单事件
	 *  ToUserName	开发者微信号
	 FromUserName	发送方帐号（一个OpenID）
	 CreateTime	消息创建时间 （整型）
	 MsgType	消息类型，event
	 Event	事件类型，VIEW
	 EventKey	事件KEY值，设置的跳转URL
	 */
	private function menuView() {
		//点击菜单跳转链接时的事件推送
		//TODO：统计自定义菜单的点击次数
		return "";
	}

	/**
	 * 处理二维码扫描事件
	 */
	private function qrsceneProcess($eventKey) {
		$addWxuserflag = false; 
		//$eventKey
		//TODO: 处理二维码扫描事件
		//TODO: 转到插件中处理
		//如果是未关注用户扫码关注



		if(strlen($eventKey)< 30){
			$eventKey = intval(str_replace('UID_', '', $eventKey));
			if (is_int($eventKey) && $eventKey > 0) {
				$addWxuserflag = true;
				$map['invite_code'] = $eventKey;
				$invite_wxid = apiCall(WxuserApi::GET_INFO,array($map));
				if($invite_wxid['status']) {
					$addWxuserflag = true;
					$wxid          = $invite_wxid['info']['id'];
//$add_test=apiCall(WxreplyTextApi::ADD,array(['keyword'=>33,'content'=>$wxid]));//推送测试

					$this->addWxuser($eventKey,'',$wxid);
				}
			}

		}
		//如果是已关注用户，再次扫码
        //if(is_numeric($eventKey)){
		if(strlen($eventKey)>30){

            $map['invite_code'] = $eventKey;
            $invite_wxid = apiCall(WxuserApi::GET_INFO,array($map));
            if($invite_wxid['status']){

                $addWxuserflag = true;
                $wxid = $invite_wxid['info']['id'];
                $this->addWxuser('0','0',$wxid);
            }
            addWeixinLog("用户uid= " . $eventKey, "【微信消息】");
        }
		
		if(!$addWxuserflag){

			$this->addWxuser();
		}
		echo json_encode('',JSON_UNESCAPED_UNICODE);
		return "";

	}

	/**
	 * 关注事件
	 */
	private function subscribe() {
		addWeixinLog($this->data, "[subscribe]");
		if (isset($this -> data['EventKey']) && !empty($this->data['EventKey'])) {
			//TODO: 处理用户通过推广二维码进行关注的事件
			$eventKey = $this -> data['EventKey'];
			addWeixinLog("[subscribe]  EventKey = " . $eventKey, "关注消息带场景KEY");

//			$ev_ken=str_replace("qrscene_", "", $eventKey);//推送测试
//$add_test=apiCall(WxreplyTextApi::ADD,array(['keyword'=>99]));//推送测试

			$this -> qrsceneProcess(str_replace("qrscene_", "", $eventKey));


		} else {
			//扫描公众号二维码进行关注
			$this->addWxuser();
		}
		
		$ss_keyword = C("SS_KEYWORD");
		addWeixinLog("[SS_KEYWORD]".$ss_keyword, "首次关注回复关键词");
		if(!empty($ss_keyword)){
			return $this->textProcess($ss_keyword);//处理关键词
		}
		addWeixinLog("[subscribe]".$this -> getOpenID(), "关注消息");
		return "";
	}

	/**
	 * 取消关注
	 */
	private function unsubscribe() {
		//TODO: 取消关注
		//==更新粉丝为未关注
		$wxuser = array('subscribed' => 0);
		$result = apiCall(WxuserApi::SAVE, array( array('openid' => $this -> getOpenID(),'wxaccount'=>$this->wxaccount['id']), $wxuser));
		if (!$result['status']) {
			LogRecord($result['info'], __FILE__);
		}
		addWeixinLog("[unsubscribe]" . $this -> getOpenID(), "取消关注消息");
		return "";
	}

	/**
	 * 用户已二维码扫描关注事件
	 */
	private function qrsceneScan() {
		$eventKey = $this -> data['EventKey'];
		addWeixinLog("[qrsceneScan]" . $eventKey, "微信消息");
		return $this -> qrsceneProcess($eventKey);
	}

	//======================================其它辅助方法
	
	/**
	 * 插入粉丝信息
	 */
	private function addWxuser($referrer = 0,$cnt=0,$wxid=0) {

echo "";

		addWeixinLog($wxid,"事件进入");
		$openid = $this -> getOpenID();
		$userinfo = $this -> wxapi -> getBaseUserInfo($openid);

		if(!$userinfo['status']){
			LogRecord($userinfo['info'], __FILE__.__LINE__);
			if($cnt > 1){
				return ;
			}
			$this->addWxuser($referrer,$cnt+1,$wxid);
		}

		$userinfo = $userinfo['info'];
		
		$map = array('openid' => $this -> getOpenID(), 'wxaccount_id' => $this->wxaccount['id'] );

		$result = apiCall(WxuserApi::GET_INFO, array($map));//当前粉丝的信息是否已经存在记录

		$wxuser = array();
		$wxuser['wxaccount_id'] = intval($this->wxaccount['id']);
		$wxuser['openid'] = $openid;
		$wxuser['nickname'] = '';
		$wxuser['avatar'] = '';
		$wxuser['referrer'] = $referrer;
		$wxuser['sex'] = 0;
		$wxuser['province'] = '';
		$wxuser['country'] = '中国';
		$wxuser['city'] = "";
		$wxuser['subscribe_time'] = time();
		$wxuser['subscribed'] = 1;

		if (is_array($userinfo)) {
			$wxuser['nickname'] = $userinfo['nickname'];
			$wxuser['province'] = $userinfo['province'];
			$wxuser['country'] = $userinfo['country'];
			$wxuser['city'] = $userinfo['city'];
			$wxuser['sex'] = $userinfo['sex'];
			$wxuser['avatar'] = $userinfo['headimgurl'];
			$wxuser['subscribe_time'] = $userinfo['subscribe_time'];
			$wxuser['subscribed'] = 1;
		}

		//判断是否已记录
        addWeixinLog(is_array($result['info']),"1249");
		if (is_array($result['info'])) {
			//更新
			$result = apiCall(WxuserApi::SAVE, array($map, $wxuser));

		} else {
			addWeixinLog($wxuser['referrer'],"1247");			addWeixinLog($wxid,"1248");
			//新增
            $res = apiCall(WxuserApi::GET_INFO, array('','id desc'));
            if($res['status']){
                $wxuser['invite_code'] = $res['info']['id']+1;
                $result = apiCall(WxuserApi::ADD, array($wxuser));
            }

            //zjq这里添加一个新建的会员表的数据
            $rolemap['grade_list'] = array('GT',0);
            $roleres = apiCall(RoleApi::GET_INFO,array($rolemap,'grade_list asc'));//调出最低级的VIP等级给用户进行赋值

            if($roleres['status']){

                $memberadd = array(
                    'wx_id' => $result['info'],
                    'name' => $userinfo['nickname'],
                    'role_grade' => $roleres['info']['id'],
                    'store_money' => '0',
                    'spend_money' => '0',
                    'sm_update_time'=>time(),
                    'year_recommend_num'=>'0',
                    'yr_update_time'=>time(),
                    's_gold_mumber' => $roleres['info']['become_gold'],
                    's_platina_mumber' => $roleres['info']['become_platina'],
                    'vip_type' => '1',
                    'sham_share' => '0',
                    'real_shares' => '0',
                    'points' => '0',
                    'invitation_uid' => $wxid,
                );

                $newmemberres = apiCall(NewmemberApi::ADD,array($memberadd));

                if($newmemberres['status']){
                    $flag = false;
                    $error = $newmemberres['info'];
                }
                if($wxid!== 0){
                    $invit_map['wx_id'] = $wxid;
                    $updateres = apiCall(NewmemberApi::GET_INFO,array($invit_map));
                    if($updateres['status']){
                        $invit_num = $updateres['info']['year_recommend_num'] +1;
                        $isave['year_recommend_num'] = $invit_num;
                        $sres = apiCall(NewmemberApi::SAVE,array($invit_map,$isave));
                        if($sres['status']){
                            $flag = false;
                            $error = $newmemberres['info'];
                        }
                    }
                }

            }
exit;

		}
        if($result['status']){
            $flag = false;
            $error = $result['info'];
        }

        return array('status'=>$flag,'info'=>$error);

		LogRecord($result['info'], __FILE__.__LINE__);

	}
	
	/**
	 * 检测推荐人是否合法
	 * @param $referrer 推荐人
	 * @param $id 当前用户ID
	 */
	private function checkReferrer($curID,$family){
		if($curID == 0){return true;}
		if($curID == $family['wxuserid']){
			//不能自己推荐自己
			return false;
		}
		
		return true;		
	}


	/*
	 * 获取openid
	 */
	private function getOpenID() {
		return $this -> data['FromUserName'];
	}

}
