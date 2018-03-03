<?php
namespace app\admin\controller;

class Test extends CheckLogin{

  public function map(){

    $this->assign('desc', 'desc');
    $this->assign('name', 'name');
    $this->assign('logo', 1);
    $this->assign('lat', 30);
    $this->assign('lng', 120);
    return $this->show();
  }
  public function captcha(){

    // $s = input('captcha','');
    // dump($s);
    // $s = captcha_check($s);
    // die();

    // $s = config('captcha');
    // $s = app('think\Config')->pull('captcha');
    halt($s);
  }
  // modal map
  public function add(){
    // $event = controller('Admin/Blog', 'event');
    // echo $event->update(5); // 输出 update:5

    // action('blog/index','','event'); //访问admin/event/blog/index
    // dump(app('think\view'));
    // halt()
    // abort('403','public function ');
    // die();
    return $this->show();
  }
}