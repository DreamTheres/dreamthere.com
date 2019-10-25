<?php

/**
 * Description of DateUtil
 *
 * @author Administrator
 */
class DateUtil {

    /**
     * 比较两个日期间的距离信息
     * @param string $date1     日期1 例:'2017-01-01 12:00:00'
     * @param string $date2     日期2 例:'2017-02-01 13:00:00'
     * @return array        两个日期间的距离信息
     */
    public static function diffDate($date1, $date2) {
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $interval = $datetime1->diff($datetime2);
        $time['y'] = $interval->format('%y');
        $time['m'] = $interval->format('%m');
        $time['d'] = $interval->format('%d');
        $time['h'] = $interval->format('%h');
        $time['i'] = $interval->format('%i');
        $time['s'] = $interval->format('%s');
        $time['a'] = $interval->format('%a');    // 两个时间相差总天数（不含当前天）
        $time['tm'] = $time['y'] * 12 + $time['m']; // 两个时间相差总月数（不含当前月）
        return $time;
    }

}
