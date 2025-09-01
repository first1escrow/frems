<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/staffNotify.class.php';
require_once dirname(dirname(dirname(__DIR__))) . '/lib/rc4/crypt.php';
require_once __DIR__ . '/leaveConfig.php';

use First1\V1\Staff\StaffNotify;

//加密文字
function enCrypt($str, $seed = 'firstfeedSms')
{
    $encode = '';
    $rc     = new Crypt_RC4;
    $rc->setKey($seed);
    $encode = $rc->encrypt($str);

    return $encode;
}

$date        = $_POST['date'];
$time        = $_POST['time'];
$type        = $_POST['type'];
$staff       = $_SESSION['member_id'];
$description = $_POST['desc'];

if (empty($date) || ! preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $date)) {
    http_response_code(400);
    exit('無法確認補打卡申請資訊(D)');
}

if (empty($time) || ! preg_match("/^[0-9]{2}\:[0-9]{2}$/", $time) || $time < '09:00' || $time > '21:00') {
    http_response_code(400);
    exit('無法確認補打卡申請資訊(T)');
}

if (empty($type) || ! in_array($type, ['IN', 'OUT'])) {
    http_response_code(400);
    exit('無法確認補打卡申請資訊(Y)');
}

$conn = new first1DB;

//20250328 確認申請補打卡日期是否已被鎖定
$sql      = 'SELECT sDate FROM tStaffLockDate WHERE 1 ORDER BY sDate DESC LIMIT 1;';
$lockDate = $conn->one($sql)['sDate'];

if ($date <= $lockDate) {
    http_response_code(400);
    exit('補打卡日期已被鎖定，無法申請');
}

//取得主管
$sql = 'SELECT
            b.sStaffId
        FROM
            tPeopleInfo AS a
        JOIN
            tSupervisor as b ON a.pDep = b.sDepartment
        WHERE
            a.pId = :staff AND b.sStatus = "Y";';
$rs = $conn->one($sql, ['staff' => $staff]);

$supervisor = $rs['sStaffId'];
$supervisor = ($staff == $supervisor) ? MANAGER : $supervisor; //20241216 家津請示過雄哥後，主管請假直接由總經理簽核

$sql = 'INSERT INTO `tStaffCheckInApply` (`sStaffId`, `sApplyDate`, `sApplyTime`, `sApplyType`, `sReason`, `sSupervisor`, `sCreatedAt`) VALUES (:staff, :date, :time, :type, :reason, :supervisor, NOW());';
if ($conn->exeSql($sql, ['staff' => $staff, 'date' => $date, 'time' => $time . ':00', 'type' => $type, 'reason' => $description, 'supervisor' => $supervisor])) {
    $last_id = $conn->lastInsertId();

    //發送通知
    $code = json_encode([
        'caseId' => $last_id,
        'ts'     => time(),
    ], JSON_UNESCAPED_UNICODE);

    $code = enCrypt($code);

    $message = $_SESSION['member_name'] . '申請 ' . $date . ' ' . $time . ' 補打卡，請審核。 ' . WWWHOST . '/line/confirm/checkInConfirm.php?code=' . $code;

    StaffNotify::send($supervisor, $message);

    exit('已送出申請、待審核中...');
}

http_response_code(500);
exit('無法送出申請');
