<?php
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';

$staffId = empty($_GET['staffId']) ? null : $_GET['staffId'];
$leaveId = empty($_GET['leaveId']) ? null : $_GET['leaveId'];

if (empty($staffId) || empty($leaveId || ! is_numeric($staffId) || ! is_numeric($leaveId))) {
    exit('<center><h3>Invalid parameters</h3></center>');
}

$conn = new first1DB();

//員工姓名基本資料
$sql   = 'SELECT a.pId, a.pName, a.pDep, pOnBoard, b.dDep, b.dTitle FROM tPeopleInfo AS a JOIN tDepartment AS b ON a.pDep = b.dId WHERE a.pId = :staffId;';
$staff = $conn->one($sql, ['staffId' => $staffId]);

//員工假別時數歷史紀錄
$sql = 'SELECT
            a.sStaffId,
            a.sDate,
            a.sLeaveId,
            a.sLeaveDefault ,
            a.sLeaveBalance,
            (CASE WHEN b.sMemo IS NULL THEN b.sLeaveName ELSE b.sMemo END) as leaveName
        FROM
            tStaffLeaveDefaultHistory AS a
        JOIN
            tStaffLeaveType AS b ON a.sLeaveId = b.sId
        WHERE
            a.sStaffId = :staffId
            AND a.sLeaveId = :leaveId
        ORDER BY
            a.sDate DESC;';
$list = $conn->all($sql, ['staffId' => $staffId, 'leaveId' => $leaveId]);

$smarty->assign('staff', $staff);
$smarty->assign('list', $list);

$smarty->display('staffDefaultLeaveHistoryDetail.inc.tpl', '', 'HR');
