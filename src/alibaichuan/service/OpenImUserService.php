<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-09-14
 * Time: 16:13
 */

namespace src\alibaichuan\service;

/**
 * 用户同步
 * Class OpenIMUserService
 * @package src\alibaichuan\service
 */
class OpenImUserService extends BaseService
{


    // 推送消息 - todo:fix - 历史里没有
    public function push($uinfo,$id2,$msg=''){
        //taobao.openim.custmsg.push (推送自定义openim消息)
        if(empty($uinfo) || !isset($uinfo['baichuan_uid']) || !isset($uinfo['uid']) || !isset($uinfo['nickname'])) return ['status'=>false,'info'=>'uinfo为空'];
        if(empty($msg)) return ['status'=>false,'info'=>'msg为空'];
        $sum  = mb_substr($msg,0,6);
        // dump($sum);
        // shalt('sum',$sum);
        $id1  = $uinfo['baichuan_uid'];
        $nick = $uinfo['nickname'];
        // $id1 = getOpenIMUid($id1);
        $topClient = $this->getTopClient();
        $req = new \OpenimCustmsgPushRequest;
        $custmsg = new \CustMsg;
        $custmsg->from_user=$id1;
        $custmsg->to_appkey="0";
        $custmsg->to_users =$this->getToUsersStr($id2);
        $custmsg->summary  =$sum;
        //客户端最近消息里面显示的消息摘要
        $custmsg->data     =$msg;
        $aps       = ["alert"=>"ios apns push"];
        $apns_data = "apns推送的附带数据";
        $custmsg->aps      =json_encode($aps);
        // {"alert":"ios apns push"}
        // apns推送时，里面的aps结构体json字符串，aps.alert为必填字段。本字段为可选，若为空，则表示不进行apns推送。aps.size() + apns_param.size() < 200
        $custmsg->apns_param =$apns_data;
        // apns推送的附带数据apns推送的附带数据。
        // 客户端收到apns消息后，可以从apns结构体的"d"字段中取出该内容。aps.size() + apns_param.size() < 200
        $custmsg->invisible  ="0";
        //是否在最近会话列表里面展示
        $custmsg->from_nick  =$nick ? $nick:$id1;
        $custmsg->from_taobao="0";
        $req->setCustmsg(json_encode($custmsg));
        $resp = $topClient->execute($req);
        $parseResp = $this->parseResp($resp);
        if($parseResp['code'] === "0"){
            //请求成功
            if(isset($parseResp['extra']['msgid'])){
                $info = $parseResp['extra']['msgid'];
            }else{
                $info = "";
            }
            return ['status'=>true,'info'=>$info,'extra'=>$parseResp['extra']];
        }else{
            //请求错误
            return ['status'=>false,'info'=>$parseResp['info'],'extra'=>$parseResp];
        }
    }
    // 私聊 - todo:fix - 历史里没有
    public function send($id1,$id2,$msg=''){
        if(empty($msg)) return ['status'=>false,'info'=>'msg为空'];
        //taobao.openim.immsg.push (openim标准消息发送)
        $topClient = $this->getTopClient();
        $req = new \OpenimImmsgPushRequest;
        $immsg = new \ImMsg;//\StdClass();
        $immsg ->from_user  = getOpenIMUid($id1);
        $immsg ->to_users   = $this->getToUsersStr($id2);
        $immsg ->msg_type   = "0";
        $immsg ->context    = $msg;
        $immsg ->to_appkey  = "0";//$this->getAppKey();
        $immsg ->media_attr = "";
        $immsg ->from_taobao= "0";
        $s = json_encode($immsg);
        // shalt('s',$s);
        $req  ->setImmsg($s);
        $resp = $topClient->execute($req);
        $parseResp = $this->parseResp($resp);
        if($parseResp['code'] === "0"){
            //请求成功
            if(isset($parseResp['extra']['msgid'])){
                $info = $parseResp['extra']['msgid'];
            }else{
                $info = [];
            }
            return ['status'=>true,'info'=>$info,'extra'=>$parseResp['extra']];
        }else{
            //请求错误
            return ['status'=>false,'info'=>$parseResp['info'],'extra'=>$parseResp];
        }
    }
    private function getToUsersStr($ids){
        $str = "";
        $ids = explode(',',$ids);
        foreach ($ids as $v) {
            $str .=  (($str) ? ",":"")."\"".getOpenIMUid($v)."\"";
        }
        return "[".$str."]";
    }
    // 账户聊天历史
    public function history($id1,$id2,$beg,$end,$count=10,$next=''){
        //调用taobao.openim.relations.get

        $topClient = $this->getTopClient();
        $req = new \OpenimChatlogsGetRequest;
        $id1  = getOpenIMUid($id1);
        $id2  = getOpenIMUid($id2);
        $userinfo1  = new \Userinfos;
        $userinfo1 ->uid = $id1;
        $req -> setUser1(json_encode($userinfo1));
        $userinfo2  = new \Userinfos;
        $userinfo2 ->uid = $id2;
        $req -> setUser2(json_encode($userinfo2));
        $req -> setBegin(''.$beg);
        $req -> setEnd(''.$end);
        $req -> setCount(''.$count);
        if($next) $req->setNextKey(''.$next);
        $resp = $topClient->execute($req);
        $parseResp = $this->parseResp($resp);
        if($parseResp['code'] === "0"){
            //请求成功
            if(isset($parseResp['extra']['result'])){
                $info = $parseResp['extra']['result'];
            }else{
                $info = [];
            }
            return ['status'=>true,'info'=>$info,'extra'=>$parseResp['extra']];
        }else{
            //请求错误
            return ['status'=>false,'info'=>$parseResp['info'],'extra'=>$parseResp];
        }
    }
    // 账户近期聊天关系
    public function relation($id,$beg,$end){
        //调用taobao.openim.relations.get

        $topClient = $this->getTopClient();
        $req = new \OpenimRelationsGetRequest;
        $id  = getOpenIMUid($id);
        $userinfo  = new \Userinfos;
        $userinfo ->uid = $id;
        $req -> setUser(json_encode($userinfo));
        $req -> setBegDate(''.$beg);
        $req -> setEndDate(''.$end);
        $resp = $topClient->execute($req);
        $parseResp = $this->parseResp($resp);
        if($parseResp['code'] === "0"){
            //请求成功
            if(!empty($parseResp['extra']['users']) && isset($parseResp['extra']['users']['open_im_user'])){
                $info = $parseResp['extra']['users']['open_im_user'];
            }else{
                $info = [];
            }
            return ['status'=>true,'info'=>$info,'extra'=>$parseResp['extra']];
        }else{
            //请求错误
            return ['status'=>false,'info'=>$parseResp['info'],'extra'=>$parseResp];
        }
    }

