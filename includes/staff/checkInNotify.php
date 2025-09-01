<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/staffNotify.class.php';
require_once dirname(dirname(dirname(__DIR__))) . '/lib/rc4/crypt.php';
require_once __DIR__ . '/leaveConfig.php';
require_once __DIR__ . '/leaveFunction.php';

use First1\V1\Staff\StaffNotify;

//取得案件
$sql = 'SELECT sId, sStaffId, (SELECT pName FROM tPeopleInfo WHERE pId = a.sStaffId) as staff, sSupervisor FROM tStaffCheckInApply AS a WHERE sId = :id;';
$rs  = $conn->one($sql, ['id' => $sId]);

if (empty($rs)) {
    http_response_code(400);
    exit('無法取得欲審核紀錄');
}

$rs['sSupervisor'] = ($rs['sSupervisor'] == $_SESSION['member_id']) ? VICE_MANAGER : $rs['sSupervisor']; //自己審核自己給副總批核

//發送通知
$code = json_encode([
    'caseId' => $rs['sId'],
    'ts'     => time(),
], JSON_UNESCAPED_UNICODE);

$code = enCrypt($code);

$message = $rs['staff'] . '申請' . $date . '補打卡，請審核。 <br>';
$message .= '<a href="' . WWWHOST . '/line/confirm/checkInConfirm.php?code=' . $code . '" target="_blank">審核連結</a>';

exit($message);
