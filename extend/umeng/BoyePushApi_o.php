<?php
/**
 * 虎头奔 友盟推送 广播&别名单播
 * User  : hebidu
 * Editor: rainbow
 * Date  : 2016-12-30 14:31:21
 */

namespace app\src\extend\umeng;

use app\src\user\logic\MemberLogic;

require_once('UmengPushApi.php');

class BoyePushApi {
    const ALIAS_TYPE = 'htb';
    //android
    //hutouben_android_worker
    protected $and_worker_mode = true;
    protected $and_worker_appkey = '5864e0d4677baa1cbf001210';
    protected $and_worker_secret = 'p1o0w93yckip4f6uwnrl7yvsslskclwi';
    //hutouben_android_driver
    protected $and_driver_mode = true;
    protected $and_driver_appkey = '5864d39daed17906b7000eed';
    protected $and_driver_secret = 'cz6pfxpv1bm5ii9tg4xbffgceknwc9ns';

    //ios
    //hutouben_ios_worker
    protected $ios_worker_mode = true;
    protected $ios_worker_appkey = '5858a2e507fe652d7c0007f9';
    protected $ios_worker_secret = 'warlgd8kfx1uh7wc2rhxhfmhb4ruudhp';
    //hutouben_ios_driver
    protected $ios_driver_mode = true;
    protected $ios_driver_appkey = '58589d95310c9307d7000f69';
    protected $ios_driver_secret = 'llswfwcvyimyo9to4qfo5auwzdkkiwzz';


    /**
     * 别名 - 单播(1)/多播(50-)/文件博(50+)
     * @param $uid   string/int uids(逗号分隔<=50)
     * @param $param array      消息体
     * @param $param after_open 推送信息
     * @param $param client     客户端[worker,driver,其他任意字符]
     */
    public function send($uid='',$param,$after_open=['type'=>'go_app','param'=>'','extra'=>''],$client=""){
        $worker = true;
        $driver = true;
        if($client == 'worker') $driver = false;
        if($client == 'driver') $worker = false;

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
            $r = new \UmengPushApi($this->and_worker_appkey,$this->and_worker_secret);
            $file_id = $r->getFileId($uids);
        }
        //检查消息主题
        $r = $this->checkMsgBody($param);
        if(!$r['status']) return $r;

        //发送安卓消息
        if($worker) $Android_w = new \UmengPushApi($this->and_worker_appkey,$this->and_worker_secret);
        if($driver) $Android_d = new \UmengPushApi($this->and_driver_appkey,$this->and_driver_secret);
        $entity_w = [
            'alias_type'      =>self::ALIAS_TYPE,
            'ticker'          =>$param['ticker'],
            'title'           =>$param['title'],
            'text'            =>$param['text'],
            'after_open'      =>$after_open['type'],
            'production_mode' =>$this->and_worker_mode,
        ];
        if($file) $entity_w['file_id'] = $file_id;
        else $entity_w['alias'] = $uid;
        $entity_d = $entity_w;
        $entity_d['production_mode'] = $this->and_driver_mode;

        //自定义打开指定页面
        if($after_open['type'] == 'go_url'){
            $entity_w['url'] = $after_open['param'];
            $entity_d['url'] = $after_open['param'];
        }elseif($after_open['type'] == 'go_activity'){
            $entity_w['activity'] = $after_open['param'];
            $entity_d['activity'] = $after_open['param'];
        }
        //自定义参数
        if(!empty($after_open['extra'])){
            if(isset($after_open['extra']['sound'])){
                //自定义sound
                $entity_w['sound'] = $after_open['extra']['sound'];
                $entity_d['sound'] = $after_open['extra']['sound'];
                // unset($after_open['extra']['sound']);
            }
            $entity_w['payload']['extra'] = $after_open['extra'];
            $entity_d['payload']['extra'] = $after_open['extra'];
        }
        if($worker) $result_a_w = $Android_w->sendAndroidCustomizedcast($entity_w);
        if($driver) $result_a_d = $Android_d->sendAndroidCustomizedcast($entity_d);

        //发送IOS消息
        if($worker) $IOS_w = new \UmengPushApi($this->ios_worker_appkey,$this->ios_worker_secret);
        if($driver) $IOS_d = new \UmengPushApi($this->ios_driver_appkey,$this->ios_driver_secret);
        $entity_w = [
            'alias_type'      =>self::ALIAS_TYPE,
            'alert'           =>$param['alert'],
            'badge'           =>0, //角标
            'sound'           =>'default',
            'production_mode' =>$this->ios_worker_mode,
        ];
        if($file) $entity_w['file_id'] = $file_id;
        else $entity_w['alias'] = $uid;
        $entity_d = $entity_w;
        $entity_d['production_mode'] = $this->ios_driver_mode;

