<?php
/**
 * Created by PhpStorm.
 * User: Zhoujinda
 * Date: 2016/10/27
 * Time: 16:36
 */
//todo : 待整合
namespace app\src\tool;

use app\index\logic\contract\createOrderLogic;
use app\system\model\MemberConfig;
use app\system\model\Organization;
use app\system\model\OrgMember;
use app\system\model\UserContract;
use app\system\model\UserContractContent;
use app\system\model\UserPictureType;
use PhpOffice\PhpWord\Settings;

class wordPDF {

    static protected function deal_rent_info($rent_info,$pay_type){

        if($pay_type==createOrderLogic::PAY_ONCE){
            //一次支付
        }


        $rent_info = explode(',',$rent_info);
        if(count($rent_info)==0){
            $ret = '____';
            return $ret;
        }

        //判断是否统一房租
        $flag = true;
        $t = null;
        foreach ($rent_info as $val){
            if(is_null($t)){
                $t = $val;
            }else if($t != $val){
                $flag = false;
                break;
            }
        }
        if($flag){
            //统一房租
            $ret = '该房屋月租金为（人民币）'.($rent_info[0]/100).'元整。';
            return $ret;
        }


        $ret = "";

        foreach ($rent_info as $key=>$val){

            $t = intval($key) + 1;
            $p = $val/100;
            $ret .= "该房屋第 $t 年月租金为（人民币）$p 元整。";

        }
        return $ret;
    }

    static public function save($contract_no, $replace = false, $pc = false){

        $UserContract = UserContract::get(['contract_no' => $contract_no]);
        if(is_null($UserContract)){
            exception('error contract');
        }
        if($UserContract instanceof UserContract){}
        $tpl = $UserContract->template_id;

        $UserContractContent = UserContractContent::all(['contract_no' => $contract_no]);
        $keys = [];
        if(!is_null($UserContractContent)){
            foreach ($UserContractContent as $val){
                if($val instanceof UserContractContent){}
                if(substr($val->key,0,1)!='_'){
                    $keys[$val->key] = $val->value;
                }
            }
        }

        $file_time = $UserContract->update_time;

        Settings::loadConfig(VENDOR_PATH. '/phpoffice/phpword/phpword.ini.dist');
        Settings::setPdfRendererPath(VENDOR_PATH. '/dompdf/dompdf');
        Settings::setOutputEscapingEnabled(true);
        define('DOMPDF_ENABLE_AUTOLOAD', false);

        $PUBLIC_PATH = ROOT_PATH .'/public';


        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $templateProcessor = $phpWord->loadTemplate($PUBLIC_PATH.'/template/contractTpl.docx');

        //$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($PUBLIC_PATH.'/template/contractTpl.docx');

        $variables = $templateProcessor->getVariables();

        $sign_leaser = 0;
        $sign_renter = 0;

        $pay_type = isset($keys['pay_type']) ? $keys['pay_type'] : false;

        foreach ($variables as $val){
            $text = "____";
            if($val == 'lesser_idcard'){
                continue;
            }
            if($val == 'renter_idcard'){
                continue;
            }
            if($val== 'contract_no'){
                continue;
            }
            if($val == 'authorization'){
                continue;
            }
            if($val == 'alliance_img'){
                continue;
            }
            if($val == 'zhujia_img'){
                continue;
            }
            if(isset($keys[$val])){
                $text = $keys[$val];
                //房租信息的处理
                if($val == 'rent_info' && $keys['pay_type']){
                    $text = self::deal_rent_info($text,$pay_type);
                }
                if($val == 'sign_leaser'){
                    $sign_leaser = intval($text);
                    continue;
                }
                if($val == 'sign_renter'){
                    $sign_renter = intval($text);
                    continue;
                }
                if($val == 'start_date'){
                    $text = date('Y年n月j日',strtotime($text));
                }
                if($val == 'end_date'){
                    $text = date('Y年n月j日',strtotime($text));
                }
                if($val == 'sign_leaser_date'){
                    $text = date('Y年n月j日',strtotime($text));
                }
                if($val == 'sign_renter_date'){
                    $text = date('Y年n月j日',strtotime($text));
                }
            }

            $templateProcessor->setValue($val, $text);
        }



        $date = date('Ymd',$file_time);

        $CONTRACT_PATH = $PUBLIC_PATH .'/upload/userContract/'.$date;


        $fileName = $CONTRACT_PATH .'/'.$contract_no.'.docx';
        $htmlName = $CONTRACT_PATH .'/'.$contract_no.'.html';
        $htmlName_pc = $CONTRACT_PATH .'/'.$contract_no.'.pc.html';

        if(!is_dir($CONTRACT_PATH)){
            mkdir($CONTRACT_PATH,0777);
        }


        if($replace){
            if(file_exists($fileName)){
                unlink($fileName);
            }
            $tempFileName = $templateProcessor->save();
            copy($tempFileName, $fileName);

            $phpWord = \PhpOffice\PhpWord\IOFactory::load($tempFileName);
            $phpWord->save($htmlName_pc, 'HTML');



        }else{
            if(!file_exists($fileName)){
                $tempFileName = $templateProcessor->save();
                copy($tempFileName, $fileName);

                $phpWord = \PhpOffice\PhpWord\IOFactory::load($tempFileName);
                $phpWord->save($htmlName_pc, 'HTML');

            }
        }

        //签字图片处理
        self::dealImg([['sign_leaser',$sign_leaser],['sign_renter',$sign_renter]],$htmlName_pc);
        //身份证图处理
        self::dealIdImg($UserContract,$htmlName_pc);
        //处理合同html标题
        self::dealTitle($UserContract,$htmlName_pc);

        //放大字体 另存一份手机字体放大后的html
        self::fonSize($htmlName_pc,$htmlName);

        return $pc?$htmlName_pc:$htmlName;

    }


