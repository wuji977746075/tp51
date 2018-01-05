<?php
/**
 * 易微听 友盟推送 广播&别名单播(50-)&文件播(50+)
 * User  : hebidu
 * Editor: rainbow
 * Date  : 2016-12-30 14:31:21
 */

namespace app\src\extend\umeng;

use app\src\user\logic\MemberLogic;

require_once('UmengPushApi.php');

class BoyePushApi {
    const ALIAS_TYPE = 'ewelisten';
    //android
    protected $and_mode = true;
    protected $and_appkey = '593f9f968f4a9d2c7f001598';
    protected $and_secret = 'rdnfitiu00djgd9ttbygbjr8lwli8bev';

    //ios
    protected $ios_mode = false;
    protected $ios_appkey = '58c8a04fb27b0a1255000a9f';
    protected $ios_secret = 'gugehwjkhhby1i3edcrivjtprq3vj6xp';

    /**
     * 别名 - 单播(1)/多播(50-)/文件博(50+)
     * @param $uid   string/int uids(逗号分隔<=50)
     * @param $param array      消息体
     * @param $param after_open 推送信息
     * @param $param client     客户端[worker,driver,其他任意字符]
     */
    public function send($uid='',$param,$after_open=['type'=>'go_app','param'=>'','extra'=>''],$client=""){

        //检查 uid
        $file = false;
        if(is_numeric($uid)){ //单用户
            if(!(new MemberLogic())->isExistUid($uid)) return returnErr(Linvalid('uid'));
        }elseif(is_string($uid)){ //多用户
            $uids = array_unique(explode(',', $uid));
            $size = count($uids);
            if($size>50) $file = true;
            $uids = implode("\n", $uids);//一定要 "\n"
        }
        if($file){
            //获取file_id
            $r = new \UmengPushApi($this->and_appkey,$this->and_secret);
            $file_id = $r->getFileId($uids);
        }
        //检查消息主题
        $r = $this->checkMsgBody($param);
        if(!$r['status']) return $r;

        //发送安卓消息
        $Android = new \UmengPushApi($this->and_appkey,$this->and_secret);

        $entity = [
            'alias_type'      =>self::ALIAS_TYPE,
            'ticker'          =>$param['ticker'],
            'title'           =>$param['title'],
            'text'            =>$param['text'],
            'after_open'      =>$after_open['type'],
            'production_mode' =>$this->and_mode,
        ];
        if($file) $entity['file_id'] = $file_id;
        else $entity['alias'] = $uid;

        //自定义打开指定页面
        if($after_open['type'] == 'go_url'){
            $entity['url'] = $after_open['param'];
        }elseif($after_open['type'] == 'go_activity'){
            $entity['activity'] = $after_open['param'];
        }
        //自定义参数
        if(!empty($after_open['extra'])){
            if(isset($after_open['extra']['sound'])){
                //自定义sound
                $entity['sound'] = $after_open['extra']['sound'];
            }
            $entity['payload']['extra'] = $after_open['extra'];
        }
         $result_a = $Android->sendAndroidCustomizedcast($entity);


        //发送IOS消息
        $IOS = new \UmengPushApi($this->ios_appkey,$this->ios_secret);

        $entity = [
            'alias_type'      =>self::ALIAS_TYPE,
            'alert'           =>$param['alert'],
            'badge'           =>0, //角标
            'sound'           =>'default',
            'production_mode' =>$this->ios_mode,
        ];
        if($file) $entity['file_id'] = $file_id;
        else $entity['alias'] = $uid;
        $entity_d['production_mode'] = $this->ios_mode;

        //自定义参数
        if(!empty($after_open['extra'])){
            if(isset($after_open['extra']['sound'])){
                //自定义sound
                $entity['sound'] = $after_open['extra']['sound'].'.caf';
                // unset($after_open['extra']['sound']);
            }
            $entity['payload']['extra'] = $after_open['extra'];
        }
        //自定义打开指定页面
        if($after_open['type'] == 'go_activity'){
            $entity['payload']['after_open'] = $after_open['param'];
        }
        $result_i = $IOS->sendIOSCustomizedcast($entity);

        if(!$result_a['status'] && !$result_i['status']){
            $err = 'and:'.$result_a['info'].';';
            $err .= 'ios:'.$result_i['info'].';';
            return returnErr($err);
        }else{
            $err = '';
            if(!$result_a['status']) $err = 'and:'.$result_a['info'].';';
            if(!$result_i['status']) $err = 'ios:'.$result_i['info'].';';
            return returnSuc($err ? $err : L('success'));
        }

    }

    /**
     * 广播
     * @param $param array
     */
    public function sendAll($param,$after_open=['type'=>'go_app','param'=>'','extra'=>'','sound'=>''],$client=""){

        // 检查消息主题
        $r = $this->checkMsgBody($param);
        if(!$r['status']) return $r;

        //发送安卓设备广播消息
        $Android = new \UmengPushApi($this->and_appkey,$this->and_secret);

        $entity = [
            'ticker'          =>$param['ticker'],
            'title'           =>$param['title'],
            'text'            =>$param['text'],
            'after_open'      =>$after_open['type'],
            'production_mode' =>$this->and_mode,
        ];

        //自定义打开指定页面
        if($after_open['type'] == 'go_url'){
            $entity['url'] = $after_open['param'];

        }
        if($after_open['type'] == 'go_activity'){
            $entity['activity'] = $after_open['param'];

        }
        if(!empty($after_open['extra'])){
            //Android自定义sound
            if(isset($after_open['extra']['sound'])){
                $entity['sound'] = $after_open['sound'];
            }
            $entity['payload']['extra']['after_open'] = $after_open['extra'];

        }
        $result_a = $Android->sendAndroidBroadcast($entity);

        //发送IOS设备广播消息
        $IOS = new \UmengPushApi($this->ios_appkey,$this->ios_secret);
        $entity = [
            'alert'           =>$param['alert'],
            'badge'           =>0,
            'sound'           =>'default',
            'production_mode' =>$this->ios_mode,
        ];

        //自定义打开指定页面
        if($after_open['type'] == 'go_activity'){
            $entity['payload']['after_open'] = $after_open['param'];

        }
        if(!empty($after_open['extra'])){
            if(isset($after_open['extra']['sound'])){
                //自定义sound
                $entity['sound'] = $after_open['extra']['sound'].'.caf';
                // unset($after_open['extra']['sound']);
            }
            $entity['payload']['extra']['after_open'] = $after_open['extra'];

        }
        $result_i = $IOS->sendIOSBroadcast($entity);

        if(!$result_a['status'] && !$result_i['status']){
            $err = 'and:'.$result_a['info'].';';
            $err .= 'ios:'.$result_i['info'].';';
            return returnErr($err);
        }else{
            $err = '';
            if(!$result_a['status']) $err = 'and:'.$result_a['info'].';';
            if(!$result_i['status']) $err = 'ios:'.$result_i['info'].';';
            return returnSuc($err ? $err : L('success'));
        }

    }

    /**
     * 检查消息体
     * @Author
     * @DateTime 2016-12-30T14:08:36+0800
     * @return   [type]                   [description]
     */
    private function checkMsgBody(array $param){
        if(empty($param)){
            return returnErr(Llack('param'));
        }
        if(empty($param['ticker'])){
            return returnErr(Llack("param['ticker']"));
        }
        if(empty($param['title'])){
            return returnErr(Llack("param['title']"));
        }
        if(empty($param['text'])){
            return returnErr(Llack("param['text']"));
        }
        if(empty($param['alert'])){
            return returnErr(Llack("param['alert']"));
        }
        return returnSuc(L('success'));
    }
}