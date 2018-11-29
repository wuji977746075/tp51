<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\admin\controller;


use app\src\email\action\EmailSendAction;

class Email extends  Admin{

    public function send(){
        if(IS_GET){
            $to_email = $this->_param('to_email',0)?$this->_param('to_email',0):"";
            $this->assign("to_email",$to_email);
            $return_url = $this->_param('return_url',0)?$this->_param('return_url',0):"";
            $this->assign("return_url",$return_url);
            return $this->boye_display();
        }else{
            $title = $this->_param('title','');
            $to_email=$this->_param('to_email','');
            $content=$this->_param('content','');
            $return_url=$this->_param('return_url','')?$this->_param('return_url',''):url('Admin/Email/send');

            $result=(new EmailSendAction())->send($to_email,$title,$content);
            if($result['status']){
                $this->success("发送成功！",$return_url);
            }else{
                $this->error($result['info']);
            }

        }
    }





}
