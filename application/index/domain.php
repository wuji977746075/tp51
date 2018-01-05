<?php
 /**
  * Author      : rainbow <hzboye010@163>
  * DateTime    : 2017-12-26 16:59:12
  * Description : [Description]
  * 没配的无权访问,alpha 排序
  * 每条为一个domain : config
  *  里面为 各api : app
  *    [节点操作see/config_see 默认空,是否需要登陆 默认false]
  */
return [
  'config'=>[
    'app'    =>['',false]
    ,'version'=>['app_see']
  ],
  'ava' =>[
    'test' =>[]
  ]
];