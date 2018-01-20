<?php
/**
 * Created by PhpStorm.
 * @author bean
 * @time 2018/1/5 0005 16:39
 */
namespace chat\models;

use newx\orm\base\Model;

/**
 * 用户表
 * @property int $id ID
 * @property string $name 账号
 * @property string $nickname 昵称
 * @property string $password 密码
 * @property int $status 状态 0.禁用 1.正常
 * @property int $inline 是否在线 0.否 1.是
 * @property string $session_id SESSION ID
 * @property string $login_time 登录时间
 * @property string $create_time 注册时间
 */
class UserModel extends Model
{
    public $table = 'user';

    // 用户状态 - 禁用
    const STATUS_CLOSE = 0;

    // 用户状态 - 正常
    const STATUS_OPEN = 1;

    /**
     * 根据token获取用户信息
     * @param array $token
     * @return $this|null
     */
    public static function getUserByToken($token = null)
    {
        return UserModel::model()
            ->where([
                'session_id' => $token,
                'status' => UserModel::STATUS_OPEN
            ])
            ->one();
    }
}