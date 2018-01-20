<?php
/**
 * 异常基础类
 * @author: bean
 * @version: 1.0
 */

namespace newx\exception;

class BaseException extends \Exception
{
    /**
     * 异常名称
     * @var string
     */
    public $name = 'Notice Error';

    /**
     * 抛出异常
     */
    public function throwOut()
    {
        header("HTTP/1.1 500 Internal Server Error");
        header("status: 500 Internal Server Error");
        return exceptionError($this);
    }
}