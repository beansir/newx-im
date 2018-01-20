<?php
/**
 * 响应体
 * @author: bean
 * @version: 1.0
 */
namespace newx\base;

use newx\helpers\ArrayHelper;

class Response
{
    const CONTENT_TYPE_JSON = 'json';
    const CONTENT_TYPE_XML = 'xml';

    /**
     * 请求状态
     * @var bool
     */
    private $_status;

    /**
     * 状态码
     * @var int
     */
    private $_code;

    /**
     * 状态描述
     * @var string
     */
    private $_msg;

    /**
     * 数据
     * @var array
     */
    private $_data;

    /**
     * 类型
     * @var string
     */
    private $_type;

    /**
     * Response constructor.
     * @param string $type
     */
    public function __construct($type = null)
    {
        $this->_type = $type ? $type : static::CONTENT_TYPE_JSON;
    }

    /**
     * 创建实例
     * @param string $type
     * @return $this
     */
    public static function create($type = null)
    {
        return new self($type);
    }

    /**
     * 请求成功
     * @param string $msg
     * @param array $data
     * @param int $code
     */
    public function success($msg = null, $data = [], $code = null)
    {
        $this->_status = true;
        $this->setMsg($msg, 'success');
        $this->setCode($code, 1);
        $this->setData($data);
        $this->output();
    }

    /**
     * 请求失败
     * @param string $msg
     * @param array $data
     * @param int $code
     */
    public function error($msg = null, $data = [], $code = null)
    {
        $this->_status = false;
        $this->setMsg($msg, 'error');
        $this->setCode($code, 0);
        $this->setData($data);
        $this->output();
    }

    /**
     * 配置状态码
     * @param int $code
     * @param int $default
     * @return $this
     */
    private function setCode($code = null, $default = null)
    {
        $this->_code = $code;
        if (empty($this->_code)) {
            $this->_code = $default;
        }
        return $this;
    }

    /**
     * 配置状态描述
     * @param string $msg
     * @param string $default
     * @return $this
     */
    private function setMsg($msg = null, $default = null)
    {
        $this->_msg = $msg;
        if (empty($this->_msg)) {
            $this->_msg = $default;
        }
        return $this;
    }

    /**
     * 配置数据
     * @param array $data
     * @return $this
     */
    private function setData($data = [])
    {
        if (!empty($data) && is_array($data)) {
            $this->_data = $data;
        } else {
            $this->_data = [];
        }
        return $this;
    }

    /**
     * 数据输出
     */
    private function output()
    {
        $data = [
            'status' => $this->_status,
            'code' => $this->_code,
            'msg' => $this->_msg,
            'data' => $this->_data
        ];
        switch ($this->_type) {
            case static::CONTENT_TYPE_JSON:
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($data);
                break;
            case static::CONTENT_TYPE_XML:
                header('Content-type: application/xml; charset=utf-8');
                echo ArrayHelper::xml($data);
                break;
            default:
                $this->_type = static::CONTENT_TYPE_JSON;
                $this->output();
                break;
        }
        exit;
    }
}