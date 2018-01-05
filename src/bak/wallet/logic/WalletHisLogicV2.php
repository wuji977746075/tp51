<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2016-12-28 17:33:30
 * Description : [Description]
 */

namespace app\src\wallet\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\wallet\model\WalletHis;


/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\
 * @example
 */
class WalletHisLogicV2 extends BaseLogicV2 {

    //初始化
    protected function _init(){
        $this->setModel(new WalletHis());
    }

    /**
     * 钱包历史记录
     * @Author
     * @DateTime 2017-01-04T10:31:06+0800
     * @return   [type]                   [description]
     */
    public function his($params){
        extract($params);
        return (new WalletHisLogicV2())-> query(['uid'=>$uid],['curpage'=>$current_page,'size'=>$per_page],'create_time desc');
    }
}