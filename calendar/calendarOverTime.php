<?php
/*
 * 2025-01-10 加班行程串接到假勤系統
 * 平日加班時間為 18:30 ~ 23:59:59
 * 假日加班時間為 09:00 ~ 23:59:59
 */
require_once (dirname(__DIR__)) . '/class/staff.class.php';
use First1\V1\Staff\Staff;

function calendarOverTime($postRawData){
    $output = '';
    $postRawData = http_build_query($postRawData);
    writeLog(date("Y-m-d H:i:s"). "\n" .$postRawData);
    $rc = new Crypt_RC4 ;
    $rc->setKey('firstCalendar') ;
    $postData = ['q' => $rc->encrypt($postRawData)];

    try {
        $ch = curl_init();
        $url = 'https://www.first1.com.tw/line/firstSales/overtime.php';

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        //{"status":200,"message":"加班申請成功"}
        $response = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $http_code = $curl_info['http_code'];
        $total_time = $curl_info['total_time'];

        $response_p = json_decode($response, true);
        writeLog($response."\n=============================================================");

        if($response_p['status'] != '200'){
            $output = '錯誤：' . $response_p['message'];
        } else if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) {
            $output = '錯誤：請求超時';
        } else if (curl_errno($ch)) {
            $output = '錯誤：' . curl_error($ch);
        }
    } catch (Exception $e) {
        $output = "錯誤：" . $e->getMessage();
        writeLog($output."\n=============================================================");
    } finally {
        if (isset($ch) && is_resource($ch)) {
            curl_close($ch);
        }
    }

    return $output;
}

//判斷是否為加班
function checkOvertime($startTime, $endTime) {
    $startTime = new DateTime($startTime);
    $endTime = new DateTime($endTime);
    $staff = new Staff;

    $staff->setDateTimePeriod($startTime->format('Y-m-d 00:00:00'), $endTime->format('Y-m-d 23:59:59'));
    $isWeekend = $staff->isWeekend($startTime->format('Y-m-d'));

    //判斷 例假日,假日,補班
    $overtimeType = $isWeekend ? 'H' : 'W';
    $isHolidayN = $staff->isHoliday($startTime->format('Y-m-d'));
    $isHolidayY = $staff->isHoliday($startTime->format('Y-m-d'), $startTime->format('H:i:s') ,'Y');
    if (!empty($isHolidayN)) {
        $overtimeType = 'H';
    } elseif (!empty($isHolidayY)){
        $overtimeType = 'W';
    }

    // 設定加班起始時間 18:30(平日上班)，09:00(假日上班)
    if($overtimeType == 'H'){
        $overtimeStart = new DateTime($startTime->format('Y-m-d') . ' 09:00:00');
    } else {
        $overtimeStart = new DateTime($startTime->format('Y-m-d') . ' 18:30:00');
    }

    // 如果開始時間或結束時間比加班時間晚，就算加班
    if ($startTime > $overtimeStart || $endTime > $overtimeStart) {
        // 計算加班時間（分鐘）
        if ($startTime <= $overtimeStart) {
            $overtimeMinutes = ($endTime->getTimestamp() - $overtimeStart->getTimestamp()) / 60;
        } else {
            $overtimeMinutes = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 60;
        }

        return [
            'isOverTime' => true,
            'overtimeMinutes' => round($overtimeMinutes),
            'message' => "加班時數：" . round($overtimeMinutes / 60, 1) . "小時"
        ];
    }

    return [
        'isOverTime' => false,
        'overtimeMinutes' => 0,
        'message' => "沒有加班"
    ];
}

function writeLog($content)
{

    // $fs = '/home/httpd/html/first.twhg.com.tw/calendar/overTime.log";
    $fs = dirname(__DIR__) . '/log/' . "overTime.log";
    $fp = fopen($fs, 'a+');
//    fwrite($fp, "============[" . date("Y-m-d H:i:s") . "]===========================\n");
    fwrite($fp, "content:" . $content . "\n");
//    fwrite($fp, "=============================================================\n");
    fclose($fp);
}