        //自定义参数
        if(!empty($after_open['extra'])){
            if(isset($after_open['extra']['sound'])){
                //自定义sound
                $entity_w['sound'] = $after_open['extra']['sound'].'.caf';
                $entity_d['sound'] = $after_open['extra']['sound'].'.caf';
                // unset($after_open['extra']['sound']);
            }
            $entity_w['payload']['extra'] = $after_open['extra'];
            $entity_d['payload']['extra'] = $after_open['extra'];
        }
        //自定义打开指定页面
        if($after_open['type'] == 'go_activity'){
            $entity_w['payload']['after_open'] = $after_open['param'];
            $entity_d['payload']['after_open'] = $after_open['param'];
        }
        if($worker) $result_i_w = $IOS_w->sendIOSCustomizedcast($entity_w);
        if($driver) $result_i_d = $IOS_d->sendIOSCustomizedcast($entity_d);
        if(
            ($worker && ($result_a_w['status'] || $result_i_w['status'])) ||
            ($driver && ($result_a_d['status'] || $result_i_d['status']))
        ){
            return returnSuc(L('success'));
        }else{
            $err = '';
            if($worker && !$result_a_w['status']) $err .= 'and_worker:'.$result_a_w['info'].';';
            if($worker && !$result_i_w['status']) $err .= 'ios_worker:'.$result_i_w['info'].';';
            if($driver && !$result_a_d['status']) $err .= 'and_driver:'.$result_a_d['info'].';';
            if($driver && !$result_i_d['status']) $err .= 'ios_dirver:'.$result_i_d['info'].';';
            return returnErr($err);
        }

    }

    /**
     * 广播
     * @param $param array
     */
    public function sendAll($param,$after_open=['type'=>'go_app','param'=>'','extra'=>'','sound'=>''],$client=""){

        $worker = true;
        $driver = true;
        if($client == 'worker') $driver = false;
        if($client == 'driver') $worker = false;

        // 检查消息主题
        $r = $this->checkMsgBody($param);
        if(!$r['status']) return $r;

        //发送安卓设备广播消息
        if($worker) $Android_w = new \UmengPushApi($this->and_worker_appkey,$this->and_worker_secret);
        if($driver) $Android_d = new \UmengPushApi($this->and_driver_appkey,$this->and_driver_secret);

        $entity_w = [
            'ticker'          =>$param['ticker'],
            'title'           =>$param['title'],
            'text'            =>$param['text'],
            'after_open'      =>$after_open['type'],
            'production_mode' =>$this->and_worker_mode,
        ];

        $entity_d = $entity_w;$entity_d['production_mode'] = $this->and_driver_mode;
        //自定义打开指定页面
        if($after_open['type'] == 'go_url'){
            $entity_w['url'] = $after_open['param'];
            $entity_d['url'] = $after_open['param'];
        }
        if($after_open['type'] == 'go_activity'){
            $entity_w['activity'] = $after_open['param'];
            $entity_d['activity'] = $after_open['param'];
        }
        if(!empty($after_open['extra'])){
            //Android自定义sound
            if(isset($after_open['extra']['sound'])){
                $entity_w['sound'] = $after_open['sound'];
                $entity_d['sound'] = $after_open['sound'];
            }
            $entity_w['payload']['extra']['after_open'] = $after_open['extra'];
            $entity_d['payload']['extra']['after_open'] = $after_open['extra'];
        }
        if($worker) $result_a_w = $Android_w->sendAndroidBroadcast($entity_w);
        if($driver) $result_a_d = $Android_d->sendAndroidBroadcast($entity_d);

        //发送IOS设备广播消息
        if($worker) $IOS_w = new \UmengPushApi($this->ios_worker_appkey,$this->ios_worker_secret);
        if($driver) $IOS_d = new \UmengPushApi($this->ios_driver_appkey,$this->ios_driver_secret);

        $entity_w = [
            'alert'           =>$param['alert'],
            'badge'           =>0,
            'sound'           =>'default',
            'production_mode' =>$this->ios_worker_mode,
        ];
        $entity_d = $entity_w;$entity_d['production_mode'] = $this->ios_driver_mode;

        //自定义打开指定页面
        if($after_open['type'] == 'go_activity'){
            $entity_w['payload']['after_open'] = $after_open['param'];
            $entity_d['payload']['after_open'] = $after_open['param'];
        }
        if(!empty($after_open['extra'])){
            if(isset($after_open['extra']['sound'])){
                //自定义sound
                $entity_w['sound'] = $after_open['extra']['sound'].'.caf';
                $entity_d['sound'] = $after_open['extra']['sound'].'.caf';
                // unset($after_open['extra']['sound']);
            }
            $entity_w['payload']['extra']['after_open'] = $after_open['extra'];
            $entity_d['payload']['extra']['after_open'] = $after_open['extra'];
        }
        if($worker) $result_i_w = $IOS_w->sendIOSBroadcast($entity_w);
        if($driver) $result_i_d = $IOS_d->sendIOSBroadcast($entity_d);

        if(
            ($worker && (!$result_a_w['status'] || !$result_i_w['status'])) ||
            ($driver && (!$result_a_d['status'] || !$result_i_d['status']))
        ){
            $err = '';
            if($worker && !$result_a_w['status']) $err .= 'and_worker:'.$result_a_w['info'].';';
            if($worker && !$result_i_w['status']) $err .= 'ios_worker:'.$result_i_w['info'].';';
            if($driver && !$result_a_d['status']) $err .= 'and_driver:'.$result_a_d['info'].';';
            if($driver && !$result_i_d['status']) $err .= 'ios_dirver:'.$result_i_d['info'].';';
            return returnErr($err);
        }else{
            return returnSuc(L('success'));
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