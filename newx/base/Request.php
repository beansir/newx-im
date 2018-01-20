<?php
/**
 * 请求体
 * @author bean
 * @version: 1.0
 */
namespace newx\base;

use newx\helpers\ArrayHelper;

class Request
{
    /**
     * GET
     * @var array
     */
    private $_get = [];

    /**
     * POST
     * @var array
     */
    private $_post = [];

    /**
     * REQUEST
     * @var array
     */
    private $_request = [];

    /**
     * BODY
     * @var mixed
     */
    private $_body;

    /**
     * HEADER
     * @var array
     */
    private $_header = [];

    /**
     * Request constructor.
     */
    public function __construct()
    {
        // GET
        $this->_get = $_GET;

        // POST
        $this->_post = $_POST;

        // REQUEST
        $this->_request = $_REQUEST;

        // BODY
        $body = file_get_contents('php://input');
        if ($body) {
            if (stristr($this->type(), 'application/json')) {
                $body = json_decode($body, true);
            }
        }
        $this->_body = $body;

        // HEADER
        $server = $_SERVER;
        if ($server) {
            foreach ($server as $key => $value) {
                if (stristr($key, 'HTTP')) {
                    $index = strtolower(str_replace('HTTP_', '', $key));
                    $this->_header[$index] = $value;
                }
            }
        }
    }

    /**
     * 创建实例
     * @return $this
     */
    public static function create()
    {
        return new self();
    }

    /**
     * 获取$_GET
     * @param $name
     * @param $default
     * @return array|string|null
     */
    public function get($name = null, $default = null)
    {
        return $this->data($this->_get, $name, $default);
    }

    /**
     * 获取$_POST
     * @param $name
     * @param $default
     * @return array|null|string
     */
    public function post($name = null, $default = null)
    {
        return $this->data($this->_post, $name, $default);
    }

    /**
     * 获取$_REQUEST
     * @param $name
     * @param $default
     * @return array|null|string
     */
    public function all($name = null, $default = null)
    {
        return $this->data($this->_request, $name, $default);
    }

    /**
     * 获取Body
     * @param $name
     * @param $default
     * @return array|null|string
     */
    public function body($name = null, $default = null)
    {
        $data = $this->_body;
        if ($name) {
            $data =  ArrayHelper::value($data, $name);
        }
        if (!$data && $default) {
            $data = $default;
        }
        return $data;
    }

    /**
     * 获取Header
     * @param $name
     * @param $default
     * @return array|string|null
     */
    public function header($name = null, $default = null)
    {
        $data = $this->_header;
        if ($name) {
            $data =  ArrayHelper::value($data, $name);
        }
        if (!$data && $default) {
            $data = $default;
        }
        return $data;
    }

    /**
     * Content Type
     * @return string|null
     */
    public function type()
    {
        return ArrayHelper::value($_SERVER, 'CONTENT_TYPE');
    }

    /**
     * 数据处理
     * @param $data
     * @param $name
     * @param $default
     * @return array|string|null
     */
    private function data($data, $name = null, $default = null)
    {
        if ($name) {
            $data = ArrayHelper::value($data, $name);
        }
        if (!$data) {
            $data = $this->body($name, $default);
        }
        return $data;
    }
}