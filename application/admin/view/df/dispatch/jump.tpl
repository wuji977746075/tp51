<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>跳转提示</title>
    <meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
<link rel="stylesheet" href="__CDN__layui/2.4.5/css/layui.css" media="all">
    <style type="text/css">
      body{ width:100%;min-height:100%;background-color: rgba(0,0,0,0.5); }
        .system-message{ padding: 0 0 20px;margin: 100px auto;max-width: 400px;background-color:#f8f8f8; }
        .system-message h3{ font-size: 50px; font-weight: normal; line-height: 120px; margin-bottom: 12px;border:1px solid #ccc; }
        .system-message .jump{ padding-top: 10px; }
        .system-message .jump a{ color: #333 !important; }
        .system-message .success,.system-message .error{ padding: 24px; line-height: 1.8em; font-size: 23px ;text-align: center; }
        .system-message .error{color: #F93434; }
        .system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display:none; }
    </style>
</head>
<body>
<div class="system-message">
    <div style="padding:24px;" class="layui-elem-quote"><p>
    <?php if($code == 1){ echo $msg; ?>
    <?php }else{ ?>
      <?php echo $code;?> : <span style="padding-top:0px;"><?php echo($msg); ?></span>
      <p><?php if($data) dump($data); ?> </p>
    <?php } ?>
    </p></div>
    <p class="detail"></p>
    <div class="jump" style="padding-right:5px;text-align:center;">
        <a id="href" class="layui-btn" href="<?php echo($url); ?>" style="color:#fff;">跳转（ <b id="wait"><?php echo($wait); ?></b> ）</a>
        <a id="cancel" class="layui-btn" href="javascript::void;" style="color:#A8EB8F;" onclick="cancel();">取消</a>
    </div>
</div>
<script type="text/javascript">
    var timer = {};
    //取消跳转
    function cancel(){
      if(!isNaN(timer.interval)){
          clearInterval(timer.interval);
      }
      return false;
    }
    function start(){
        var wait = document.getElementById('wait'),href = document.getElementById('href').href;
        timer.interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time == 0) {
                location.href = href;
                cancel();
            };

        }, 1000);
    }
    (function(){
        start();
    })();
</script>
</body>
</html>