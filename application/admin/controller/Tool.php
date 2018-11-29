<?php
/**
 * Author      : rainbow <977746075@qq.com>
 * DateTime    : 2018-11-29 13:49:26
 * Description : [Description]
 */

// namespace app\
// use

class Tool {

  /**
   * 创建验证码
   */
  public function random(){

      $session_id = AdminSessionHelper::getSessionId();
      $po = new SecurityCodePo();

      $po->setAcceptor($session_id);
      $po->setCodeCreateWay(CodeCreateWayEnum::ALPHA_AND_NUMBER);
      $po->setCodeLength(4);
      $po->setCodeType(SecurityCode::TYPE_FOR_LOGIN);

      $api = new SecurityCodeApi();
      $result = $api->create($po);
      if($result['status']){
          $verify = new DbVerify();
          $verify->entry($result['info']);
      }else{
          dump($result['info']) ;
          exit;
      }
  }

  public function itboye(){
        header('Content-type: application/javascript; charset=utf-8');
        $version = "1.0.1";
        $site_url = AdminConfigHelper::getValue('SITE_URL');
        //普通模式  0,兼容模式  3
        if(AdminConfigHelper::getValue('URL_MODEL') == 0 || AdminConfigHelper::getValue('URL_MODEL') == 1) {
            $site_url .= "/index.php";
        }

        $config = "function initItboye(){
            window.itboye = window.itboye || {};
            window.itboye.version = '".$version."';
            window.itboye.api_url = '".$site_url."';
            window.itboye.reloadWhenNotParent = function(){
                var parent = window.parent;
                console.log(parent,parent.parent);

                if(window.parent != window && parent != parent.parent){
                console.log('reloadWhenNotParent!');
                parent.location = window.location;
//                parent.parent.location.reload();
            }

            };


                window.itboye.history  = window.itboye.history || {};
                window.itboye.history.back = function(){
                    $(\".LRADMS_iframe\").each(function(index,item){
                       if($(item).data(\"id\") ==  $(\".active.menuTab\").data(\"id\")){
                           $(item).get(0).contentWindow.history.back(-1);
                       }
                    });
                };

            window.itboye.top_back = function(){

            parent.window.history.back(-1);

            };
        console.log('初始化成功!');
    }";
        echo ($config);
        echo "console.log('******(*^__^*) *********');";
        echo "console.log('Welcome to Itboye\'s World!');";
        echo "console.log('******(*^__^*) *************');";
        echo "initItboye();";
        echo "console.log(itboye);";

        echo "window.itboye.reloadWhenNotParent();";


        exit();
    }
}