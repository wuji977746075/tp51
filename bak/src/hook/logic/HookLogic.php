<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-26
 * Time: 19:48
 */

namespace app\src\hook\logic;

use app\src\base\logic\BaseLogic;
use app\src\hook\model\Hook;

/**
 * Class HookLogic
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\hook\logic
 */
class HookLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new Hook());
    }
}