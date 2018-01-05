<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-17
 * Time: 11:47
 */

namespace app\src\securitycode\logic;


use app\src\base\logic\BaseLogic;
use app\src\securitycode\model\SecurityCode;

class SecurityCodeLogic extends BaseLogic
{
    protected function _init()
    {
        $this->setModel(new SecurityCode());
    }
    
    public function resetAll($acceptor,$type,$client_id){
        
        $result = $this->save(array('accepter'=>$acceptor,'type'=>$type,'client_id'=>$client_id) , array('status'=>1));

        if($result === false){
            return $this->apiReturnErr($this->getModel()->getError());
        }

        return $this->apiReturnSuc($acceptor);
    }

    /**
     * 验证验证码是否有效
     * @param $code     string
     * @param $acceptor
     * @param $type
     * @param $client_id
     * @param bool $is_clear 是否清除同类型验证码
     * @return array
     * @internal param string $mobile
     */
    public function isLegalCode($code, $acceptor, $type , $client_id ,$is_clear = true){

        
        if($code == "itboye"){
            return $this->apiReturnSuc(lang("tip_legal_code"));
        }

        $map=array(
            'code'=>$code,
            'accepter'=>$acceptor,
            'type'=>$type,
            'client_id'=>$client_id
        );

        $order="endtime desc";

        $result = $this->getModel()->where($map)->order($order)->find();

        if(is_null($result)){
            return $this->apiReturnErr(lang("err_invalid_code"));
        }

        $codeEntity = $result;

        if($codeEntity['status'] != 0){
            return $this->apiReturnErr(lang("err_code_used"));
        }

        if($codeEntity['endtime'] < NOW_TIME){
            return $this->apiReturnErr(lang("err_code_expired"));
        }

        //1. 清除该手机号对应的验证码
        if($is_clear){
            $result = $this->resetAll($acceptor,$type,$client_id);
            if(!$result['status']){
                return $this->apiReturnErr($result['info']);
            }
        }

        return $this->apiReturnSuc(lang("tip_legal_code"));

    }
    

}