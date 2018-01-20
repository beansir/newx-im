<?php
/**
 * 函数库
 * @author: bean
 * @version: 1.0
 */

// 异常错误抛出
if (!function_exists('exceptionError')) {
    /**
     * @param \newx\exception\BaseException $exception
     * @return string|mixed
     */
    function exceptionError($exception)
    {
        ob_start();
        ob_implicit_flush(false);
        require NEWX_PATH . '/views/exception.php';
        return ob_get_clean();
    }
}

// 格式化输出
if (!function_exists('output')) {
    /**
     * @param $data
     */
    function output($data)
    {
        if (is_array($data) || is_object($data)) {
            echo "<pre>";
            print_r($data);
        } else {
            echo $data;
        }
    }
}