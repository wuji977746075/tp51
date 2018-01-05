<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/2
 * Time: 12:40
 */

namespace app\domain;


use app\src\aliyuncs\action\ImageDetectAction;
use app\src\i18n\helper\LangHelper;
use think\Exception;

class AliyuncsDomain extends BaseDomain
{
    public function imageDetection(){
        $imgUrl = $this->_post('img_url','',LangHelper::lackParameter('img_url'));

        $imgUrl = json_encode([$imgUrl]);

        $action = new ImageDetectAction();

        $result = $action->sync($imgUrl);

        $this->returnResult($result);
    }
}