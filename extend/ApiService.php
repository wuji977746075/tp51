<?php
use \CryptUtils;
use \ErrorCode as EC;
/**
 * curl调用api接口.
 * 根空间 user 根空间 需\,
 * 非根空间 user 根空间 无需\,
 * Class ApiService
 */
class ApiService {

    private $apiUrl;//接口地址，根地址
    private $client_id;
    private $client_secret;
    private $notify_id;//resp : 返回的请求id,一般与请求的一致
    private $time;     //resp : 返回时的时间
    //封装为pc
    function __construct($id='',$secret=''){
        $this->apiUrl        = config('api_url').'/';
        $this->client_id     = $id ? $id : CLIENT_ID;
        $this->client_secret = $secret ? $secret : CLIENT_SECRET;
    }

    /**
     * @param $url Orders/add 接口方法
     * @param $data
     * @param bool $is_debug
     * @param int  $client_type 1:front-app;2=admin-app
     * @return array
     * @throws \Exception
     */
    public function callRemote($url,$data,$is_debug=false){
        if(!isset($data['type']) || empty($data['type'])){
            return $this->ret(EC::Lack_Para,Llack('type'));
        }
        $type = $data['type'];
        unset($data['type']);
        if(!isset($data['api_ver']) || empty($data['api_ver'])){
         return $this->ret(EC::Lack_Para,Llack('api_ver'));
        }
        $api_ver = $data['api_ver'];
        unset($data['api_ver']);
        if(!isset($data['notify_id']) || empty($data['notify_id'])){
            $notify_id = time();
        }else{
            $notify_id = $data['notify_id'];
            unset($data['notify_id']);
        }
        if(!isset($data['alg']) || empty($data['alg'])){
            $alg = 'md5';
        }else{
            unset($data['alg']);
            $alg = $data['alg'];
        }
        $encrypt_data = CryptUtils::encrypt($data);
        $param = [
            'client_secret' =>$this->client_secret,
            'api_ver'       =>$api_ver,
            'notify_id'     =>$notify_id, //请求id
            'time'          =>strval(microtime(true)), //发起请求的时间
            'data'          =>$encrypt_data,
            'type'          =>$type,
            'app_version'   =>1.0,
            'lang'          =>'zh_cn',
            'app_type'      =>'test',
            'alg'           =>$alg,
        ];
        $param['sign']      = CryptUtils::sign($param);
        $param['client_id'] = $this->client_id;
        $encrypt_data = DesCrypt::encode(json_encode($param),$this->client_secret);
        $param = [
            'itboye'    =>base64_encode($encrypt_data),
            'client_id' =>$this->client_id,
        ];
        $r = $this->curlPost($url,$param);
        // dump($r);die();


        if($is_debug){
            multi_dump($r,'json',lang('encrypt-data'));
        }
        // 返回需至少携带 code,data,msg,notify_id,time
        // -1 的话, data = 原始返回数据
        if(!isset($r['code']))
            return $this->ret(-1,Llack('code'),$r);
        if(!isset($r['data']))
            return $this->ret(-1,Llack('data'),$r);
        if(!isset($r['msg']))
            return $this->ret(-1,Llack('msg'),$r);
        if(!isset($r['notify_id']))
            return $this->ret(-1,Llack('notify_id'),$r);
        if(!isset($r['time']))
            return $this->ret(-1,Llack('time'),$r);
        $code      = (int) $r['code'];
        $this->notify_id = (int) $r['notify_id']; // 请求id
        $this->time      = (int) $r['time']; // api返回时间
        // curl请求成功 - 不代表api执行结果
        if($code == 0){ //  业务成功
            // $r : 额外携带 data(业务成功数据加密字符串),type,sign
            if(isset($r['sign'])){
                // 添加secret 验证
                $r['client_secret'] = $this->client_secret;
                // 解密 返回的data
                $decrypt_data = CryptUtils::decrypt($r['data']);
                if(!CryptUtils::verify_sign($r['sign'],$r)){
                    return $this->ret(-1,'返回验签失败',$r);
                }else{ // api return [code,msg,data]
                    return $this->ret($r['code'],$r['msg'],$decrypt_data);
                }
            }else{
                return $this->ret(-1,Llack('sign'),$info);
            }
        }elseif($code > 0){// >0 : 错误 未加密
            return $this->ret($r['code'],$r['msg'],$r['data']);
        }
        // 异常
        return $this->ret(-1,'接口返回异常',$r);
    }

    private function ret($code=0,$msg='',$data=[]){
        $code = empty($code) ? 0 : $code;
        $msg  = empty($msg) ? 'ok' : $msg;
        return ['code'=>$code,'msg'=>$msg,'data'=>$data,'notify_id'=>$this->notify_id,'time'=>$this->time];
    }

    public function throws($msg='ok',$code=0){
        throw new \Exception($msg,$code);
    }
    /**
     *
     * @param $url Orders/add
     * @param $data  array
     * @return array
     */
    protected function curlPost($url, $data) {

        $url = $this->apiUrl.$url;
        $url = rtrim($url,"/");
        //对data进行加密

        $ch     = curl_init();
        $header = ['Accept-Charset'=>"utf-8"];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.64 Safari/537.36'); //chrome46/mac
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $errorno = curl_errno($ch);

        if($errorno){
            $this->throws('curl exception : '.$errorno,Ec::Exception);
        }else{
            $js = json_decode($tmpInfo,true);
            is_null($js) && $js = "$tmpInfo";
            return $js;
        }
    }

}