    //放大字体 另存一份手机字体放大后的html
    static function fonSize($filename_pc,$filename){

        if(!file_exists($filename_pc)) return;
        $file = file_get_contents($filename_pc);
        //缩放字体
        $file = str_replace('font-size: 12pt;', 'font-size: 3rem;', $file,$count);

        file_put_contents($filename,$file);
    }

    //处理合同html标题
    static function dealTitle(UserContract $UserContract,$filename){
        if(!file_exists($filename)) return;
        $file = file_get_contents($filename);

        $contract_no = $UserContract->contract_no;
        $file = str_replace('<title>PHPWord</title>', '<title>住家租房合同'.$contract_no.'</title>', $file,$count);
        $file = str_replace('${contract_no}', $contract_no, $file,$count);

        unlink($filename);
        file_put_contents($filename,$file);
    }

    //身份证图处理
    static function dealIdImg(UserContract $UserContract,$filename){

        $owner_id = $UserContract->first_party_uid;
        $renter_id = $UserContract->second_party_uid;
        $house_no = $UserContract->house_no;

        $owner_img = [];
        $renter_img = [];
        $agent_img = [];

        $UserPictureType = UserPictureType::all(['uid'=>$owner_id, 'type'=>'id']);
        if(!is_null($UserPictureType)){
            foreach ($UserPictureType as $val){
                $owner_img[] = $val['img_id'];
            }
        }
        $UserPictureType = UserPictureType::all(['uid'=>$renter_id, 'type'=>'id']);
        if(!is_null($UserPictureType)){
            foreach ($UserPictureType as $val){
                $renter_img[] = $val['img_id'];
            }
        }
        //委托书处理
        $UserPictureType = UserPictureType::all(['uid'=>$owner_id, 'extra'=>$house_no, 'type'=>'agent']);
        if(!is_null($UserPictureType)){
            foreach ($UserPictureType as $val){
                $agent_img[] = $val['img_id'];
            }
            self::dealImg([['authorization',$agent_img]], $filename,600,440);
        }else{
            if(!file_exists($filename)) return;
            $file = file_get_contents($filename);
            $file = str_replace('${authorization}', '无', $file,$count);
            unlink($filename);
            file_put_contents($filename,$file);
        }
        //联盟店公司水印
        //查找所属联盟店
        $OrgMember = OrgMember::get(['uid' => $owner_id]);

        if(!is_null($OrgMember)){
            $oid = $OrgMember['top_oid'];
            //联盟店管理员
            $admin_uid = $OrgMember::get(['oid'=>$oid, 'is_admin'=>1]);
            if(!is_null($admin_uid)){
                $admin_uid = $admin_uid['uid'];
            }else{
                $admin_uid = 0;
            }

            $UserPictureType = UserPictureType::where(['uid'=>$admin_uid,  'type'=>'alliance'])->find();

            if($admin_uid!=0 && !is_null($UserPictureType)){
                $alliance_img = $UserPictureType->img_id;
                self::dealImg([['alliance_img',$alliance_img]], $filename,600,440);
            }else{
                if(!file_exists($filename)) return;
                $file = file_get_contents($filename);
                $file = str_replace('${alliance_img}', '', $file,$count);
                unlink($filename);
                file_put_contents($filename,$file);
            }
        }else{
            if(!file_exists($filename)) return;
            $file = file_get_contents($filename);
            $file = str_replace('${alliance_img}', '', $file,$count);
            unlink($filename);
            file_put_contents($filename,$file);
        }

        //住家水印
        $PUBLIC_PATH = ROOT_PATH .'/public';
        $file = file_get_contents($filename);
        $img = file_get_contents($PUBLIC_PATH.'/template/zhujia_img.png');
        $img_src = 'data:image/jpeg;base64,'. chunk_split(base64_encode($img)) ;
        $width = 300;
        $height = 300;
        $img_html = '<img border="0" style="width: '.$width.'px; height: '.$height.'px;" src="' .$img_src  .'"><br/>';
        $file = str_replace('${zhujia_img}', $img_html, $file,$count);
        unlink($filename);
        file_put_contents($filename,$file);

        self::dealImg([['lesser_idcard',$owner_img],['renter_idcard',$renter_img]], $filename,400,240);

    }

    static function dealImg($keys = [], $filename, $width=200, $height=100){
        if(!file_exists($filename)) return;

        $file = file_get_contents($filename);
        foreach ($keys as $val){
            $key = $val[0];
            $img_id = $val[1];
            $img_html = '';
            if (is_array($img_id)){

                foreach ($img_id as $val2){
                    $img_url = config('api_url').'/picture/index?id='.$val2;
                    @$img =  file_get_contents($img_url);
                    $img_src = 'data:image/jpeg;base64,'. chunk_split(base64_encode($img)) ;
                    $img_html .= '<img border="0" style="width: '.$width.'px; height: '.$height.'px;" src="' .$img_src  .'"><br/>';

                }

            }else{
                if($img_id<=0) continue;

                $img_url = config('api_url').'/picture/index?id='.$img_id;
                @$img =  file_get_contents($img_url);
                $img_src = 'data:image/jpeg;base64,'. chunk_split(base64_encode($img)) ;
                $img_html = '<img border="0" style="width: '.$width.'px; height: '.$height.'px;" src="' .$img_src  .'">';

            }

            $file = str_replace('${'.$key.'}', $img_html, $file,$count);

            unlink($filename);
            file_put_contents($filename,$file);

        }


    }
}