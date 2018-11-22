<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/2
 * Time: 12:19
 */

namespace app\src\aliyuncs\action;


use app\src\aliyuncs\green\Request\V20161018\ImageDetectionRequest;
use app\src\base\action\BaseAction;
use app\src\base\helper\ExceptionHelper;
use think\Exception;

class ImageDetectAction extends BaseAction
{

    public function __construct()
    {
        vendor("aliyuncs/aliyun-php-sdk-core/Config");
    }


    public function sync($imageUrl=""){
        date_default_timezone_set("PRC");

        if(empty($imageUrl)){
           return $this->error('错误的参数');
        }

        $accessKeyId = "LTAI9fWqXTAbknLF";
        $accessKeySecret = "48PBRJx9QRkIIxl4vwMAKjSUFR0V9c";

        //请替换成你自己的accessKeyId、accessKeySecret
        $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $accessKeyId, $accessKeySecret);
        \DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", "Green", "green.cn-hangzhou.aliyuncs.com");
        $client = new \DefaultAcsClient($iClientProfile);
        // 图片检测
        $request = new  ImageDetectionRequest();
        //设置参数
        $request->setAsync("false");
        //设置图片链接, 最多支持50张图片
        $request->setImageUrl($imageUrl);
        //设置检测的场景
        //同步支持多个场景同时识别
        //porn: 黄图检测
        //ocr： ocr文字识别
        //illegal: 暴恐敏感识别
        //$request->setScene(json_encode(array("porn","ocr","illegal")));
        $request->setScene(json_encode(array("porn")));

        try{
            $response = $client->getAcsResponse($request);
//            print_r($response);
            //返回状态值成功时进行处理
            if("Success" == $response->Code){
                $imageResults = $response->ImageResults->ImageResult;
                foreach ($imageResults as $imageResult) {
//                    print_r($imageResult);
                    if(property_exists($imageResult, "PornResult")) {
                        //黄图结果处理
                        $pornResult = $imageResult->PornResult;
                        if($pornResult != null
                            && property_exists($pornResult, "Rate")
                            && property_exists($pornResult, "Label")) {
                            return $this->success(['rate'=>$pornResult->Rate,'label'=>$pornResult->Label]);
                        }
                    }
                    if(property_exists($imageResult, "IllegalResult")) {
                        //暴恐敏感结果处理
                        $illegalResult = $imageResult->IllegalResult;
                        if($illegalResult != null
                            && property_exists($illegalResult, "Rate")
                            && property_exists($illegalResult, "Label")) {
                            return $this->success(['rate'=>$illegalResult->Rate,'label'=>$illegalResult->Label]);
                        }
                    }
                    if(property_exists($imageResult, "OcrResult")) {
                        //ocr结果处理
                        //打印ocr结果
                        $ocrResult = $imageResult->OcrResult;
                        if($ocrResult != null
                            && property_exists($illegalResult, "Text")) {
                            print_r($ocrResult->Text);
                            return $this->success(['rate'=>0,'label'=>$ocrResult->Text]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            return $this->error(ExceptionHelper::getErrorString($e));
        }

        return $this->error("检测失败");
    }

}