<?php
function html_head_tip($html='',$pre=true){
  return '<blockquote class="layui-elem-quote head-tip">'.($pre ? '<strong><i class="fa fa-fw fa-info-circle"></i>提示：</strong>' : '').'<span style="display:inline-flex;">'.$html.'</span></blockquote>';
}

function html_return($url='',$msg='',$skin=''){
  if($skin === 'layer'){
    $msg = $msg ? $msg : L('close');
    return '<a style="cursor:pointer" class="layui-btn layui-btn-sm layui-btn-primary ml10 js-close-iframe">'.$msg.'</a>';
  }else{
    $msg = $msg ? $msg : L('return');
    $url = $url ? $url : 'javascript:history.go(-1)';
    return '<a href="'.$url.'" class="layui-btn layui-btn-sm layui-btn-primary ml10">'.$msg.'</a>';
  }
}