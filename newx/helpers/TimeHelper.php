<?php
/**
 * 时间帮助类
 * @author bean
 * @version: 1.0
 */
namespace newx\helpers;

class TimeHelper
{
    /**
     * 时间戳格式化
     * @param int $timestamp
     * @param string $format
     * @return string
     */
    public static function format($timestamp = 0, $format = 'Y-m-d H:i:s')
    {
        return $timestamp > 0 ? date($format, $timestamp) : date($format, time());
    }

    /**
     * 按月份获取日期列表
     * @param int $month 月份，默认当月
     * @param bool $isUnixTime 是否时间戳格式
     * @param string $format 时间格式，非时间戳时有效
     * @return array
     */
    public static function dateListByMonth($month = null, $isUnixTime = false, $format = 'Y-m-d')
    {
        $year = date('Y');
        $month = $month ? $month : date('m');
        //$day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $day = date("t", strtotime($year . '-' . $month));
        $start_date = $year . '-' . $month . '-01';
        $end_date = $year . '-' . $month . '-' . $day;
        return self::dateListByInterval($start_date, $end_date, $isUnixTime, $format);
    }

    /**
     * 按区间获取日期列表
     * @param string $startDate 开始日期 1970-01-01
     * @param string $endDate 结束日期
     * @param bool $isUnixTime 是否时间戳格式
     * @param string $format 非时间戳格式
     * @return array
     */
    public static function dateListByInterval($startDate, $endDate, $isUnixTime = false, $format = 'Y-m-d')
    {
        $data = [];
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        while ($startDate <= $endDate) {
            if (!$isUnixTime) {
                $data[] = date($format, $startDate);
            } else {
                $data[] = $startDate;
            }
            $startDate = $startDate + 86400;
        }
        return $data;
    }

    /**
     * 按天数获取日期列表
     * @param int $count 天数
     * @param string $endDate 结束日期 1970-01-01
     * @return array
     */
    public static function dateListByCount($count = 1, $endDate = null)
    {
        $data = [];
        if (empty($endDate)) {
            $endDate = date('Y-m-d', time());
        }
        for ($i = $count - 1; $i >= 0; $i--) {
            $data[] = date('Y-m-d', strtotime("-{$i}day", strtotime($endDate)));
        }
        return $data;
    }
}