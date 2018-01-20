<?php
/**
 * 字符串帮助类
 * @author: bean
 * @version: 1.0
 */
namespace newx\helpers;

class StringHelper
{
    /**
     * 防SQL注入
     * @param string $sql
     * @return string|mixed
     */
    public static function sqlSafe($sql = null)
    {
        if (is_string($sql)) {
            return str_replace(['"', "'", ';', '_', '%'], ['\"', "\'", '\;', '\_', '\%'], $sql);
        } else {
            return $sql;
        }
    }

    /**
     * 以分隔符分隔，各个首字母转成大写，并去除分隔符，例如: hello-world 转为 HelloWorld
     * @param string $string
     * @param string $delimiter
     * @return string|mixed
     */
    public static function lower2upper($string = null, $delimiter = null)
    {
        if (!$string) {
            return $string;
        }
        if (!$delimiter || !stristr($string, $delimiter)) {
            return ucfirst($string);
        }
        $array = explode($delimiter, $string);
        foreach ($array as &$item) {
            $item = ucfirst($item);
        }
        $string = implode('', $array);
        return $string;
    }

    /**
     * 以大写字母分隔，各个大写字母转为小写，并用分隔符拼接，例如: HelloWorld 转为 hello-world
     * @param string $string
     * @param string $delimiter
     * @return string|mixed
     */
    public static function upper2lower($string = null, $delimiter = null)
    {
        if ($string && is_string($string)) {
            $string = preg_replace_callback(
                "/[A-Z]/",
                function ($match) use ($delimiter) {
                    return $delimiter . strtolower($match[0]);
                },
                $string
            );
            if (strpos($string, $delimiter) === 0) {
                $string = substr($string, 1);
            }
        }
        return $string;
    }
}