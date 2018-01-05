<?php
/**
 * 周舟 hzboye010@163.com
 * addby sublime snippets
 */

namespace app\index\domain;

use src\base\helper\ConfigHelper;
use src\base\utils\CacheUtils;
use src\config\ConfigLogic;

class ConfigDomain extends BaseDomain{

    //系统配置
    public function app(){
      $this->checkVersion();
      $r = (new ConfigLogic)->queryGroup(1,600);
      return  $r;
    }

    /**
     * 系统支持的app支付方式
     * @author hebidu <email:346551990@qq.com>
     */
    public function supportPayways(){
      $this->checkVersion();
      $config = ConfigHelper::app_support_payways();

      return $config;
    }

    public function version(){
      $this->checkVersion();
      $notes = "客户端" . $this->client_id . "，调用APP版本查询接口";
      addLog("Config/version", $_GET, $_POST, $notes);

      $ret = [];
      $from   = $this->_post("app_type","","缺少来源");
      $r = (new ConfigLogic)->query(['group'=>6]);
      $info = $this->simpleResult($r);
      if(strcasecmp($from, 'ios') == 0){
        $ret['version']      = $info['ios_version'];
        $ret['update_log']   = $info['ios_update_log'];
      }else if(strcasecmp($from, 'android') == 0){
        $ret['version']      = $info['android_version'];
        $ret['update_log']   = $info['android_update_log'];
      }
      $ret['download_url'] = $info['app_download_url'];
      return $ret;
    }

    private function simpleResult($r){
      $r = is_array($r)?$r:[];
      $simpleResult = [];
      foreach($r as $vo){
        $val  = $vo['value'];
        $name = strtolower($vo['name']);
        if($vo['type'] == 3){
          $array = preg_split('/[,;\r\n]+/', trim($val, ",;\r\n"));
          if (strpos($val, ':')) {
            $val = [];
            foreach ($array as $va) {
              list($k, $v) = explode(':', $va,2);
              $val[] = $v;
            }
          } else {
            $val = $array;
          }
        }else{
          $val = htmlspecialchars_decode($val);
        }
        $simpleResult[$name] = $val;
      }
      return $simpleResult;
    }
}