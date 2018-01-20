<?php
/**
 * Swoole接口
 * @author bean
 * @version 1.0
 */
namespace newx\library\swoole;

interface SwooleInterface
{
    /**
     * 初始化配置
     */
    public function init();

    /**
     * 启动服务
     */
    public function start();

    /**
     * 获取服务
     */
    public function get();
}