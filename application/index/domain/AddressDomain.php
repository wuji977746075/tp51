<?php
namespace app\index\domain;
/**
 * @title 文档index类
 */
class AddressDomain extends BaseDomain{

    /**
     * @title 设置默认地址
     * @description
     * @author 作者
     * @url http://www.baidu.com/save.html
     * @method POST
     *
     * @code 200 成功
     * @code 201 失败
     *
     * @param string name 名称 '' false
     * @param int age 年龄 '' false
     * @return {"code":200,"message":"666","data":{"param":1}}
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