<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-10
 * Time: 9:28
 */

namespace src\freight\model;
use src\base\BaseModel as Model;

class FreightTemplate extends Model{
    /**
     * 重量
     * @author hebidu <email:346551990@qq.com>
     */
    const TYPE_WEIGHT = "2";

    /**
     * 件数
     * @author hebidu <email:346551990@qq.com>
     */
    const TYPE_COUNT = "1";

    public function fas(){
        return $this->hasMany('FreightAddress','template_id');
    }
}