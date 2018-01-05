<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2016-12-22 14:35:14
 * Description : [微信APP支付service]
 */

namespace app\src\wxpay\service;

/**
 * [微信APP支付service]
 *
 * [微信APP支付service]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\wxpay\service
 * @example
 */
class WxpayService {
    public $config = null;
    public function __construct($is_worker=false){
      $this->config = $this->getConfig($is_worker);
    }

    public function getPrePayOrder($body, $out_trade_no, $total_fee){
      $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
      $notify_url = $this->config["notify_url"];

      $nonce_str = $this->getRandChar(32);

      $data["appid"]            = $this->config["appid"];
      $data["body"]             = $body;
      $data["mch_id"]           = $this->config['mch_id'];
      $data["nonce_str"]        = $nonce_str;
      $data["notify_url"]       = $notify_url;
      $data["out_trade_no"]     = $out_trade_no;
      $data["spbill_create_ip"] = $this->get_client_ip();
      $data["total_fee"]        = $total_fee;
      $data["trade_type"]       = "APP";
      $start = date('YmdHis');
      $data["time_start"]       = $start;
      $data["time_expire"]      = $start+900;
      $s = $this->getSign($data);
      $data["sign"] = $s;
      //生成pay_code
      // $pay_code = $this->getPayCode(1);
      // return $data;
// var_dump($data);die();
      $xml = $this->arrayToXml($data);
// echo $xml;//die();
      $response = $this->postXmlCurl($xml, $url);
// echo $response;die();

      //将微信返回的结果xml转成数组
      return $this->xmlstr_to_array($response);
    }

    //组装已签名的支付数据返回给客户端
    public function reSign($prepayId){
      $data = [];
      $data["appid"]        = $this->config["appid"];
      $data["noncestr"]     = $this->getRandChar(32);;
      $data["package"]      = "Sign=WXPay";
      $data["partnerid"]    = $this->config['mch_id'];
      $data["prepayid"]     = $prepayId;
      $data["timestamp"]    = time();
      $s                    = $this->getSign($data);
      $data["sign"]         = $s;
      $data["packageValue"] = "Sign=WXPay";//安卓key-fix
      unset($s);
      return $data;
    }
    /*
     *生成签名
     */
    protected function getSign($Obj)
    {
        foreach ($Obj as $k => $v)
        {
            $Parameters[strtolower($k)] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        // echo "【string】 =".$String."</br>";
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".$this->config['api_key'];
        // echo "<textarea style='width: 50%; height: 150px;'>$String</textarea> <br />";
        //签名步骤三：MD5加密
        $result_ = strtoupper(md5($String));
        return $result_;
    }

    //获取指定长度的随机字符串
    protected function getRandChar($length){
       $str = null;
       $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
       $max = strlen($strPol)-1;

       for($i=0;$i<$length;$i++){
        $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
       }

       return $str;
    }

    //数组转xml
    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
             if (is_numeric($val))
             {
                $xml.="<".$key.">".$val."</".$key.">";
             }
             else
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }

    //post https请求，CURLOPT_POSTFIELDS xml格式
    protected function postXmlCurl($xml,$url,$second=30)
    {
        //初始化curl
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data)
        {
            curl_close($ch);
            return $data;
        }
        else
        {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
    }

    /*
        获取当前服务器的IP
    */
    protected function get_client_ip()
    {
        if ($_SERVER['REMOTE_ADDR']) {
        $cip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv("REMOTE_ADDR")) {
        $cip = getenv("REMOTE_ADDR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
        $cip = getenv("HTTP_CLIENT_IP");
        } else {
        $cip = "unknown";
        }
        return $cip;
    }

    //将数组转成uri字符串
    protected function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
               $v = urlencode($v);
            }
            $buff .= strtolower($k) . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    public function checkSign($data){
      $sign = $data['sign'];
      // echo $sign.'--';
      // echo $this->getSign($data).'--';
      unset($data['sign']);
      // echo $this->getSign($data).'--';
      return $sign == $this->getSign($data);
    }
    /**
     *xml转成数组
    */
    public function xmlstr_to_array($xmlstr) {
      $doc = new \DOMDocument();
      $doc->loadXML($xmlstr);
      return $this->domnode_to_array($doc->documentElement);
    }
    protected function domnode_to_array($node) {
      $output = array();
      switch ($node->nodeType) {
       case XML_CDATA_SECTION_NODE:
       case XML_TEXT_NODE:
        $output = trim($node->textContent);
       break;
       case XML_ELEMENT_NODE:
        for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
         $child = $node->childNodes->item($i);
         $v = $this->domnode_to_array($child);
         if(isset($child->tagName)) {
           $t = $child->tagName;
           if(!isset($output[$t])) {
            $output[$t] = array();
           }
           $output[$t][] = $v;
         }
         elseif($v) {
          $output = (string) $v;
         }
        }
        if(is_array($output)) {
         if($node->attributes->length) {
          $a = array();
          foreach($node->attributes as $attrName => $attrNode) {
           $a[$attrName] = (string) $attrNode->value;
          }
          $output['@attributes'] = $a;
         }
         foreach ($output as $t => $v) {
          if(is_array($v) && count($v)==1 && $t!='@attributes') {
           $output[$t] = $v[0];
          }
         }
        }
       break;
      }
      return $output;
    }

    // 配置
    private function getConfig($is_worker=false){
      if($is_worker){ //技工端配置
        return [
          "appid"      =>"wx100244ee053769a0",//获取
          "mch_id"     =>"1432323202",//获取
          "api_key"    =>"GoxSq1rkuZPyHzpxxZNtac0O5Hb8Vur0",//远程配置
          "notify_url" =>"http://api.ryzcgf.com/public/index.php/payback/wxpay/worker/1",//本地配置
        ];
      }else{ //司机端配置
        return [
          "appid"      =>"wx28fe69f36a61056b",//获取
          "mch_id"     =>"1355801702",//获取
          "api_key"    =>"7Ls1IrMf3sUaiUObyo9avYu1erpbzTwo",//远程配置
          "notify_url" =>"http://api.ryzcgf.com/public/index.php/payback/wxpay",//本地配置
        ];
      }
    }
}