    public function get($userids){
        //调用taobao.openim.users.get

        $topClient = $this->getTopClient();
        $req = new \OpenimUsersGetRequest;
        $req -> setUserids($userids);
        $r = $topClient->execute($req);
        $r = $this->checkErr($r);
        $r = $r ? $r['userinfos']['userinfos'] : $r;
        return $r;
    }

    /**
     * 与百川同步一个用户信息
     * @param $info  userid,password,nick,icon_url,name[实名]
     * @return userid|exception
     */
    public function add($info){

        $topClient = $this->getTopClient();
        $req       = new \OpenimUsersAddRequest;
        $userinfo  = new \Userinfos;
        //TODO: 填写用户信息
        $userinfo->userid   = $info['userid'];
        $userinfo->password = $info['password'];
        $userinfo->nick     = $info['nick'];
        $userinfo->icon_url = $info['icon_url'];

        $userinfo->mobile   = "";
        $userinfo->taobaoid = "";
        $userinfo->email    = "";
        $userinfo->remark   = "";
        $userinfo->extra    = "{}";
        $userinfo->career   = "";
        $userinfo->vip      = "{}";
        $userinfo->address  = "";
        $userinfo->name     = isset($info['name']) ? $info['name'] : '';
        $userinfo->age      = "18";
        $userinfo->gender   = "";
        $userinfo->wechat   = "";
        $userinfo->qq       = "";
        $userinfo->weibo    = "";

        $req->setUserinfos(json_encode($userinfo));
        $resp = $topClient->execute($req); // SimpleXMLElement
        $r = $this->checkErr($resp); // Array
        $uid_succ = isset($r['uid_succ']) ? $r['uid_succ'] : [];
        $uid_fail = isset($r['uid_fail']) ? $r['uid_fail'] : [];
        $fail_msg = isset($r['fail_msg']) ? $r['fail_msg'] : [];
        if(is_array($uid_succ) && count($uid_succ) == 1){//正确
            return $uid_succ['string'];
        }else{ // 错误
            $msg = $fail_msg['string'];
            if($msg == 'data exist') $msg = '该ID已被注册';
            throw new \Exception($msg);
        }
    }


    /**
     * 更新用户信息: 昵称,头像，密码
     * @see http://open.taobao.com/doc2/apiDetail.htm?apiId=24164
     * @param AliUserInfoUpdatePo $infoUpdatePo
     * @return array|\by\infrastructure\base\CallResult
     * @throws \Exception
     */
    public function update($userid,$nick='',$icon='',$name=''){

        $topClient =  $this->getTopClient();
        $req = new \OpenimUsersUpdateRequest;
        $userinfo = new \Userinfos;

        //TODO: 填写用户信息
        $userinfo->userid   = $userid;
        if($nick) $userinfo->nick     = $nick;
        if($icon) $userinfo->icon_url = $icon;
        if($name) $userinfo->name     = $name;

        $req->setUserinfos(json_encode($userinfo));
        $resp = $topClient->execute($req);
        $r = $this->checkErr($resp);

        $uid_succ = isset($r['uid_succ']) ? $r['uid_succ'] : [];
        $uid_fail = isset($r['uid_fail']) ? $r['uid_fail'] : [];
        $fail_msg = isset($r['fail_msg']) ? $r['fail_msg'] : [];
        if(is_array($uid_succ) && count($uid_succ) == 1){//正确
            return $uid_succ['string'];
        }else{ // 错误
            throw new \Exception($fail_msg['string']);
        }
    }
    /**
     * 删除用户关联
     * @param array $userids  '$open_id,..''
     * @param $info
     */
    public function del($userids= ''){

        $topClient =  $this->getTopClient();
        $req = new \OpenimUsersDeleteRequest;
        $req->setUserids($userids);
        $resp = $topClient->execute($req);
        $r = $this->checkErr($resp);
        return $r['result']['string'];

    }

}