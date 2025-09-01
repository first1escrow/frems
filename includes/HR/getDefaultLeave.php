<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/staff.class.php';

$staff = $_POST['staff'];
if (empty($staff) || ! is_numeric($staff)) {
    http_response_code(400);
    exit('Invalid staff');
}

$conn = new First1DB;

$sql = 'SELECT
            sLeaveId,
            (SELECT CASE WHEN sMemo IS NULL OR sMemo = "" THEN sLeaveName ELSE sMemo END AS leaveName FROM tStaffLeaveType WHERE sId = a.sLeaveId) AS sLeaveName,
            sLeaveDefault,
            sLeaveBalance,
            sLeaveRemark
        FROM
            tStaffLeaveDefault AS a
        WHERE
            sStaffId = :id';
$data = $conn->all($sql, ['id' => $staff]);

header('Content-Type: application/json');
echo json_encode($data);
exit;
