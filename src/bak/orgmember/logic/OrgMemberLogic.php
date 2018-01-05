<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-26
 * Time: 19:48
 */

namespace app\src\i18n\logic;

use app\src\base\logic\BaseLogic;
use app\src\orgmember\model\OrgMember;

/**
 * Class MultiLanguageLogic
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\i18n\logic
 */
class OrgMemberLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new OrgMember());
    }
}