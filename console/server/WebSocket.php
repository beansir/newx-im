<?php
/**
 * @author: bean
 * @version: 1.0
 */
namespace console\server;

use chat\components\Cache;
use newx\data\Redis;
use newx\helpers\ArrayHelper;

class WebSocket extends \newx\library\swoole\WebSocket
{
    const LOG_CONNECT = 'connect';

    const LOG_MESSAGE = 'message';

    const LOG_CLOSE = 'close';

    /**
     * 监听客户端连接
     * @param $server
     * @param $request
     */
    public function open($server, $request)
    {
        // 用户token
        $token = ArrayHelper::value($request->get, 'token');

        // 获取用户信息
        $user = Redis::create()->hGet(Cache::REDIS_KEY_USER_INFO, $token);
        $user = json_decode($user, true);
        $nickname = ArrayHelper::value($user, 'nickname', $request->fd);

        // 推送消息
        $this->publishMsg($server, $nickname . '上线了');

        // 连接日志
        self::log([
            'wid' => $request->fd,
            'token' => $token,
            'uid' => ArrayHelper::value($user, 'id'),
            'nickname' => $nickname,
            'user_agent' => ArrayHelper::value($request->header, 'user-agent'),
            'ip' => ArrayHelper::value($request->server, 'remote_addr'),
        ], static::LOG_CONNECT);
    }

    /**
     * 监听客户端数据
     * @param $server
     * @param $frame
     */
    public function message($server, $frame)
    {
        // 客户端数据
        $data = json_decode($frame->data, true);

        // 获取用户信息
        $token = ArrayHelper::value($data, 'token');
        $user = Redis::create()->hGet(Cache::REDIS_KEY_USER_INFO, $token);
        $user = json_decode($user, true);

        if ($user) {
            // 正常登录
            $publish_data = [
                'token' => $token,
                'nickname' => ArrayHelper::value($user, 'nickname', $frame->fd),
                'avatar' => '/public/images/user/avatar/default.jpg',
                'content' => ArrayHelper::value($data, 'content'),
                'date' => date('Y-m-d H:i:s'),
                'message' => ''
            ];
            $this->publishData($server, $publish_data);
        } else {
            // 被迫下线
            $this->publishError($server, $frame->fd, 2);
        }
    }

    /**
     * 监听客户端关闭连接
     * @param $server
     * @param $fd
     */
    public function close($server, $fd)
    {

    }

    /**
     * @param $data
     * @param $type
     */
    public static function log($data, $type)
    {
        $path = '/data/www/newx/console/logs/chat/' . $type;
        if (!file_exists($path)) {
            @mkdir($path, 0777, true);
        }
        $file = $path . '/' . date('Ymd') . '.log';
        $data = is_array($data) ? print_r($data, true) : $data;
        $data = "\n" . date('Y-m-d H:i:s') . "\n$data\n";
        @file_put_contents($file, $data, FILE_APPEND);
    }

    /**
     * 推送消息
     * @param $server
     * @param $msg
     */
    protected function publishMsg($server, $msg)
    {
        $users = $server->connections;
        if ($users) {
            $res = json_encode([
                'code' => 1,
                'data' => [
                    'token' => '',
                    'nickname' => '',
                    'avatar' => '',
                    'content' => '',
                    'date' => '',
                    'message' => $msg
                ]
            ]);
            foreach ($users as $fd) {
                $server->push($fd, $res);
            }
        }
    }

    /**
     * 推送数据
     * @param $server
     * @param $data
     */
    protected function publishData($server, $data)
    {
        $users = $server->connections;
        if ($users) {
            $res = json_encode([
                'code' => 1,
                'data' => $data
            ]);
            foreach ($users as $fd) {
                $server->push($fd, $res);
            }
        }

        // 消息日志
        self::log($data, static::LOG_MESSAGE);
    }

    /**
     * 推送错误信息
     * @param $server
     * @param $fd
     * @param $code
     */
    protected function publishError($server, $fd, $code)
    {
        $push_data = json_encode([
            'code' => $code,
            'data' => []
        ]);
        $server->push($fd, $push_data);
    }
}