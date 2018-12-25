<?php
namespace src\lock\lock;
use src\base\BaseLogic as BaseApi;

class LockHisLogic extends BaseApi{
  // 动作对应值暂不能修改,因为是直接保存的
  const ACTION_LOCK_INIT = '锁初始化';
  const ACTION_LOCK_RENAME = '锁重命名';
  const ACTION_LOCK_PUSH = '推送设置';
  const ACTION_LOCK_UNBIND = '删除管理员钥匙';
  const ACTION_REG = '账号注册';
  const ACTION_UNBIND = '账号解绑';
  const ACTION_BIND = '账号换绑';
  const ACTION_HOUSE_BIND = '绑定房源';
  const ACTION_HOUSE_UNBIND = '解绑房源';
  const ACTION_CHANGE_ADMIN = '锁管理员转移';

  const ACTION_PASS_EDIT = '修改密码';
  const ACTION_PASS_RESET = '重置用户密码';
  const ACTION_PASS_GET = '获取密码';
  const ACTION_PASS_ADD = '添加密码';
  const ACTION_PASS_DEL = '删除密码';
  const ACTION_PASS_RENAME = '设置密码别名';
  const ACTION_PASS_RESET_RENT = '重置租户密码';

  const ACTION_KEY_RENAME = '钥匙重命名';
  const ACTION_KEY_SEND_USER = '发送用户钥匙';
  const ACTION_KEY_SEND_RENT = '发送租户钥匙';
  const ACTION_KEY_SEND_RENT_USER = '发送租户用户钥匙';
  const ACTION_KEY_CHANGE = '钥匙期限变更';
  const ACTION_KEY_CHANGE_LINK = '钥匙期限关联变更';
  const ACTION_KEY_RESET = '重置普通钥匙';
  const ACTION_KEY_UNLOCK = '解冻钥匙';
  const ACTION_KEY_LOCK = '冻结钥匙';
  const ACTION_KEY_LOCK_LINK = '冻结关联钥匙';
  const ACTION_KEY_DEL_RENT  = '删除租户钥匙';
  const ACTION_KEY_DEL_USER  = '删除用户钥匙';
  const ACTION_KEY_DEL_UNADMIN  = '删除非管理员钥匙';
  const ACTION_KEY_DEL_RENT_USER  = '删除租户用户钥匙';
  const ACTION_KEY_DEL_RENT_USER_LINK  = '删除关联租户用户钥匙';

  const ACTION_ICCARD_ADD   = '添加IC卡';
  const ACTION_ICCARD_DEL   = '删除IC卡';
  const ACTION_ICCARD_CLEAR = '清空IC卡';
}