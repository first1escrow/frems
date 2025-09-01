<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/includes/staff/leaveConfig.php';

$id = $_GET['id'];
if (empty($id) || !is_numeric($id)) {
    exit('Invalid access'); //非法存取
}

$conn = new first1DB;

$case = [];

$sql = 'SELECT
            sApplicant,
            (SELECT pName FROM tPeopleInfo WHERE pId = a.sApplicant) as staffName,
            sOvertimeType,
            CASE WHEN sOvertimeType = "W" THEN "平日加班" WHEN sOvertimeType = "H" THEN "假日加班" ELSE "-" END AS sOvertimeTypeName,
            sOvertimeFromDateTime,
            sOvertimeToDateTime,
            sTotalHoursOfOvertime,
            sApplyReason,
            sUnitApproval,
            (SELECT pName FROM tPeopleInfo WHERE pId = a.sUnitApproval) as supervisor,
            sUnitApprovalDateTime,
            sProcessing,
            sStatus
        FROM
            tStaffOvertimeApply AS a
        WHERE
            sId = ' . $id . ';';
$rs = $conn->one($sql);

if (!empty($rs)) {
    $rs['sOvertimeFromDateTime'] = date('Y-m-d H:i', strtotime($rs['sOvertimeFromDateTime']));
    $rs['sOvertimeToDateTime']   = date('Y-m-d H:i', strtotime($rs['sOvertimeToDateTime']));
    $rs['supervisor']            = empty($rs['supervisor']) ? '無' : $rs['supervisor'];
    $rs['sUnitApprovalDateTime'] = empty($rs['sUnitApprovalDateTime']) ? '-' : substr($rs['sUnitApprovalDateTime'], 0, -3);
}

$smarty->assign('case', $rs);

$smarty->display('overtimeList.inc.tpl', '', 'staff');