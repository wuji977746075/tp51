<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-18
 * Time: 18:09
 */

namespace src\address\logic;
use src\base\logic\BaseLogic;


/**
 * Class AddressLogic
 * @author hebidu <email:346551990@qq.com>
 * @package src\system\logic
 */
class AddressLogic extends BaseLogic{

    public function add($entity){
        $logic = new AddressLogic();
        if(!isset($entity['uid'])) return $this->error(lang('uid_need'));

        $uid = $entity['uid'];

        $result = $logic->count(['uid'=>$uid]);

        if(!$result['status']) return $this->error(lang('fail'));

        $count = intval($result['info']);

        if($count >= 10){
            return $this->error(lang("exceed_limit",['param'=>10]));
        }

        $result = $logic->add($entity,"id");
    }
    public function delete($map){

        $uid = $map['uid'];
        $id  = $map['id'];

        $logic = new AddressLogic();

        $result = $logic->delete($map);

        if($result['status']){

            $result = $logic->getInfo(['uid'=>$uid]);
            $address_id = 0;

            if($result['status'] && is_array($result['info']) && !empty($result['info'])){
                $address = $result['info'];
                $address_id = $address['id'];
            }

            $logic = new MemberConfigLogic();
            //
            $logic->save(['uid'=>$uid,'default_address'=>$id],['default_address'=>$address_id]);

            return $this->success(1);
        }

        return $this->error($result['info']);

    }
}