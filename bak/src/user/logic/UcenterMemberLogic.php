<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 17:10
 */

namespace app\src\user\logic;

use app\src\base\helper\ConfigHelper;
use app\src\base\helper\ValidateHelper;
use app\src\base\logic\BaseLogic;
use app\src\user\model\UcenterMember;

class UcenterMemberLogic extends BaseLogic
{
    /**
     * @return mixed
     */
    protected function _init()
    {
        $this->setModel(new UcenterMember());
    }
    /**
     * [getOneInfo 获取表某一字段]
     * @Author
     * @DateTime 2016-12-28T10:24:54+0800
     * @param    [type]                   $uid   [description]
     * @param    string                   $field [description]
     * @return   [type]                          [description]
     */
    public function getOneInfo($uid,$field='mobile'){
      $r = $this->getModel()->where(['id'=>$uid])->field($field)->find();
      return $r ? $r->getData()[$field] : '';
    }
    /**
     * 验证uid 与psw 是否对应
     * @param $uid
     * @param $psw
     * @param int $encrypt 表示是否加密过的psw，默认加密
     */
    public function auth($uid,$psw,$encrypt=1){

        $result = $this->getInfo(['id'=>$uid]);
        if(ValidateHelper::legalArrayResult($result)){
            $user = $result['info'];

            if($encrypt == 1){
                return $user['password'] == $psw;
            }else{
                $salt = ConfigHelper::getPasswordSalt();
                return $user['password'] == think_ucenter_md5($psw,$salt);
            }
        }

        return false;
    }

    /**
     * 用户注册
     *
     * @param $username
     * @param $password
     * @param $email
     * @param $mobile
     * @param $reg_from
     * @param $country
     * @return array
     * @internal param $from
     */
    public function register($username, $password, $email, $mobile, $country,$reg_from){

        $user             = new UcenterMember();
        $user->username   = $username;
        $user->email      = $email;
        $user->password   = $password;
        $user->mobile     = $mobile;
        $user->reg_from   = $reg_from;
        $user->country_no = $country;
        $result = $user->save();
        if($result === false){
            return $this->apiReturnErr($user->getError());
        }else{
            return $this->apiReturnSuc($user->id);
        }
    }

    public function checkUsername($username){
        $result = $this->getInfo(['username'=>$username]);
        if ($result['status'] && is_array($result['info'])) {
            return array('status' => true, 'info' => $result['info']);
        } else {
            $result = $this->getInfo(['mobile'=>$username]);
            if ($result['status'] && is_array($result['info'])) {
                return array('status' => true, 'info' => $result['info']);
            }
        }
        return array('status' => false, 'info' => $result['info']);
    }

}