<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/traits/CheckIn.trait.php';

class CheckInOut
{
    use CheckIn;

    private $conn;

    public function __construct()
    {
        $this->conn = new first1DB;
    }
}

$fh = dirname(dirname(__DIR__)) . '/log/checkInOut';
if (! is_dir($fh)) {
    mkdir($fh, 0777, true);
}

$fh_error1 = $fh . '/error1_' . date('Ymd') . '.log'; //打卡寫入資料庫失敗
$fh_error2 = $fh . '/error2_' . date('Ymd') . '.log'; //資料庫顯示新增成功，但查無紀錄
$fh .= '/' . date('Ymd') . '.log';                    //打卡紀錄 raw data

$inOut = strtoupper($_POST['type']);
$from  = $_POST['from'];
$staff = $_SESSION['member_id'];

$date = date('Y-m-d');
$time = date('H:i:s');

file_put_contents($fh, date('Y-m-d H:i:s') . ' Request: inOut = ' . $inOut . '、from = ' . $from . '、staff = ' . $staff . '、date = ' . $date . '、time = ' . $time . PHP_EOL, FILE_APPEND);

if (($time < '07:00:00') || ($time > '20:10:00') && ($_SESSION['member_pDep'] != 7)) { //20241121 會議討論後，打卡時間調整為07:00 ~ 20:10 20241230 會議討論後，業務部門不限制時間
    http_response_code(400);
    exit('打卡時間為 07:00 ~ 20:10');
}

$ip = $_SERVER['REMOTE_ADDR'];
if (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}

if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
}

$conn = new first1DB;

//20250305 上班打卡時間顯示當日第一筆打卡時間
$earlyTime = '';
if ($inOut == 'IN') {
    $sql = 'SELECT sDateTime FROM tStaffCheckIn WHERE sStaffId = ' . $staff . ' AND sInOut = "IN" AND sDateTime >= "' . $date . ' 00:00:00' . '" AND sDateTime <= "' . $date . ' 23:59:59' . '" ORDER BY sDateTime ASC LIMIT 1;';
    $rs  = $conn->one($sql);
    if (! empty($rs)) {
        $earlyTime = date('H:i:s', strtotime($rs['sDateTime']));
    }
}

$sql = 'INSERT INTO `tStaffCheckIn` (`sInOut`, `sFrom`, `sIp`, `sStaffId`, `sDateTime`, `sCreated_at`) VALUES (:type, :from, :ip, :staff, :time, :time);';
if ($conn->exeSql($sql, ['type' => $inOut, 'from' => $from, 'ip' => $ip, 'staff' => $staff, 'time' => $date . ' ' . $time])) {
    $last_id = $conn->lastInsertId();

    if ($inOut == 'IN') {
        $checkInOut = new CheckInOut;

        $holidays        = $checkInOut->getHolidays($date . ' 00:00:00', $date . ' 23:59:59');
        $workingHolidays = $checkInOut->getHolidays($date . ' 00:00:00', $date . ' 23:59:59', true);
        $leaves          = $checkInOut->getLeaves($staff, $date . ' 00:00:00', $date . ' 23:59:59');
        $overtimes       = $checkInOut->getOvertimes($date . ' 00:00:00', $date . ' 23:59:59');
        $checkInTime     = $checkInOut->getCheckInTimeByDate($staff, $holidays, $workingHolidays, $leaves, $date, $overtimes);

        $time_ts        = new DateTime($date . ' ' . $time);
        $checkInTime_ts = new DateTime($date . ' ' . $checkInTime);

        //打卡時間超過規定時間，且不超過6分鐘時，每月可有3次寬限(允許遲到 09:01 ~ 09:05)
        if (($time_ts->getTimestamp() > $checkInTime_ts->getTimestamp()) && ($time_ts->getTimestamp() <= $checkInTime_ts->modify('+300 seconds')->getTimestamp()) && empty($checkInOut->isCheckIn($staff, $date, 'IN', $last_id))) {
            $sql = 'SELECT sId, sLateDate, sCheckInTime, sCheckInOutIndex FROM tStaffMonthlyLateAllow WHERE sStaffId = :staff AND sLateDate >= :from_date AND sLateDate <= :to_date';
            $rs  = $conn->all($sql, ['staff' => $staff, 'from_date' => date('Y-m-01', strtotime($date)), 'to_date' => date('Y-m-t', strtotime($date))]);

            if ((count($rs) < 3) && (! in_array($date, array_column($rs, 'sLateDate')))) {
                $sql = 'INSERT INTO `tStaffMonthlyLateAllow` (`sStaffId`, `sLateDate`, `sCheckInTime`, `sCheckInOutIndex`, `sOrder`, `sCreatedAt`) VALUES (:staff, :date, :time, :index, :order, NOW());';
                $conn->exeSql($sql, ['staff' => $staff, 'date' => $date, 'time' => $time, 'index' => $last_id, 'order' => count($rs) + 1]);
            }
        }
    }

    $sql = 'SELECT sId, sStaffId, sDateTime, sInOut FROM tStaffCheckIn WHERE sId = :id;';
    $rs  = $conn->one($sql, ['id' => $last_id]);
    if (empty($rs)) {
        file_put_contents($fh_error1, date('Y-m-d H:i:s') . ' Check In Out fail (date: ' . $date . '、time: ' . $time . '、inOut: ' . $inOut . '、staff: ' . $staff . ').' . PHP_EOL . 'Sql: ' . $conn->debug() . PHP_EOL, FILE_APPEND);
        http_response_code(400);
        // exit('<center>打卡失敗！請通知資訊人員</center>');
        exit('打卡失敗！請通知資訊人員');
    }

    if (! empty($earlyTime)) {
        $time .= '<div style="padding:5px;"></div><p style="font-size:10pt;">（今日已打過卡：' . $earlyTime . '）</p>';
    }

    $ampm = ($inOut == 'IN') ? '上班' : '下班';
    exit('<center>' . $ampm . '打卡時間：' . $time . '</center>');
}

file_put_contents($fh_error2, date('Y-m-d H:i:s') . ' Check In Out fail (date: ' . $date . '、time: ' . $time . '、inOut: ' . $inOut . '、staff: ' . $staff . ').' . PHP_EOL . 'Sql: ' . $conn->debug() . PHP_EOL, FILE_APPEND);

http_response_code(400);
// exit('<center>打卡失敗！請通知資訊人員</center>');
exit('打卡失敗！請通知資訊人員');
