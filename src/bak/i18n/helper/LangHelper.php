<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-01
 * Time: 13:41
 */

namespace app\src\i18n\helper;


class LangHelper
{
    public static function lackParameter($param=''){
        return lang('lack_parameter',["param"=>$param]);
    }
    public static function getLangSupport(){
        return [];
        $lang_list = cache("hbd_lang_support");
        if(empty($lang_list)){
            $r= file_get_contents('http://api.guannan.itboye.com/public/index.php/Lang/Support');
            if($r===false){
                return false;
            }else{
                $lang_list= json_decode($r,true);
            }
            cache("hbd_lang_support" , $lang_list , 60*24*3600);
        }

        return $lang_list;
    }
}