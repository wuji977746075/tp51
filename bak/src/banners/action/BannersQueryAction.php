<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-06
 * Time: 10:26
 */

namespace app\src\banners\action;


use app\src\banners\logic\BannersLogic;
use app\src\base\action\BaseAction;

/**
 * Class BannersQueryAction
 * banner查询操作类
 * @package app\src\banners\action
 */
class BannersQueryAction extends BaseAction
{

    /**
     * banner查询操作
     * modify 1. 新增url_type参数 @20170117
     * @param $position integer 图片位置
     * @param string $url_type 跳转链接类型
     * @return array
     * @author hebidu <email:346551990@qq.com>
     */
    public function query($position,$url_type=''){
        $map = ['position'=>$position];
        if(!empty($url_type)){
            $map['url_type'] = $url_type;
        }
        $order ="sort asc";
        $field ='url,url_type,notes,img,title';

        $result = (new BannersLogic())->queryNoPaging($map,$order,$field);
        return $this->result($result);
    }

}