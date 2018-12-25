<?php
namespace src\lock\lock;
use src\base\BaseModel as Model;

class LockKey extends Model{

  // 另有common.php getKeyType
  const ADMIN     = 0; // 管理员钥匙
  const USER      = 1; // 用户钥匙
  const RENT      = 2; // 租户钥匙
  const RENT_USER = 3; // 租户用户钥匙

  // 另有common.php getKeyStatus
  const OK     = 110401; // 钥匙正常
  const WAIT   = 110402; // 钥匙等待接收
  const FROZE  = 110405; // 钥匙冻结
  const DELETE = 110408; // 钥匙删除
  const RESET  = 110410; // 钥匙重置

  const LOW_POWER = 30;          // 低电量阈值
  const RENT_PASS_LIMIT = 5;     // 正常租户最大发送密码:个
  const RENT_PASS_DAY_LIMIT = 5; // 正常租户默认密码时长:天

	protected $table = 'itboye_locks_key';
  protected $autoWriteTimestamp = true;

  // 科技侠密码类型描述
  public static function getScienerTypeDesc($t){
    switch ($t) {
      case 1:
        return '单次';
        break;
      case 2:
        return '永久';
        break;
      case 3:
        return '限时';
        break;
      case 4:
        return '删除';
        break;
      default:
        return '未知';
        break;
    }
  }
}