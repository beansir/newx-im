<?php
/**
 * 数组帮助类
 * @author: bean
 * @version: 1.0
 */
namespace newx\helpers;

class ArrayHelper
{
    /**
     * 根据键名获取键值，重组为新数组
     * @param array $arrays 二维数组
     * @param string $key 键名
     * @return array
     */
    public static function values($arrays = [], $key = null)
    {
        $data = [];
        if (!empty($arrays) && is_array($arrays)) {
            foreach ($arrays as $array) {
                if (is_array($array) && array_key_exists($key, $array)) {
                    $data[] = $array[$key];
                }
            }
        }
        return $data;
    }

    /**
     * 根据键名获取键值
     * @param array $arrays 一维数组
     * @param string|array $key 键名
     * @param mixed $default 默认值
     * @return null|mixed
     */
    public static function value($arrays = [], $key, $default = null)
    {
        $data = !empty($default) ? $default : null;

        if (empty($arrays) || !is_array($arrays)) {
            return $data;
        }

        if (is_array($key)) {
            foreach ($key as $item) {
                if (!array_key_exists($item, $arrays)) {
                    return $data;
                }
                $arrays = $arrays[$item];
            }
            return $arrays;
        } else {
            if (array_key_exists($key, $arrays)) {
                return $arrays[$key];
            } else {
                return $data;
            }
        }
    }

    /**
     * 将第二维数组中的指定键名的键值提到第一维数组中作为键名
     * @param array $array 必须是二维数组
     * @param string $key 指定键名
     * @return array
     */
    public static function index($array = [], $key = null)
    {
        $data = [];
        if ($array && is_array($array)) {
            foreach ($array as $index => $item) {
                if (is_array($item) && array_key_exists($key, $item)) {
                    $data[$item[$key]] = $item;
                }
            }
        }
        return $data;
    }

    /**
     * 数组转XML
     * @param array $array
     * @param string $parent 父级标签名
     * @return string
     */
    public static function xml($array = [], $parent = 'xml')
    {
        $data = '<?xml version="1.0" encoding="utf-8"?>';
        $data .= '<' . $parent . '>';
        $data .= self::array2xml($array);
        $data .= '</' . $parent . '>';
        return $data;
    }

    /**
     * 数组转XML数据处理
     * @param array $array
     * @return string
     */
    private static function array2xml($array = [])
    {
        global $data;
        if ($array) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $data .= '<' . $key . '>';
                    self::xml($value);
                    $data .= '</' . $key . '>';
                } else {
                    $data .= '<' . $key . '>' . $value . '</' . $key . '>';
                }
            }
        }
        return $data;
    }
}