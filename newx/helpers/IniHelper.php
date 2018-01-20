<?php
/**
 * @author: bean
 * @version: 1.0
 */
namespace newx\helpers;

class IniHelper
{
    /**
     * 配置时区
     * @param string $timezone
     */
    public static function setTimezone($timezone = null)
    {
        if (!empty($timezone)) {
            date_default_timezone_set($timezone);
        }
    }
}