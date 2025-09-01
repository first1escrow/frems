<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$date = empty($_POST['date']) ? '' : $_POST['date'];

if (empty($date) || ! preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $date)) {
    http_response_code(400);
    exit('日期格式錯誤');
}

$conn = new first1DB;
$sql  = 'UPDATE tStaffLockDate SET sDate = :date, sModifierId = :modifier, sModifierName = :modifierName WHERE sId = 1;';
echo $conn->exeSql($sql, ['date' => $date, 'modifier' => $_SESSION['member_id'], 'modifierName' => $_SESSION['member_name']]) ? '已鎖定' : '鎖定失敗';
exit;
