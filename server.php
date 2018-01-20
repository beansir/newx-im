<?php

class Server
{
    public $host = '0.0.0.0';

    public $port = 9501;

    public $server;

    /**
     * Server constructor.
     */
    public function __construct()
    {
        // 创建服务
        $this->server = new swoole_server($this->host, $this->port);

        // 监听客户端连接
        $this->server->on('connect', [$this, 'connect']);

        // 监听数据接收
        $this->server->on('receive', [$this, 'receive']);

        // 监听客户端连接关闭
        $this->server->on('close', [$this, 'close']);
    }

    /**
     * 启动服务器
     */
    public function run()
    {
        $this->server->start();
    }

    /**
     * 监听客户端连接
     */
    public function connect($server, $fd)
    {
        echo "User {$fd} Connected\n";
    }

    /**
     * 监听客户端数据
     */
    public function receive($server, $fd, $from_id, $data)
    {
        // 传送数据
        $users = $server->connections;
        foreach ($users as $fd) {
            $server->send($fd, "User $fd: $data");
        }

        echo "User $fd: $data\n";
    }

    /**
     * 监听客户端关闭连接
     */
    public function close($server, $fd)
    {
        echo "User $fd Closed\n";
    }
}

$server = new Server();
$server->run();