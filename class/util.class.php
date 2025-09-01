<?php
namespace First1\V1\Util;

require_once __DIR__ . '/traits/CharactorWidthConvert.traits.php';

class Util
{
    use CharactorWidthConvert;

    /**
     * 日期轉換
     * convertDateToEast: 轉換至民國年
     * convertDateToWest: 轉換至公元年
     */
    public static function convertDateToEast($date, $divide = '-', $slash = '-')
    {
        if (!preg_match("/\d+\-\d+\-\d+$/", $date)) {
            throw new \Exception('Invalid date format.');
        }

        $tmp = explode($divide, $date);
        if ($tmp[0] >= 1911) {
            $tmp[0] -= 1911;
        }

        return implode($slash, $tmp);
    }

    public static function convertDateToWest($date, $divide = '-', $slash = '-')
    {
        if (!preg_match("/\d+\-\d+\-\d+$/", $date)) {
            throw new \Exception('Invalid date format.');
        }

        $tmp = explode($divide, $date);
        if ($tmp[0] <= 1911) {
            $tmp[0] += 1911;
        }

        return implode($slash, $tmp);
    }
    ##

    /**
     * 月份轉換季別
     */
    public static function monthToSeason($month)
    {
        return ceil($month / 3);
    }
    ##

    /**
     * 季別轉月份
     */
    public static function seasonToMonth($season)
    {
        $last_month = $season * 3;

        $months   = [];
        $months[] = $last_month;
        $months[] = --$last_month;
        $months[] = --$last_month;

        sort($months);

        return $months;
    }
    ##

    /**
     * JSON 格式化回應
     */
    public static function jsonResponse($status, $message, $data = null)
    {
        $response = [
            'status'  => $status,
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}
