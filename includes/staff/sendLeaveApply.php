<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/leaveHourCount.class.php';
require_once dirname(dirname(__DIR__)) . '/class/slack.class.php';
require_once dirname(dirname(dirname(__DIR__))) . '/lib/rc4/crypt.php';
require_once __DIR__ . '/leaveConfig.php';
require_once __DIR__ . '/leaveFunction.php';

use First1\V1\Notify\Slack;
use First1\V1\Staff\LeaveHourCount;

$slack = Slack::getInstance(
    $env['slack']['token'],
    $env['slack']['channel_token']
);
$channel = 'C08RWMRAVRC'; // Slack channel ID for SFTP errors

$staffId = $_POST['member_id'];
if (empty($staffId) || ($staffId != $_SESSION['member_id'])) {
    breakOut('無法取得員工資料');
}

$leaveId = $_POST['leaveId'];
if (empty($leaveId)) {
    breakOut('無法取得請假資料');
}

$dateFrom     = $_POST['date-from'];
$dateTo       = $_POST['date-to'];
$timeFrom     = $_POST['time-from'];
$timeTo       = $_POST['time-to'];
$dateAll      = $_POST['date-all'];
$sApplyReason = $_POST['apply-reason'];

if ($dateAll == 'A') {
    $timeFrom = '09:00:00';
    $timeTo   = '18:00:00';
}

if ($leaveId == '20') {
    $timeFrom = '09:00:00';
    $timeTo   = '10:00:00';
}

if (empty($dateFrom) || empty($dateTo) || empty($timeFrom) || empty($timeTo) || empty($dateAll) || ! in_array($dateAll, ['A', 'S'])) {
    breakOut('請填寫完整請假時間');
}

$leaveDateFrom = $dateFrom . ' ' . $timeFrom;
$leaveDateTo   = $dateTo . ' ' . $timeTo;
$timestampFrom = strtotime($leaveDateFrom);
$timestampTo   = strtotime($leaveDateTo);

$agent = empty($_POST['agent']) ? '' : $_POST['agent'];

$sLeaveAttachment = null;
if (in_array($leaveId, $needAttachmentLeave)) {
    if ($_FILES['leaveAttachment']['error'] !== 0) {
        breakOut('請提供上傳附件');
    }

    $extension = pathinfo($_FILES['leaveAttachment']['name'], PATHINFO_EXTENSION);
    if (! in_array($extension, ['jpg', 'jpeg', 'png'])) {
        breakOut('附件格式錯誤');
    }

    $uploadPath = dirname(dirname(dirname(__DIR__))) . '/uploads/leaveApply';
    if (! is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    $sLeaveAttachment = $staffId . '_' . $leaveId . '_' . $timestampFrom . '.' . $extension;

    $file = $_FILES['leaveAttachment']['tmp_name'];
    $dest = $uploadPath . '/' . $sLeaveAttachment;

    // 將檔案移至指定位置
    move_uploaded_file($file, $dest);

    //同步請假附件檔案
    $cmd = '/usr/bin/php ' . __DIR__ . '/uploadLeaveAttachmentBySFTP.php ' . base64_encode($dest);
    shell_exec($cmd);

    $message = "shell command: {$cmd}";
    $slack->chatPostMessage($message, $channel);
}

$totalHours = ($leaveId == '20') ? 1 : LeaveHourCount::getLeaveHours(new DateTime($leaveDateFrom), new DateTime($leaveDateTo), $dateAll);

$notify_to = empty($agent) ? 'U' : 'A';

$unitApproval = getSupervisor($staffId);
if ($unitApproval == $staffId) { //主管是自己
    $unitApproval = MANAGER;         //20241216 家津請示過雄哥後，主管請假直接由總經理簽核
}

$managerApproval = ($unitApproval == MANAGER) ? null : MANAGER;

$conn = new first1DB;
$sql  = 'INSERT INTO
            tStaffLeaveApply
            (
                sApplicant,
                sLeaveId,
                sLeaveFromDateTime,
                sLeaveToDateTime,
                sLeaveFromTmestamp,
                sLeaveToTimestamp,
                sTotalHoursOfLeave,
                sApplyReason,
                sLeaveAttachment,
                sAgentApproval,
                sUnitApproval,
                sManagerApproval,
                sProcessing,
                sStatus,
                sCreatedAt
            ) VALUES (
                :sApplicant,
                :sLeaveId,
                :sLeaveFromDateTime,
                :sLeaveToDateTime,
                :sLeaveFromTmestamp,
                :sLeaveToTimestamp,
                :sTotalHoursOfLeave,
                :sApplyReason,
                :sLeaveAttachment,
                :sAgentApproval,
                :sUnitApproval,
                :sManagerApproval,
                :processing,
                :status,
                :createdAt
            );';

$bind = [
    'sApplicant'         => $staffId,
    'sLeaveId'           => $leaveId,
    'sLeaveFromDateTime' => $leaveDateFrom,
    'sLeaveToDateTime'   => $leaveDateTo,
    'sLeaveFromTmestamp' => $timestampFrom,
    'sLeaveToTimestamp'  => $timestampTo,
    'sTotalHoursOfLeave' => $totalHours,
    'sApplyReason'       => $sApplyReason,
    'sLeaveAttachment'   => empty($sLeaveAttachment) ? null : $sLeaveAttachment,
    'sAgentApproval'     => empty($agent) ? null : $agent,
    'sUnitApproval'      => $unitApproval,
    'sManagerApproval'   => $managerApproval,
    'processing'         => $notify_to,
    'status'             => "N",
    'createdAt'          => date('Y-m-d H:i:s'),
];

if ($conn->exeSql($sql, $bind)) {
    $insertId  = $conn->lastInsertId();
    $leaveData = getLeaveData($insertId);
    $leaveData = array_merge($leaveData, ['apply' => $_SESSION['member_id']]);

    if (in_array($notify_to, ['A', 'U', 'M'])) {
        if ($notify_to == 'A') {
            $response = sendAgentApply($leaveData, $insertId, true);
        }

        if ($notify_to == 'U') {
            $response = sendUnitApply($leaveData, $insertId, true);
        }

        if ($notify_to == 'M') {
            $response = sendManagerApply($leaveData, $insertId, true);
        }

        breakOut('已送出申請');
    }

    breakOut('無法確認簽核人員');
}

breakOut('無法送出申請');
