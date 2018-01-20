<?php
/**
 * 自动加载类
 * @author: bean
 * @time: 2017-3-11 16:51:27
 * @version 1.0
 */

namespace newx\base;

use Newx;
use newx\exception\AppException;

class AutoLoader
{
    /**
     * 自动加载文件
     * @param string $class className
     * @throws AppException
     */
    public static function autoload($class)
    {
        // 第三方库不加载
        if (in_array($class, Newx::$thirdLibrary)) {
            return;
        }

        // 检查类文件是否存在
        $classFile = Newx::getDir('module') . $class . '.php';
        $classFile = str_replace('\\', '/', $classFile); // 兼容linux

        if (!file_exists($classFile)) {
            throw new AppException('load file not exists: ' . $classFile);
        }

        // 类文件加载日志
        Newx::$classLoads[] = $classFile;

        // 加载类文件
        require_once $classFile;
    }
}
spl_autoload_register("\\newx\\base\\AutoLoader::autoload");