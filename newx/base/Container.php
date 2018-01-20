<?php
/**
 * 全局容器
 * @author: bean
 * @version: 1.0
 */
namespace newx\base;

use newx\data\Connection;
use newx\helpers\ArrayHelper;

class Container
{
    /**
     * 容器数据
     * @var array
     */
    private static $containers = [];

    /**
     * 获取容器数据
     * @param string $key
     * @return mixed
     */
    public static function get($key = null)
    {
        if (!$key) {
            return self::$containers;
        }
        return ArrayHelper::value(self::$containers, $key);
    }

    /**
     * 配置容器数据
     * @param $key
     * @param $data
     */
    public static function set($key, $data)
    {
        self::$containers[$key] = $data;
    }

}