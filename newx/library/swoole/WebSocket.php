<?php
/**
 * @author: bean
 * @version: 1.0
 */
namespace newx\library\swoole;

use newx\exception\AppException;
use newx\helpers\ArrayHelper;

class WebSocket implements SwooleInterface
{
    /**
     * 服务
     * @var object
     */
    private $_server;

    /**
     * 主机地址
     * @var string
     */
    private $_host;

    /**
     * 端口
     * @var int
     */
    private $_port;

    /**
     * WebSocket constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->init($config);

        // 创建服务
        $this->_server = new \swoole_websocket_server($this->_host, $this->_port);

        // 监听客户端连接
        $this->_server->on('open', [$this, 'open']);

        // 监听数据接收
        $this->_server->on('message', [$this, 'message']);

        // 监听客户端连接关闭
        $this->_server->on('close', [$this, 'close']);
    }

    /**
     * 初始化
     * @param array $config
     * @throws AppException
     */
    public function init($config = [])
    {
        // 服务主机地址
        $this->_host = ArrayHelper::value($config, 'host');
        if (empty($this->_host)) {
            throw new AppException('web socket config error: host not exists');
        }

        // 服务端口
        $this->_port = ArrayHelper::value($config, 'port');
        if (empty($this->_port)) {
            throw new AppException('web socket config error: port not exists');
        }
    }

    /**
     * 启动服务
     */
    public function start()
    {
        $this->_server->start();
    }

    /**
     * 获取服务
     */
    public function get()
    {
        return $this->_server;
    }

    /**
     * 监听客户端连接
     * @param $server
     * @param $request
     */
    public function open($server, $request){}

    /**
     * 监听客户端数据
     * @param $server
     * @param $frame
     */
    public function message($server, $frame){}

    /**
     * 监听客户端关闭连接
     * @param $server
     * @param $fd
     */
    public function close($server, $fd){}

}