<?php
namespace app\index\domain;

class AddressDomain extends BaseDomain{


    /**
     * 设置默认地址
     * @author hebidu <email:346551990@qq.com>
     */
    public function setDefault(){
        // halt($this->getOriginData());
        $api_ver = $this->checkVersion("10
            0", "请增加s_id参数22");
        $id  = input("id/d",0);
        $uid = input("uid/d",0);

        $this->apiReturnSuc(['id'=>$id,'uid'=>$uid]);
        // $this->apiReturnErr(lang('fail'));

    }
}