<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/9
 * Time: 11:01
 */

namespace app\domain;


use app\src\i18n\helper\LangHelper;
use app\src\tool_email\helper\EmailHelper;

class EmailDomain extends BaseDomain
{
    public function send(){
        $to_email = $this->_post('to_email','',LangHelper::lackParameter('to_email'));
        $title = $this->_post('title','',LangHelper::lackParameter('title'));
        $content = $this->_post('content','',LangHelper::lackParameter('content'));

        $result = (new EmailHelper())->send($to_email,$title,$content);

        $this->returnResult($result);
    }

}