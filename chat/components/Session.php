<?php
/**
 * Created by PhpStorm.
 * @author bean
 * @time 2018/1/5 0005 17:11
 */
namespace chat\components;

use newx\helpers\ArrayHelper;

class Session
{
    /**
     * SESSION KEY
     * @var string
     */
    protected static $key = 'SID';

    /**
     * 有效时间
     * @var int
     */
    protected static $expire = 86400 * 30;

    /**
     * 获取SESSION ID
     * @return mixed|null|string
     */
    public static function get()
    {
        $id = ArrayHelper::value($_COOKIE, static::$key);
        if (!$id) {
            $id = self::create();
            self::set($id);
        }
        return $id;
    }

    /**
     * 存储SESSION ID
     * @param $id
     * @return bool
     */
    public static function set($id)
    {
        return setcookie(static::$key, $id, time() + static::$expire);
    }

    /**
     * 生成SESSION ID
     * @return string
     */
    protected static function create()
    {
        return md5(date('YmdHis', time()) . rand(100, 999) . rand(100, 999));
    }
}