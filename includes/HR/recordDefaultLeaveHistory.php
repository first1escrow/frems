<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$_conn = new First1DB;

$values = [];
foreach ($leaveData['sLeaveId'] as $key => $value) {
    $leaveData['sLeaveDefault'][$key] = empty($leaveData['sLeaveDefault'][$key]) ? 0 : $leaveData['sLeaveDefault'][$key];
    $leaveData['sLeaveBalance'][$key] = empty($leaveData['sLeaveBalance'][$key]) ? 0 : $leaveData['sLeaveBalance'][$key];

    $values[] = '(' . $leaveData['sStaffId'] . ',"' . date('Y-m-d') . '", ' . $value . ', ' . $leaveData['sLeaveDefault'][$key] . ', ' . $leaveData['sLeaveBalance'][$key] . ', NOW())';
}

$sql = 'INSERT INTO
            tStaffLeaveDefaultHistory
        (
            sStaffId,
            sDate,
            sLeaveId,
            sLeaveDefault,
            sLeaveBalance,
            sCreatedAt
        ) VALUES ' . implode(', ', $values) . '
                ON DUPLICATE KEY UPDATE
                    sLeaveDefault = VALUES(sLeaveDefault),
                    sLeaveBalance = VALUES(sLeaveBalance);';
$_conn->exeSql($sql);

$_conn = $values = $leaveData = null;
