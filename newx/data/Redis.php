<?php
/**
 * @author: bean
 * @version: 1.0
 */
namespace newx\data;

class Redis
{
    /**
     * HOST
     * @var string
     */
    private static $_host = '127.0.0.1';

    /**
     * PORT
     * @var int
     */
    private static $_port = 6379;

    /**
     * Redis连接
     * @var \Redis
     */
    private static $_connection;

    /**
     * 获取Redis实例
     * @return \Redis
     */
    public static function create()
    {
        if (!isset(self::$_connection)) {
            $connection = new \Redis();
            $connection->connect(self::$_host, self::$_port);
            self::$_connection = $connection;
        }
        return self::$_connection;
    }
}