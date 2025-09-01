<?php
namespace First1\V1\Staff;

require_once __DIR__ . '/staff.class.php';

use DateTime;
use First1\V1\Staff\Staff;

class LeaveHourCount
{
    /**
     * 取得請假時數
     *
     * @param DateTime $from 起始時間
     * @param DateTime $to 結束時間
     * @param string $dateAll 是否全天 'A': 全天、'S': 特定時間
     * @return float
     */
    public static function getLeaveHours($from, $to, $dateAll = 'S')
    {
        $total_hours = 0;

        if (! $from instanceof DateTime) {
            $from = new DateTime($from);
        }

        if (! $to instanceof DateTime) {
            $to = new DateTime($to);
        }

        if ($dateAll == 'A') {
            $from = new DateTime($from->format('Y-m-d 09:00:00'));
            $to   = new DateTime($to->format('Y-m-d 18:00:00'));
        }

        $staff = new Staff;
        $staff->setDateTimePeriod($from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s'));

        // 迴圈顯示每一天的日期
        $today = clone $from;

        while ($today->format("Y-m-d") <= $to->format("Y-m-d")) {
            //是否為節日
            if ($staff->isHoliday($today->format('Y-m-d'), '09:00:00') && $staff->isHoliday($today->format('Y-m-d'), '17:30:00')) {
                $today->modify('+1 day'); // 增加一天
                continue;
            }

            //是否為假日
            if ($staff->isWeekend($today->format('Y-m-d'))) {
                $today->modify('+1 day'); // 增加一天
                continue;
            }

            // 早上上班時間
            $today_morning_from = new DateTime($today->format('Y-m-d 09:00:00'));
            if (($today->format('Y-m-d') == $from->format('Y-m-d')) && ($from->format('H:i:s') >= '09:00:00')) {
                $today_morning_from = clone $from;
            }

            $specific_holiday = $staff->getHoliday($today->format('Y-m-d'), '09:00:00');
            if (! empty($specific_holiday)) {
                $today_morning_from = new DateTime($today->format('Y-m-d') . ' ' . $specific_holiday['hToTime']);
            }

            // 早上下班時間
            $today_morning_to = new DateTime($today->format('Y-m-d 12:00:00'));
            if (($today->format('Y-m-d') == $to->format('Y-m-d')) && ($to->format('H:i:s') < '12:00:00')) {
                $today_morning_to = clone $to;
            }

            $specific_holiday = $staff->getHoliday($today->format('Y-m-d'), '12:00:00');
            if (! empty($specific_holiday)) {
                $today_morning_to = new DateTime($today->format('Y-m-d') . ' ' . $specific_holiday['hFromTime']);
            }

            // 下午上班時間
            $today_afternoon_from = new DateTime($today->format('Y-m-d 13:00:00'));
            if (($today->format('Y-m-d') == $from->format('Y-m-d')) && ($from->format('H:i:s') >= '13:00:00')) {
                $today_afternoon_from = clone $from;
            }

            $specific_holiday = $staff->getHoliday($today_afternoon_from->format('Y-m-d'), $today_afternoon_from->format('H:i:s'));
            if (! empty($specific_holiday)) {
                $today_afternoon_from = new DateTime($today->format('Y-m-d') . ' ' . $specific_holiday['hToTime']);
            }

            // 下午下班時間
            $today_afternoon_to = new DateTime($today->format('Y-m-d 17:30:00'));
            if (($dateAll == 'A') || (($today->format('Y-m-d') != $from->format('Y-m-d')) && ($today->format('Y-m-d') != $to->format('Y-m-d')))) {
                $today_afternoon_to = $today_afternoon_to->modify('+30 minutes');
            }

            if (($today->format('Y-m-d') == $to->format('Y-m-d')) && ($to->format('H:i:s') < '17:30:00')) {
                $today_afternoon_to = clone $to;
            }

            $specific_holiday = $staff->getHoliday($today->format('Y-m-d'), '17:30:00');
            if (! empty($specific_holiday)) {
                $today_afternoon_to = new DateTime($today->format('Y-m-d') . ' ' . $specific_holiday['hFromTime']);
            }

            // 上午
            $morning = ($today->format('Y-m-d') == $from->format('Y-m-d')) ? clone $from : $today_morning_from;
            $diff    = $morning->diff($today_morning_to);

            $days     = 0;
            $am_hours = 0;
            $minutes  = 0;
            if ($diff->invert == 0) { //如果為1，表示時間差為負，即結束時間小於開始時間
                $days     = $diff->d;
                $am_hours = $diff->h;
                $minutes  = $diff->i;
            }

            $days *= 8;
            $minutes /= 60;
            $am_hours += $days + $minutes;

            // 下午
            $afternoon = ($today->format('Y-m-d') == $to->format('Y-m-d')) ? $to : clone $today_afternoon_to;
            $diff      = $today_afternoon_from->diff($afternoon);

            $days     = 0;
            $pm_hours = 0;
            $minutes  = 0;
            if ($diff->invert == 0) { //如果為1，表示時間差為負，即結束時間小於開始時間
                $days     = $diff->d;
                $pm_hours = $diff->h;
                $minutes  = $diff->i;
            }

            $days *= 8;
            $minutes /= 60;
            $pm_hours += $days + $minutes;

            $hours = $am_hours + $pm_hours;
            if ($hours == 7.5) {
                $hours = 8;
            }
            $total_hours += $hours;

            $today->modify('+1 day'); // 增加一天
        }

        return $total_hours;
    }

    /**
     * 計算加班申請期間的時數
     *
     * @param string $fromDatetime 開始日期時間
     * @param string $toDatetime 結束日期時間
     * @param string $beginTime 表定開始時間
     * @param string $endTime 表定結束時間
     * @return array 各天的時數
     */

    public static function getOvertimeHours($fromDatetime, $toDatetime)
    {
        $noon_hour    = 1;   //中午休息時間 1 小時
        $evening_hour = 0.5; //傍晚休息時間半小時

        $from = new DateTime($fromDatetime);
        $to   = new DateTime($toDatetime);

        $from_date = $from->format('Y-m-d');
        $from_time = $from->format('H:i:s');

        $to_date = $to->format('Y-m-d');
        $to_time = $to->format('H:i:s');

        if ($from_date != $to_date) {
            throw new \Exception('begin date and end date must be the same');
        }

        if (($from_time >= '12:00:00') && ($from_time < '13:00:00')) { //開始時間為中午，調整為13:00:00
            $from_time = '13:00:00';
        }

        if (($from_time >= '18:00:00') && ($from_time < '18:30:00')) { //開始時間為傍晚，調整為18:30:00
            $from_time = '18:30:00';
        }

        if (($to_time >= '12:00:00') && ($to_time < '13:00:00')) { //結束時間為中午，調整為12:00:00
            $to_time = '12:00:00';
        }

        if (($to_time >= '18:00:00') && ($to_time < '18:30:00')) { //結束時間為傍晚，調整為18:00:00
            $to_time = '18:00:00';
        }

        if ($from_time >= $to_time) {
            throw new \Exception('begin time must be less than end time');
        }

        //上午
        if ($from_time < '12:00:00') {
            if ($to_time > '18:30:00') { //整天
                $noon_hour    = 1;
                $evening_hour = 0.5;
            }

            if (($to_time <= '18:30:00') && ($to_time > '18:00:00')) { //下午(傍晚時間)
                $noon_hour    = 1;
                $evening_hour = 0;
            }

            if (($to_time <= '18:00:00') && ($to_time > '13:00:00')) { //下午(含中午)
                $noon_hour    = 1;
                $evening_hour = 0;
            }

            if (($to_time <= '13:00:00') && ($to_time > '12:00:00')) { //中午
                $noon_hour    = 0;
                $evening_hour = 0;
            }

            if ($to_time <= '12:00:00') { //上午
                $noon_hour    = 0;
                $evening_hour = 0;
            }

            $hours = self::getFromToHours($from_date . ' ' . $from_time, $to_date . ' ' . $to_time) - $noon_hour - $evening_hour;
            return self::getOvertimeHoursByHalfHour($hours);
        }

        //下午
        if (($from_time >= '13:00:00') && ($from_time < '18:00:00')) {
            if ($to_time > '18:30:00') { //下半天
                $noon_hour    = 0;
                $evening_hour = 0.5;
            }

            if (($to_time <= '18:30:00') && ($to_time > '18:00:00')) { //下半午(傍晚時間)
                $noon_hour    = 0;
                $evening_hour = 0;
            }

            if (($to_time <= '18:00:00') && ($to_time > '13:00:00')) { //下半午(含中午)
                $noon_hour    = 0;
                $evening_hour = 0;
            }

            if (($to_time <= '13:00:00') && ($to_time > '12:00:00')) { //中午
                $noon_hour    = 0;
                $evening_hour = 0;
            }

            $hours = self::getFromToHours($from_date . ' ' . $from_time, $to_date . ' ' . $to_time) - $noon_hour - $evening_hour;
            return self::getOvertimeHoursByHalfHour($hours);
        }

        //傍晚
        if ($from_time >= '18:30:00') {
            $noon_hour    = 0;
            $evening_hour = 0;

            $hours = self::getFromToHours($from_date . ' ' . $from_time, $to_date . ' ' . $to_time) - $noon_hour - $evening_hour;
            return self::getOvertimeHoursByHalfHour($hours);
        }

        throw new \Exception('invalid time range. from: ' . $fromDatetime . ' to: ' . $toDatetime);
    }

    /**
     * 加班計算時數以0.5小時為單位
     * @param float $hours 時數
     * @return float
     */
    public static function getOvertimeHoursByHalfHour($hours)
    {
        $hours = round($hours, 1);

        $integer = floor($hours);
        $decimal = $hours - $integer;
        $decimal = ($decimal >= 0.5) ? 0.5 : 0;

        return $integer + $decimal;
    }

    /**
     * 取得時數
     *
     * @param string $from 開始時間
     * @param string $to 結束時間
     * @return float
     */
    public static function getFromToHours($from, $to)
    {
        $from = new DateTime($from);
        $to   = new DateTime($to);

        $diff = $from->diff($to);

        $days    = 0;
        $hours   = 0;
        $minutes = 0;
        if ($diff->invert == 0) { //如果為1，表示時間差為負，即結束時間小於開始時間
            $days    = $diff->d;
            $hours   = $diff->h;
            $minutes = $diff->i;
        }

        $days *= 8;
        $minutes /= 60;
        $hours += $days + $minutes;

        return $hours;
    }
}
