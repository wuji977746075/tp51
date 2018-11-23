<?php
// 路由变量
// 设置name变量规则（采用正则定义,自动前^后$,以/开头表示使用完整路由规则，局部规则覆盖全局）
// Route::pattern('name', '\w+');
// 支持批量添加
// Route::pattern([
//     'name' => '\w+',
//     'id'   => '\d+',
// ]);
// Route::get('item-<name>-<id>', 'product/detail')
//     ->pattern(['name' => '\w+', 'id' => '\d+']);

// install
Route::get('install/', 'install/index');
// another admin path : bug  html replace error(e:index)
// Route::get('admin/', '@[admin/login/]index');

// 一些链接的重写
// Route::get('think', function () {
//     return 'hello,ThinkPHP5!';
// });
// Route::get('hello/:name', 'index/hello');
// 重定向默认301
// Route::get('blog/:id','http://blog.thinkphp.cn/read/:id')->status(302);
// 5.1.3+
// Route::redirect('blog/:id','http://blog.thinkphp.cn/read/:id')->status(302);
// 直接模板渲染 5.1.3+
// Route::view('push/test','push@test',['city'=>'shanghai']);
// 依赖注入
// Response $resp
// Request  $req
// Route::get('hello/:name', function(Request $req,$name){
//   return json('hello:'. $name);
// });
// Route::get('static', response()->code(404));

return [
];