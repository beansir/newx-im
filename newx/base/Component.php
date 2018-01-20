<?php
/**
 * 组件类
 * @author: bean
 * @version: 1.0
 */
namespace newx\base;

class Component
{
    /**
     * CONSTRUCT
     * @param array $configs 组件配置
     */
    public function __construct($configs = array())
    {
        if (!empty($configs)) {
            foreach ($configs as $property => $config) {
                foreach ($config as $method => $value) {
                    if (method_exists($this, '_' . $method)) {
                        $this->{$property} = $this->{'_' . $method}($value);
                    }
                }
            }
        }
    }

    /**
     * 类组件
     * @param string $className 类名
     * @return object
     */
    public function _class($className)
    {
        return new $className;
    }
}